<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\PaymentDetail;

class LoanController extends Controller
{
    private function checkLoanExistence( $id )
        {
            if ( ! empty( $id ) )
                {
                    $existence_result = Loan::find( $id );

                    if ( $existence_result )
                        {
                            return $existence_result ;
                        }
                    else
                        {
                            $this->finalResponse( 'error', 'Unable to find loan records!', 'Are you sure you have selected correct loan for Enquiry?' );
                        }
                }
            else
                {
                    $this->finalResponse();
                }
        }

    private function calculateEMIAmount( $amount, $term )
        {
            if ( ! empty( $amount ) && ! empty( $term ) )
                {
                    return ceil( $amount / $term );
                }
            else
                {
                    $this->finalResponse();
                }
        }

    function applyforLoan( Request $request )
        {
            if ( ! $this->doesUserExist( $request->email, $request->secret ) )
                {
                    return;
                }

            $loans = new Loan();

            // To store data in DB Syntax ( $modal_object->db_columns_name = $request->field_name )
            $loans->status = 0;
            $loans->user_id = 1;
            $loans->amount = $request->amount;
            $loans->term = $request->term;

            try
                {
                    $result = $loans->save();

                    if ( $result )
                        {
                            return [ $this->finalResponse( 'success', 'Loan applicaton successful!', 'Your loan has been sent for approval, you will receive a confirmation post approval.' ) ];
                        }
                    else
                        {
                            return [ $this->finalResponse( 'error', 'Unable to apply for loan!', '' ) ];
                        }
                }
            catch ( Exception $e )
                {
                    $this->finalResponse();
                }
        }

    function approveLoan( Request $request )
        {
            if ( $this->doesUserExist( $request->email, $request->secret ) )
                {
                    $response_result = $this->checkLoanExistence( $request->loan_id );

                    $paymentDetail = new PaymentDetail();

                    $paymentDetail->status = 1;
                    $paymentDetail->loan_id = $request->loan_id;
                    $paymentDetail->last_repayment_date = NULL;
                    $paymentDetail->next_repayment_date = now()->addDays(7);
                    $paymentDetail->installment_amount = $this->calculateEMIAmount( $response_result->amount, $response_result->term );
                    $paymentDetail->pending_amount = $response_result->amount;

                    $result = $paymentDetail->save();

                    if ( $result )
                        {
                            $loan_result = Loan::where( [ 'id' => $request->loan_id ] )->update( [ 'status' => 1 ] );

                            if ( $loan_result )
                                {
                                    return [ $this->finalResponse( 'success', 'Loan applicaton approved!', 'User can start paying EMI now. You can set an email ( Using mailgun or any other ) / SMS notification ( If DLT Registration done ) to inform the user for further process.' ) ];
                                }
                            else
                                {
                                    $this->finalResponse();
                                }
                        }
                    else
                        {
                            return [ $this->finalResponse( 'error', 'Loan approval unsuccessful!', '' ) ];
                        }
                }
        }

    function payLoanEMI( Request $request )
        {
            if ( $this->doesUserExist( $request->email, $request->secret ) )
                {
                    $meta_result = PaymentDetail::find( $request->payment_details_id );

                    if ( $meta_result )
                        {
                            if ( $meta_result->pending_amount <= 0 )
                                {
                                    $this->finalResponse( 'success', 'EMIs Completed!', 'All EMIs were paid were you, no EMIs pending' );
                                }

                            if ( $request->amount < $meta_result->installment_amount )
                                {
                                    $this->finalResponse( 'error', 'EMI amount mismatched!', 'You have to pay min ' . $meta_result->installment_amount . ' every EMI cycle.' );
                                }

                            if ( $request->amount > $meta_result->installment_amount )
                                {
                                    $this->finalResponse( 'error', 'EMI amount mismatched!', 'You can only pay max ' . $meta_result->installment_amount . ' every EMI cycle.' );
                                }

                            $payment = new Payment();

                            $payment->status = 1;
                            $payment->payment_details_id = $request->payment_details_id;
                            $payment->amount_received = $request->amount;

                            $result = $payment->save();

                            if ( $result )
                                {
                                    $pending_amount = ( $meta_result->pending_amount - $meta_result->installment_amount );
                                    $prev_payment_date = now()->addDays(7);
                                    $create_date = new \Carbon\Carbon($prev_payment_date);
                                    $next_payment_date = $create_date->addDays(7);

                                    $details_update_result = PaymentDetail::where( [ 'id' => $request->payment_details_id ] )->update( [ 'status' => 1, 'last_repayment_date' => $prev_payment_date, 'next_repayment_date' => $next_payment_date, 'pending_amount' => $pending_amount ] );

                                    if ( $details_update_result )
                                        {
                                            return [ $this->finalResponse( 'success', 'EMI Paid successfully!', 'Next EMI Payment on or before ' . $next_payment_date ) ];
                                        }
                                    else
                                        {
                                            $this->finalResponse();
                                        }
                                }
                            else
                                {
                                    return [ $this->finalResponse( 'error', 'Unable to Pay EMI!', '' ) ];
                                }
                        }
                    else
                        {
                            $this->finalResponse( 'error', 'Unable to load payment infromation!', 'Are you sure, you are making a correct request?' );
                        }
                }
        }
}

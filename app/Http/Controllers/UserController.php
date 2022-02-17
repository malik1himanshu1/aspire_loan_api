<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UserController extends Controller
{
    function createUser( Request $request )
        {
            $validated = $request->validate( [ 'email' =>'required|unique:users' ] );

            if ( ! empty( $validated ) )
                {
                    // create modal object and use it to save data into DB 
                    $user = new User();

                    $user->status = 1;
                    $user->email = $request->email;
                    $user->name = $request->name;
                    $user->password = bcrypt( $request->password );
                    $user->secret_key = hash_hmac( 'sha256' , '2<1CglY[1Sm\2/MyN}e)"n,Y91K_7',  bin2hex( random_bytes( 32 ) ) );

                    $result = $user->save();

                    if ( $result )
                        {
                            return [ $this->finalResponse( 'success', 'User created!', 'Pleae login to continue.' ) ];
                        }
                    else
                        {
                            return [ $this->finalResponse( 'error', '', '' ) ];
                        }
                }
            else
                {
                    return [ $this->finalResponse( 'info', 'User already exists!', 'Please choose a different email.' ) ];
                }
        }

    function checkUser( Request $request )
        {
            if ( Auth::attempt( [ 'email'=>$request->email,'password'=>$request->password ] ) )
                {
                    return [ $this->finalResponse( 'success', 'User authenticated!', 'Welcome to loan application.', [ 'secret' => Auth::User()->secret_key ] ) ];
                }
            else
                {
                    return [ $this->finalResponse( 'error', 'Unable to login!', 'Please enter correct credentials.' ) ];
                }
        }
}

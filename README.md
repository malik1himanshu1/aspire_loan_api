### **Laravel API Loan App**

**Version** 7.*

This app is a API app that will give you access to a loan approval and payment .

### ** Installation**
1. Run `composer install`


### **Database setup**

Please open your localhost phpmyadmin / adminer etc.

Navigate to folder 'Sql Schema' in root folder and you will find a file named `aspire_loan.sql`. Import that SQL file in you DBs list if you dont know about the migration of tables in laravel. 

### **Request Format and type**

If you are using POSTMAN. Please follow the below process

1. Set request type to `POST`
2. Set your base request URL
3. Select body and then select x-www-form-urlencoded
4. Below are list of APIs with required fields

### **List of APIs**

Note: My local server was http://localhost:8000 You may change the settings as per you laravel environment

1. http://localhost:8000/api/register ( Pre Login )
2. http://localhost:8000/api/login ( Pre Login )
3. http://localhost:8000/api/loan/apply ( Post Login )
4. http://localhost:8000/api/loan/approve ( Post Login )
5. http://localhost:8000/api/loan/emi/pay ( Post Login )

### **Application Flow**

### **Note: You will receive JSON responses to every API request**

1. You are a new user so visit `/api/register` to register first. Fields required for this request are `name`,`email` & `password` ( password will be encrypted so kindly memorize it )

    Expected Response: `{"status":"success","heading":"User created!","message":"Pleae login to continue."}`

2. You need to now login so visit `/api/login`. Fields required for this request are `email` & `password`

    Expect Response: `{"status":"success","heading":"User authenticated!","message":"Welcome to loan application.","data":{"secret":"8761246a220fff678b5bff65c47d6d0c5f0e4e88b4cc9fde550dfa4d5591b1cb"}}`

    Note: secret and email, you will need this for every API request

3. You may apply for a loan by visiting `/api/loan/apply` Fields required for this request are `email`, `secret`, `amount` & `term`

    Expected Response: `{"status":"success","heading":"Loan applicaton successful!","message":"Your loan has been sent for approval, you will receive a confirmation post approval."}`

4. If you want to approve your loan you may head to `/api/loan/approve` Fields required for this request are `email`, `secret`, & `loan_id`

    Note: for loan_id ( You may take a look at `loans` and search for your relevant loan ID )

    Expected Response: `{"status":"success","heading":"Loan applicaton approved!","message":"User can start paying EMI now."}`

5. Now you need to pay EMIs you may visit `/api/loan/emi/pay` Fields required for this request are `email`, `secret`, `payment_details_id` & `amount`

    Note: for meta_id ( You may take a look at `payment_details` and search for relevant detail ID  )

    Expect Response ( For every valid EMI Payment ): `{"status":"success","heading":"EMI Paid successfully!","message":"Next EMI Payment on <your_responsed_date>"}`
    Expected Response ( When all the EMIs are completed ): `{"status":"success","heading":"EMIs Completed!","message":"All EMIs paid, no EMIs pending"}`
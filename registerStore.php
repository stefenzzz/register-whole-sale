<?php
add_action('wp_ajax_nopriv_submitStoreInfo','register');
add_action('wp_ajax_submitStoreInfo','register');




// register new user
function register(){


	//check ajax nonce
	if(check_ajax_referer('registerStoreInfo','nonce')){
	
		
		// get all data submitted and sanitized
		try{
          
			$data = [
				'firstname' =>  sanitize_text_field($_POST['firstname']),
				'lastname' =>  sanitize_text_field($_POST['lastname']),
				'email' => sanitize_text_field($_POST['email']),
				'typeOfCompany' =>  sanitize_text_field($_POST['typeOfCompany']),
				'shippingAddress' =>  sanitize_text_field($_POST['shippingAddress']),
				'billingAddress' =>  sanitize_text_field($_POST['billingAddress']),
				'stateSalesTax' =>  sanitize_text_field($_POST['stateSalesTax']),
				'federalEinOrTin' =>  sanitize_text_field($_POST['federalEinOrTin']),
				'companyWebsite' =>  sanitize_text_field($_POST['companyWebsite']),
				'typesOfProduct' =>  sanitize_text_field($_POST['typesOfProduct']),
				'password' =>  sanitize_text_field($_POST['password']),
				'retypePassword' =>  sanitize_text_field($_POST['retypePassword']),
				'optIntoEmailList' =>  sanitize_text_field($_POST['optIntoEmailList'])
			];
		


	
			//function if data empty send error and die
			function checkEmptySendJsonError($value,$key,$errorMessage){
				if(empty($value)){
				  $errors =[];
				  $errors[$key] = $key .$errorMessage;
				  wp_send_json([
					'error' =>  [
								'errors' => $errors
								]
							]);
				}
				
			}

			// (?=.*[a-z])  use positive look ahead to see if at least one lower case letter exists
			// (?=.*[A-Z])   use positive look ahead to see if at least one upper case letter exists
			// (?=.*\d)      use positive look ahead to see if at least one digit exists
			// (?=.*\W)      use positive look ahead to see if at least one non-word character exists

			$regex = "/^(?=.*[A-Z])(?=.*\W).{8,}$/i";



			//validate if required field are empty
			 foreach($data as $key => $d)
			 {
				//check first name if empty and not symbols
				if($key === 'firstname'){
					checkEmptySendJsonError($d,$key,' should not be empty');
					if(!preg_match("/^([a-zA-Z' ]+)$/",$d)){
						$errors = [];
						$errors['firstname'] = 'Only alphabet';
						wp_send_json([
							'error' =>  [
										'errors' => $errors
										]
									]);
					}
				}

				//check first name if empty and not symbols
				if($key === 'lastname'){
					checkEmptySendJsonError($d,$key,' should not be empty');
					if(!preg_match("/^([a-zA-Z' ]+)$/",$d)){
						$errors = [];
						$errors['lastname'] = 'Only alphabet';
						wp_send_json([
							'error' =>  [
										'errors' => $errors
										]
									]);
					}
				}
				//check email if empty
				if($key === 'email'){
					checkEmptySendJsonError($d,$key,' should not be empty');
					
				}
				// check password if empty and dont have required characters
				if($key === 'password'){
					checkEmptySendJsonError($d,$key,' should not be empty');
					if(!preg_match($regex, $d)) {
						$errors = [];
						$errors['password'] = 'atleast 8 characters, 1 uppercase and 1 special character';
						wp_send_json([
							'error' =>  [
										'errors' => $errors
										]
									]);
					}
				}
				// check password if empty and dont have required characters
				if($key === 'retypePassword'){
					checkEmptySendJsonError($d,$key,' should not be empty');
					if(!preg_match($regex, $d)) {
						$errors = [];
						$errors['retypePassword'] = 'atleast 8 characters, 1 uppercase and 1 special character';
						wp_send_json([
							'error' =>  [
										'errors' => $errors
										]
									]);
					}
				}
			 }
			//  validate email
			 if(!is_email($data['email'])){
				$errors = [];
				$errors['email'] = 'Invalid Email';
				wp_send_json([
					'error' =>  [
								'errors' => $errors
								]
							]);
			 }
			//  validate email
			 if(email_exists($data['email'])){
				$errors = [];
				$errors['email'] = 'Email already used';
				wp_send_json([
					'error' =>  [
								'errors' => $errors
								]
							]);
			 }
			//  validate if password mismatch
			 if($data['password'] !== $data['retypePassword']){
				
				$errors = [];
				$errors['password_mismatch'] = 'password_mismatch';
				wp_send_json([
					'error' =>  [
								'errors' => $errors
								]
							]);
				
			 }
			// add email to mail poet if isn't added
				if (class_exists(\MailPoet\API\API::class)) {

					try{

						$mailpoet_api = \MailPoet\API\API::MP('v1');
						$lists = $mailpoet_api->getLists();
		
								$subscirber_data = [
									'first_name' => $data['firstname'],
									'last_name' => $data['lastname'],
									'email' => $data['email'],
									'cf_2' => 'test_field'
								];
								$mailpoet_api->addSubscriber($subscirber_data,[4]);
						}catch(\Exception $e){
							$mailpoet_error = [
								'message' => $e->getMessage(),
								'code' => $e->getCode(),
							];
						}
						
					}	
			
				$r = new WP_Roles();
			
				if($r->is_role('wholesale')){
					$default_role = 'wholesale';
				}elseif($r->is_role('customer')){
					$default_role = 'customer';
				}else{
					$default_role = 'subscriber';
				}
		
			// register user to wordpress users
				$user_data = [
								'user_login' => $data['email'],
								'user_pass' => $data['password'],
								'first_name' => $data['firstname'],
								'last_name' => $data['lastname'],
								'role' => $default_role,
								'user_email' => $data['email'],
							 ];
			//  return the registered user id
				$userID = wp_insert_user( $user_data );
			
	
			if($userID instanceof WP_Error){
				wp_send_json(['error' => $userID]);
			}
			// update all fields, if fields didn`t exist it will add new fields
			update_user_meta($userID,'type_of_company',$data['typeOfCompany'],true);
			update_user_meta($userID,'shipping_address',$data['shippingAddress'],true);
			update_user_meta($userID,'billing_address',$data['billingAddress'],true);
			update_user_meta($userID,'state_sales_tax',$data['stateSalesTax'],true);
			update_user_meta($userID,'federal_ein_or_tin',$data['federalEinOrTin'],true);
			update_user_meta($userID,'company_website',$data['companyWebsite'],true);
			update_user_meta($userID,'types_of_product',$data['typesOfProduct'],true);
			update_user_meta($userID,'password',$data['password'],true);
			update_user_meta($userID,'opt_in_email_list',$data['optIntoEmailList'],true);
			
			

 
			// send json if success and die
			wp_send_json([
				'success' =>[
					'user_id' => $userID,
					'maile_poet' => $mailpoet_error ?? 'registered in mailpoet'
				]
			]);
	
		}catch(\Exception $e){
			// if user already subscribed in mailpoet
			if($e->getCode() === 12){
				wp_send_json([

						'mail_poet' => [
							'error message' => $e->getMessage(),
							'code' => $e->getCode(),
							'wp_user_id' => $userID ?? 'no user id']
					
					]);
			}
			// if error found send message
			wp_send_json([
				'general_error' => $e->getMessage(),
				'code' =>$e->getCode(),
				'user' => $userID ?? 'not registered'
			]);
		}
			
	}
	
	exit;
}



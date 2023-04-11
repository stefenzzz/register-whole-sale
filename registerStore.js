(function($){
	
$(function() {





	var firstname = document.querySelector('#firstname');
	var lastname = document.querySelector('#lastname');
	var email = document.querySelector('#email');
	var typeOfCompany = document.querySelector('#typeOfCompany');
	var shippingAddress = document.querySelector('#shippingAddress');
	var billingAddress = document.querySelector('#billingAddress');
	var stateSalesTax = document.querySelector('#stateSalesTax');
	var federalEinOrTin = document.querySelector('#federalEinOrTin');
	var companyWebsite = document.querySelector('#companyWebsite');
	var typesOfProduct = document.querySelector('#typesOfProduct');
	var password = document.querySelector('#password');
	var retypePassword = document.querySelector('#retypePassword');
	var optIntoEmailList = document.querySelector('#optIntoEmailList');
	var submitStoreInfo = document.querySelector('#submitStoreInfo');
	var formWrapper = document.querySelector('.form-wrapper');

	//check if element and exit if it didnt exist
	if(!submitStoreInfo){
		return;
	}
	submitStoreInfo.addEventListener('click',function(e){
		e.preventDefault();

		var field_errors = [];

		$('.form-wrapper > div > div > span').text('').html('');

		if(!firstname.value){
			field_errors['#firstname'] = 'First name is required';
		}
		if(!lastname.value){
			field_errors['#lastname'] = 'Last name is required';
		}
		if(!email.value){
			field_errors['#email'] = 'Email is required';
		}
		if(password.value != retypePassword.value){
			field_errors['#password'] = 'Password mismatch';
			field_errors['#retypePassword'] = 'Password mismatch';
		}
		if(!password.value.match(/^(?=.*[A-Z])(?=.*\W).{8,}$/))
		{
			field_errors['#password'] = 'At least 8 characters, 1 uppercase and 1 special character';
		}
		if(!retypePassword.value.match(/^(?=.*[A-Z])(?=.*\W).{8,}$/))
		{
			field_errors['#retypePassword'] = 'At least 8 characters, 1 uppercase and 1 special character';
		}
		

		if(Object.keys(field_errors).length !== 0){
			for (const [key,value] of Object.entries(field_errors)){
				$(key).next('span').text(value).css({'color':'red'});
			}
			return;
		}
		

		$('.form-wrapper').css({'opacity':'.5'});
		submitStoreInfo.disabled = true;
		$.post(
				   np.ajaxURL, {
					   'action': 'submitStoreInfo',
					   'nonce': np.nonce,
					   'firstname' : firstname.value,
						'lastname' : lastname.value,
						'email' : email.value,
						'typeOfCompany' : typeOfCompany.value,
						'shippingAddress' : shippingAddress.value,
						'billingAddress' : billingAddress.value,
						'stateSalesTax' : stateSalesTax.value,
						'federalEinOrTin' : federalEinOrTin.value,
						'companyWebsite' : companyWebsite.value,
						'typesOfProduct' : typesOfProduct.value,
						'password' : password.value,
						'retypePassword' : retypePassword.value,
						'optIntoEmailList' : optIntoEmailList.checked
				   },
				   function( response ) {
// 					   firstname.value = '';
// 					   lastname.value = '';
// 					   email.value = '';
// 					   typeOfCompany.value = '';
// 					   shippingAddress.value = '';
// 					   billingAddress.value = '';
// 					   stateSalesTax.value = '';
// 					   federalEinOrTin.value = '';
// 					   companyWebsite.value = '';
// 					   typesOfProduct.value = '';
// 					   password.value = '';
// 					   retypePassword.value = '';
// 					   optIntoEmailList.checked = false;


				$('.form-wrapper').css({'opacity':'1'});

						// check if response error is not empty

						if(response.error){

						

							if(response.error.errors.hasOwnProperty('existing_user_email'))
								{
									$('#email').next("span").text(response.error.errors.existing_user_email[0]).css({'color':'red'});
								}
							if(response.error.errors.hasOwnProperty('existing_user_login'))
								{
									$('#email').next("span").text(response.error.errors.existing_user_login[0]).css({'color':'red'});
								}
							if(response.error.errors.hasOwnProperty('empty_user_login'))
								{
									$('#email').next("span").text(response.error.errors.empty_user_login[0]).css({'color':'red'});
								}

							// check object key is more than 0
							if(Object.keys(response.error).length !== 0)
							{
							
								//alert if required text fields are blank
								$('#'+Object.keys(response.error.errors)[0]).next('span').text(response.error.errors[Object.keys(response.error.errors)[0]]).css({'color':'red'});


							
								// check object key
								if(Object.keys(response.error.errors)[0] == 'password_mismatch')
								{
									// alert if password are mismatch
									$('#password').next('span').text('password mismatch').css({'color':'red'});
									$('#retypePassword').next('span').text('password mismatch').css({'color':'red'});
								}
								//
								if(Object.keys(response.error.errors)[0] == 'invalid_email')
								{
									// alert email is invalid
									$('#password').next('span').text('password mismatch').css({'color':'red'});
									$('#retypePassword').next('span').text('password mismatch').css({'color':'red'});
								}
							}

							
						}
						if(response.success){
							$('.form-wrapper').html('<p>Thank you for applying for membership to our site. We will review your details and send you an email letting you know whether your application has been successful or not.<p>');
							
							
						}
						console.log(response);
					   submitStoreInfo.disabled = false;
						
				   }
			   ).fail(function(e) {
				formWrapper.innerHTML ='<p>Thank you for applying for membership to our site. We will review your details and send you an email letting you know whether your application has been successful or not.</p>'
			  });

		
		
	});
	

});

})(jQuery);	
	


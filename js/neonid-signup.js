function neon_id_onload(){

}

function neonid_partner_signup_submit(){

	var form = document.getElementsByName('partner-signup')[0]; 
	var partner_name = document.getElementById('join-name').value;
	var company_name = document.getElementById('join-company').value;
	var company_email = document.getElementById('company-mail-address').value;

 	/* do email validation */

	var isemail = validateEmail(company_email);
	const resp = form.querySelector('#business-response');
	
	if( isemail ){
	 	let loadingimg = neonid_signup.load_img;
	 	resp.innerHTML = '<img src="'+loadingimg+'" />';
	 	var request = new XMLHttpRequest();  

	 	request.open('POST', neonid_signup.ajax_url + '?action=neon_partner_early_signup', true);
	 	//AJAX dataType header
	 	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		request.responseType = 'json';
	 	var params = '&email=' + encodeURI(company_email) 
	 						   + '&dest_email='+encodeURI(dest_email)
	 						   + '&partner_name='+encodeURI(partner_name)
	 						   + '&company_name='+encodeURI(company_name);

		request.onload = function(){
		 	resp.innerHTML = '';
		 	if(request.response.type == "success"){
		 	  resp.innerHTML = request.response.message;
		 	} else {
		 	  if(neonid_signup.debug == '1'){
			 	  if(typeof request.response.message !== 'undefined'){
			 	  	    resp.innerHTML = request.response.message;
			 	  		console.log( request.response.message);
			 	  	} else { resp.innerHTML = '<p class="error"There was a problem adding your email.</p>'; console.log( 'No AJAX response from neonid_signup.')}
			 	}
		 	} 	
		}
		request.ontimeout = () => {
			if(neonid_signup.debug == '1') console.log('Request from neonid_signup timed out.')
		}

		request.send( params );

	} else {
	 	resp.innerHTML = '<p class="error">Please enter a valid email address.</p>';
	 	console.log( resp.innerHTML );
	}
}

function neonid_signup_submit(){

	var modal = document.getElementsByClassName('neon-modal')[0]; //Get visible modal, this prevents issues with cloned element IDs
 	/* do email validation */
	var email = modal.getElementsByClassName('email-field')[0].value; //on form submit this should be filled
	var dest_email = modal.querySelector('#dest_email').value; //email to return to 

	var isemail = validateEmail(email);
	const resp = modal.querySelector('#neonid-signup-response');
	
	if( isemail ){
	 	let loadingimg = neonid_signup.load_img;
	 	resp.innerHTML = '<img src="'+loadingimg+'" />';
	 	var request = new XMLHttpRequest();  

	 	request.open('POST', neonid_signup.ajax_url + '?action=neon_early_signup', true);
	 	//AJAX dataType header
	 	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		request.responseType = 'json';
	 	var params = '&email=' + encodeURI(email) + '&dest_email='+encodeURI(dest_email);

		request.onload = function(){
		 	resp.innerHTML = '';
		 	if(request.response.type == "success"){
		 	  resp.innerHTML = request.response.message;
		 	} else {
		 	  if(neonid_signup.debug == '1'){
			 	  if(typeof request.response.message !== 'undefined'){
			 	  	    resp.innerHTML = request.response.message;
			 	  		console.log( request.response.message);
			 	  	} else { resp.innerHTML = '<p class="error"There was a problem adding your email.</p>'; console.log( 'No AJAX response from neonid_signup.')}
			 	}
		 	} 	
		}
		request.ontimeout = () => {
			if(neonid_signup.debug == '1') console.log('Request from neonid_signup timed out.')
		}

		request.send( params );

	} else {
	 	resp.innerHTML = '<p class="error">Please enter a valid email address.</p>';
	 	console.log( resp.innerHTML );
	}
}


function validateEmail(email) {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
}
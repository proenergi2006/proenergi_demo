$(function() {

  // load BotDetect Captcha, it requires you to configure 
  // BotDetect Java Captcha path to captchaEndpoint setting
  var captcha = $('#botdetect-captcha').captcha({
    captchaEndpoint: 'captcha-endpoint/simple-botdetect.php'
  });
  
  // error messages of input fields
  var errorMessages = {
    name: 'Name must be at least 3 characters.',
    email: 'Email is invalid.',
    subject: 'Subject must be at least 10 characters.',
    message: 'Message must be at least 10 characters.',
    captchaCode: 'Invalid code.'
  };
  
  // global variables that holds validation status of captcha input field, 
  // use them for checking validation status when form is submitted
  var isNameValid = false,
      isEmailValid = false,
      isSubjectValid = false,
      isMessageValid = false;
  
  
  function validateName() {
    var name = $('#name').val();
    isNameValid = (name.length >= 3);
    if (isNameValid) {
      $('.name').text('');
    } else {
      $('.name').text(errorMessages.name);
    }
  }
  
  function validateEmail() {
    var email = $('#email').val();
    var emailRegEx = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    isEmailValid = emailRegEx.test(email);
    if (isEmailValid) {
      $('.email').text('');
    } else {
      $('.email').text(errorMessages.email);
    }
  }
  
  function validateSubject() {
    var subject = $('#subject').val();
    isSubjectValid = (subject.length >= 10);
    if (isSubjectValid) {
      $('.subject').text('');
    } else {
      $('.subject').text(errorMessages.subject);
    }
  }
  
  function validateMessage() {
    var message = $('#message').val();
    isMessageValid = (message.length >= 10);
    if (isMessageValid) {
      $('.message').text('');
    } else {
      $('.message').text(errorMessages.message);
    }
  }
  
  // validate input fields on blur event
  $('#name').blur(validateName);
  $('#email').blur(validateEmail);
  $('#subject').blur(validateSubject);
  $('#message').blur(validateMessage);
  
  // UI captcha validation on blur event by using the custom 'validatecaptcha' event
  // and checking the 'isCorrect' variable to either show error messages 
  // or check captcha code input field status when form is submitted
  $('#captchaCode').on('validatecaptcha', function(event, isCorrect) {      
    // display or remove error message
    if (isCorrect) {
      $('.captchaCode').text('');
    } else {
      $('.captchaCode').text(errorMessages.captchaCode);
    }
  });

  
  // On contact form submit
  $('#contactForm').submit(function(event) {

  	// use validateUnsafe() method to perform client-side captcha validation
    captcha.validateUnsafe(function(isCaptchaCodeValid) {

      if (isCaptchaCodeValid && isNameValid && isEmailValid && isSubjectValid && isMessageValid) {
        // form is valid
        // we send contact data as well as captcha data to server-side for
        // validating once again before they are inserted into database
        

        // captcha id for validating captcha at server-side
        var captchaId = captcha.getCaptchaId();

        // captcha code input value for validating captcha at server-side
        var captchaCode = captcha.getCaptchaCode();

        var postData = {
          name: $('#name').val(),
          email: $('#email').val(),
          subject: $('#subject').val(),
          message: $('#message').val(),
          captchaId: captchaId,
          captchaCode: captchaCode
        };
        
        $.ajax({
          method: 'POST',
          url: 'form/contact.php',
          data: JSON.stringify(postData),
          success: function(response) {
            if (response.success) {
              // captcha, other form data passed and the data is also stored in database
              // show success message
              $('#form-messages')
                .removeClass()
                .addClass('alert alert-success')
                .text('Your message was sent successfully!');
            } else {
              // form validation failed
              $('#form-messages')
                .removeClass()      
                .addClass('alert alert-error')
                .text('An error occurred while sending your message, please try again.');
            }
          },
          complete: function() {
            // always reload captcha image after validating captcha at server-side 
            // in order to update new captcha code for current captcha id
            captcha.reloadImage();
          },
          error: function(error) {
            throw new Error(error);
          }
        });
        
      } else {
        // form is invalid
        $('#form-messages')
          .removeClass()      
          .addClass('alert alert-error')
          .text('Please enter valid values.');
      } 

    });

    event.preventDefault();
  });
  
});

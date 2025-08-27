var app = angular.module('app', ['BotDetectCaptcha', 'ngRoute']);

app.config(function($routeProvider, captchaSettingsProvider) {
  $routeProvider
    .when('/basic', { templateUrl: 'templates/basic/basic-captcha.html' })
    .when('/contact', { templateUrl: 'templates/contact/contact-captcha.html' })
    .otherwise({ redirectTo: '/basic' });

  captchaSettingsProvider.setSettings({
    // declare your captcha endpoint, it will be served for getting captcha html, reload icon, sound icons, etc.
    captchaEndpoint: 'captcha-endpoint/simple-botdetect.php'
  });
});

app.controller('BasicController', function($scope, $http, Captcha) {

  // captcha validation messages
  $scope.successMessages = '';
  $scope.errorMessages = '';
  
  // basic captcha url
  var basicUrl = 'basic.php';
  
  $scope.validate = function() {

    // create new BotDetect AngularJS Captcha instance
    var captcha = new Captcha();

    // use validateUnsafe() method to perform client-side captcha validation
    captcha.validateUnsafe(function(isCaptchaCodeCorrect) {

      if (isCaptchaCodeCorrect) {
        
        // after UI form validation passed, 
        // we will need to validate captcha at server-side once before we save form data in database, etc.
        
        // captcha id for validating captcha at server-side
        var captchaId = captcha.captchaId;
        
        // captcha code input value for validating captcha at server-side
        var captchaCode = $scope.captchaCode;

        var postData = {
          captchaId: captchaId,
          captchaCode: captchaCode
        };
        
        $http({
          method: 'POST',
          url: basicUrl,
          data: JSON.stringify(postData)
        })
          .then(function(response) {
            if (response.data.success) {
              // captcha validation passed at server-side
              $scope.successMessages = 'CAPTCHA validation passed.';
              $scope.errorMessages = null;
            } else {
              // captcha validation failed at server-side
              $scope.errorMessages = 'CAPTCHA validation falied.';
              $scope.successMessages = null;
            }
            
            // always reload captcha image after validating captcha at server-side 
            // in order to update new captcha code for current captcha id
            captcha.reloadImage();
          }, function(error) {
            console.log(error.data);
          });
      } else {
        $scope.errorMessages = 'CAPTCHA validation falied.';
        $scope.successMessages = null;
      }
    });
  };
   
});

app.controller('ContactController', function($scope, $http, Captcha) {
  // captcha validation messages
  $scope.successMessages = '';
  $scope.errorMessages = '';
  
  // contact url
  var contactUrl = 'contact.php';

  $scope.send = function(contactForm) {

    // create new BotDetect Angular Captcha instance
    var captcha = new Captcha();

    captcha.validateUnsafe(function(isCaptchaCodeCorrect) {
      
      if (isCaptchaCodeCorrect && contactForm.name.$valid && contactForm.email.$valid 
            && contactForm.subject.$valid && contactForm.message.$valid) {
    
        // after UI form validation passed, 
        // we will need to validate captcha at server-side before we save form data in database, etc.

        // captcha id for validating captcha at server-side
        var captchaId = captcha.captchaId;
        
        // captcha code input value for validating captcha at server-side
        var captchaCode = $scope.captchaCode;
        
        var postData = {
          name: $scope.name,
          email: $scope.email,
          subject: $scope.subject,
          message: $scope.message,
          captchaId: captchaId,
          captchaCode: captchaCode
        };
        
        $http({
          method: 'POST',
          url: contactUrl,
          data: JSON.stringify(postData)
        })
          .then(function(response) {
            if (response.data.success) {
              // captcha, other form data passed and the data is also stored in database
              // show success message
              $scope.successMessages = 'Your message was sent successfully!';
              $scope.errorMessages = null;
            } else {
              // form validation failed
              $scope.errorMessages = response.data.errors;
              $scope.successMessages = null;
            }
            
            // always reload captcha image after validating captcha at server-side 
            // in order to update new captcha code for current captcha id
            captcha.reloadImage();
          }, function(error) {
            console.log(error.data);
          });
      } else {
        $scope.errorMessages = { formInvalid: 'Please enter valid values.' };
        $scope.successMessages = null;
      }
    });
  };
  
});

app.controller('NavigationController', function($scope, $location) {
  $scope.isActive = function(viewLocation) {
    return viewLocation === $location.path();
  };
});

import React from 'react';
import axios from 'axios';
import { Captcha } from 'reactjs-captcha';

class Contact extends React.Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        let self = this;

        // error messages of input fields
        const errorMessages = {
            name: 'Name must be at least 3 characters.',
            email: 'Email is invalid.',
            subject: 'Subject must be at least 10 characters.',
            message: 'Message must be at least 10 characters.',
            captchaCode: 'Invalid code.'
        };

        // global variables that holds validation status of captcha input field,
        // use them for checking validation status when form is submitted
        this.isNameValid = false;
        this.isEmailValid = false;
        this.isSubjectValid = false;
        this.isMessageValid = false;


        function validateName() {
            var name = document.getElementById('name').value;
            self.isNameValid = (name.length >= 3);
            if (self.isNameValid) {
                document.getElementsByClassName('name')[0].innerHTML = '';
            } else {
                document.getElementsByClassName('name')[0].innerHTML = errorMessages.name;
            }
        }

        function validateEmail() {
            var email = document.getElementById('email').value;
            var emailRegEx = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            self.isEmailValid = emailRegEx.test(email);
            if (self.isEmailValid) {
                document.getElementsByClassName('email')[0].innerHTML = '';
            } else {
                document.getElementsByClassName('email')[0].innerHTML = errorMessages.email;
            }
        }

        function validateSubject() {
            var subject = document.getElementById('subject').value;
            self.isSubjectValid = (subject.length >= 10);
            if (self.isSubjectValid) {
                document.getElementsByClassName('subject')[0].innerHTML = '';
            } else {
                document.getElementsByClassName('subject')[0].innerHTML = errorMessages.subject;
            }
        }

        function validateMessage() {
            var message = document.getElementById('message').value;
            self.isMessageValid = (message.length >= 10);
            if (self.isMessageValid) {
                document.getElementsByClassName('message')[0].innerHTML = '';
            } else {
                document.getElementsByClassName('message')[0].innerHTML = errorMessages.message;
            }
        }

        // validate input fields on blur event'
        document.getElementById('name').addEventListener('blur', validateName);
        document.getElementById('email').addEventListener('blur', validateEmail);
        document.getElementById('subject').addEventListener('blur', validateSubject);
        document.getElementById('message').addEventListener('blur', validateMessage);

        // UI captcha validation on blur event by using the custom 'validatecaptcha' event
        // and checking the 'event.detail' variable to either show error messages
        // or check captcha code input field status when form is submitted
        document.getElementById('captchaCode').addEventListener('validatecaptcha', function (event) {
            // update validation status of captcha code input
            let isCaptchaCodeCorrect = event.detail;
            // display or remove error message
            if (isCaptchaCodeCorrect) {
                document.getElementsByClassName('captcha-code-error')[0].innerHTML = '';
            } else {
                document.getElementsByClassName('captcha-code-error')[0].innerHTML = errorMessages.captchaCode;
            }
        });
    }

    submitForm(event) {
        let self = this;
        
        // use validateUnsafe() method to perform client-side captcha validation
        this.captcha.validateUnsafe(function(isCaptchaCodeCorrect) {

            if (isCaptchaCodeCorrect && self.isNameValid && self.isEmailValid && self.isSubjectValid && self.isMessageValid) {
               
                // form is valid
                // we send contact data as well as captcha data to server-side for
                // validating once again before they are inserted into database
            
                // captcha id for validating captcha at server-side
                var captchaId = self.captcha.getCaptchaId();

                // captcha code input value for validating captcha at server-side
                var captchaCode = self.captcha.getCaptchaCode();

                var postData = {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    subject: document.getElementById('subject').value,
                    message: document.getElementById('message').value,
                    captchaId: captchaId,
                    captchaCode: captchaCode
                };

                axios.post('form/contact.php', postData)
                    .then(response => {
                        if (response.data.success) {
                            // captcha, other form data passed and the data is also stored in database
                            // show success message
                            document.getElementById('form-messages').setAttribute('class', 'alert alert-success');
                            document.getElementById('form-messages').innerHTML = 'Your message was sent successfully!';
                        } else {
                            // form validation failed
                            document.getElementById('form-messages').setAttribute('class', 'alert alert-error');
                            document.getElementById('form-messages').innerHTML = 'An error occurred while sending your message, please try again.';
                        }
                        self.captcha.reloadImage();
                    }).catch(error => {
                        throw new Error(error);
                    });
            } else {
                // form is invalid
                document.getElementById('form-messages').setAttribute('class', 'alert alert-error');
                document.getElementById('form-messages').innerHTML = 'Please enter valid values.';
            }

        });

        event.preventDefault();
    }

    render() {
        var self = this;
        return (
            <div id="main-content">
                <form id="contactForm" method="POST" onSubmit={self.submitForm.bind(self)}>
                    <div id="form-messages"></div>

                    <label>
                        <span>Name:</span>
                        <input type="text" id="name" name="name"/>
                    </label>
                    <div className="error name"></div>


                    <label>
                        <span>Email</span>
                        <input type="email" id="email" name="email"/>
                    </label>
                    <div className="error email"></div>


                    <label>
                        <span>Subject:</span>
                        <input type="text" id="subject" name="subject"/>
                    </label>
                    <div className="error subject"></div>


                    <label>
                        <span>Message:</span>
                        <textarea id="message" name="message"></textarea>
                    </label>
                    <div className="error message"></div>


                    <Captcha styleName="reactFormCaptcha" ref={(captcha) => {this.captcha = captcha;}} />

                    <label>
                        <span>Retype the characters from the picture:</span>
                        <input type="text" name="captchaCode" id="captchaCode" data-correct-captcha/>
                    </label>
                    <div className="error captcha-code-error"></div>

                    <button type="submit" id="submitButton" className="btn btn-primary">Send
                    </button>
                </form>
            </div>
        )
    }
}

export default Contact;

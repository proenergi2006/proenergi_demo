import React from 'react';
import axios from 'axios';
import { Captcha } from 'reactjs-captcha';

class Basic extends React.Component {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        // UI captcha validation on blur event by using the custom 'validatecaptcha' event
        // and checking the 'event.detail' variable to either show error messages
        // or check captcha code input field status when form is submitted
        document.getElementById('captchaCode').addEventListener('validatecaptcha', function (event) {
            // display or remove error message
            let isCaptchaCodeCorrect = event.detail;

            if (isCaptchaCodeCorrect) {
                document.getElementsByClassName('captcha-code-error')[0].innerHTML = '';
            } else {
                document.getElementsByClassName('captcha-code-error')[0].innerHTML = 'Incorrect code';
            }
        })
    }

    basicFormSubmit(event) {
      
        // captcha id for validating captcha at server-side
        let captchaId = this.captcha.getCaptchaId();

        // captcha code input value for validating captcha at server-side
        let captchaCode = this.captcha.getCaptchaCode();

        let postData = {
            captchaId: captchaId,
            captchaCode: captchaCode
        };

        let self = this;
        let formMessage = document.getElementById('form-messages');

        axios.post('form/basic.php', postData)
                .then(response => {
                    if (response.data.success) {
                        // captcha validation passed at server-side
                        formMessage.setAttribute('class', 'alert alert-success');
                        formMessage.innerHTML = 'CAPTCHA validation passed.';
                    } else {
                        // captcha validation failed at server-side
                        formMessage.setAttribute('class', 'alert alert-error');
                        formMessage.innerHTML = 'CAPTCHA validation falied.';
                    }
                    self.captcha.reloadImage();
                }).catch(function (error) {
                    throw new Error(error);
                });

        event.preventDefault();
    }

    render() {
        return (
            <section id="main-content">
                <form id="basicForm" method="POST" onSubmit={this.basicFormSubmit.bind(this)}>
                    <div id="form-messages"></div>

                    <Captcha styleName="reactBasicCaptcha" ref={(captcha) => {this.captcha = captcha}} />

                    <label>
                        <span>Retype the characters from the picture:</span>
                        <input type="text" name="captchaCode" id="captchaCode" data-correct-captcha />
                    </label>

                    <div className="error captcha-code-error"></div>

                    <button type="submit" id="submitButton">Validate</button>
                </form>
            </section>
        )
    }
}

export default Basic;

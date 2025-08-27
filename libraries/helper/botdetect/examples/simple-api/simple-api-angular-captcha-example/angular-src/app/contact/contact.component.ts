import { Component, ViewChild, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Observable } from 'rxjs/Rx';

import { CaptchaComponent } from 'angular-captcha';

import { Contact } from './contact.interface';
import { ContactService } from './contact.service';

@Component({
  moduleId: module.id,
  selector: 'contact-form',
  templateUrl: 'contact.component.html',
  styleUrls: ['contact.component.css'],
  providers: [ContactService]
})
export class ContactComponent implements OnInit {
  
  contact: FormGroup;

  emailRegex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

  /**
   * Captcha validation messages.
   */
  errorMessages: Object;
  successMessages: string;

  /**
   * BotDetect CAPTCHA component.
   */
  @ViewChild(CaptchaComponent) captchaComponent: CaptchaComponent;

  constructor(
    private fb: FormBuilder,
    private contactService: ContactService
  ) { }

  ngOnInit(): void {
    this.contact = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(3)]],
      email: ['',  [Validators.required, Validators.pattern(this.emailRegex)]],
      subject: ['',  [Validators.required,Validators.minLength(10)]],
      message: ['',  [Validators.required,Validators.minLength(10)]],
      captchaCode: [''] // we use 'validateUnsafe' method to validate captcha code control when form is submitted
    });
  }

  send({ value }: { value: Contact }): void {

    // use validateUnsafe() method to perform client-side captcha validation
    this.captchaComponent.validateUnsafe((isCaptchaCodeCorrect: boolean) => {

      if (isCaptchaCodeCorrect && this.contact.controls.name.valid && this.contact.controls.email.valid
            && this.contact.controls.subject.valid && this.contact.controls.message.valid) {

        // form is valid
        // we send contact data as well as captcha data to server-side for
        // validating once again before they are inserted into database

        let postData = {
          name: value.name,
          email: value.email,
          subject: value.subject,
          message: value.message,
          captchaCode: this.captchaComponent.captchaCode,
          captchaId: this.captchaComponent.captchaId
        };
    
        this.contactService.send(postData)
          .subscribe(
            response => {
              if (response.success) {
                // captcha validation passed at server-side
                this.successMessages = 'CAPTCHA validation passed.';
                this.errorMessages = null;
              } else {
                // captcha validation failed at server-side
                this.errorMessages = response.errors;
                this.successMessages = '';
              }
    
              // always reload captcha image after validating captcha at server-side 
              // in order to update new captcha code for current captcha id
              this.captchaComponent.reloadImage();
            },
            error => {
              throw new Error(error);
            });
            
      } else {
        this.errorMessages = { formInvalid: 'Please enter valid values.' }
        this.successMessages = '';
      }
    });

  }
}

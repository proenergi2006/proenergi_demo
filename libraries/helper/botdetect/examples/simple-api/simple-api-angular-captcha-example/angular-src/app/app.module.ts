import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { AppRoutingModule } from './app-routing.module';
import { BotDetectCaptchaModule } from 'angular-captcha';

import { AppComponent } from './app.component';
import { BasicComponent }   from './basic/basic.component';
import { ContactComponent }   from './contact/contact.component';

import { ValuesPipe } from './values.pipe';

@NgModule({
  declarations: [
    AppComponent,
    BasicComponent,
    ContactComponent,
    ValuesPipe
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule,
    ReactiveFormsModule,
    AppRoutingModule,
    BotDetectCaptchaModule.forRoot({
      captchaEndpoint: 'captcha-endpoint/simple-botdetect.php',
    })
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }

import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { BasicComponent }   from './basic/basic.component';
import { ContactComponent }   from './contact/contact.component';

const routes: Routes = [
  { path: '', redirectTo: '/basic', pathMatch: 'full' },
  { path: 'basic',  component: BasicComponent },
  { path: 'contact',  component: ContactComponent }
];

@NgModule({
  imports: [ RouterModule.forRoot(routes, { useHash: true } ) ],
  exports: [ RouterModule ]
})
export class AppRoutingModule {}

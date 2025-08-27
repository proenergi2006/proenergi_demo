import { Injectable }    from '@angular/core';
import { Http, Response, Headers, RequestOptions } from '@angular/http';

import { Observable } from 'rxjs/Rx';

@Injectable()
export class ContactService {

  // contact api url
  contactUrl = 'contact.php';

  constructor(private http: Http) { }

  send(data: Object): Observable<any> {
    const headers = new Headers({ 'Content-Type': 'application/json' });
    const options = new RequestOptions({ headers: headers });

    return this.http.post(this.contactUrl, data, options)
      .map((response: Response) => response.json())
      .catch((error:any) => Observable.throw(error.json().error));
  }
}
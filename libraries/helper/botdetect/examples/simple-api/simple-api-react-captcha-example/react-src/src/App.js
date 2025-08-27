import React, { Component } from 'react';
import { HashRouter } from 'react-router-dom'
import BaseLayout from './BaseLayout.jsx';
import { captchaSettings } from 'reactjs-captcha';


class App extends Component {

  constructor(props) {
    super(props);

    captchaSettings.set({
      captchaEndpoint: 'captcha-endpoint/simple-botdetect.php'
    });
  }


  render() {
    return (
      <HashRouter>
        <BaseLayout />
      </HashRouter>
    );
  }
}

export default App;

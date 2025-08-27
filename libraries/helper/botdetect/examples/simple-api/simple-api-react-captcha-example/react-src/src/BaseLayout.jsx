import React from 'react';
import { BrowserRouter as Router, Route, Link, NavLink } from 'react-router-dom';
import Basic from './views/basic.jsx';
import Contact from './views/contact.jsx';
import './App.css';

class BaseLayout extends React.Component {
    render() {
        return (
            <div>
                <header>
                    <div className="header-content"><h1>BotDetect React CAPTCHA Examples</h1></div>
                </header>

                <ul className="nav">
                    <li><NavLink to="/basic-form">Basic</NavLink></li>
                    <li><NavLink to="/contact-form">Contact</NavLink></li>
                </ul>

                <Route exact path="/" component={Basic}/>
                <Route path="/basic-form" component={Basic}/>
                <Route path="/contact-form" component={Contact}/>

            </div>
        )
    }
}

export default BaseLayout;

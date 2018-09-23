import React, {Component} from 'react';
import logo from './logo.svg';
import './App.css';

class App extends Component {
    render() {
        return (
            <div className="App">
                <header className="App-header">
                    <img src={logo} className="App-logo" alt="logo"/>
                    <h1 className="App-title">Yet Another Steam Play Compatibility Checker</h1>
                </header>
                <p className="App-intro">
                    <div className={"app-container"}>
                        <div className={"app-image"} style={{float: "left"}}>
                            <img src={"placeholder.jpg"} data-src={"somewhere-valve-related"}/>
                        </div>
                        <div className={"app-title"} style={{"border-bottom": "1px"}}>
                            asd
                        </div><br/>
                        <div>
                            <p>
                                Rating
                            </p>

                        </div>
                    </div>
                </p>
            </div>
        );
    }
}

export default App;

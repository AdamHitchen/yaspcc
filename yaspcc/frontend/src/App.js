import React, {Component} from 'react';
import logo from './logo.svg';
import './App.css';


class App extends Component {
    render() {
        return (
            <div className="App">
                <header className="App-header">
                    <h1 className="App-title">Yet Another Steam Play Compatibility Checker</h1>
                </header>
                <div className={"container-fluid"}>
                    <div className={"app-container row"}>
                        <div className={"col-sm-6 col-md-offset-3"}>
                            <div className={"app-image col-sm-3"} style={{float: "left"}}>
                                <img src={"placeholder.jpg"} data-src={"somewhere-valve-related"}/>
                            </div>
                            <div className={"app-title"} style={{"border-bottom": "1px"}}>
                                asd
                            </div>
                            <br/>
                            <div>
                                <p>
                                    Rating
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default App;

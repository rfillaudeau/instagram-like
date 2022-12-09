import React from "react"
import ReactDOM from "react-dom/client"
import App from "./App"
import {AuthContextProvider} from "./contexts/AuthContext"
import {BrowserRouter} from "react-router-dom"
import "bootstrap/dist/js/bootstrap.bundle.min"
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-icons/font/bootstrap-icons.css"
import "../css/app.css"

ReactDOM
    .createRoot(document.getElementById('root'))
    .render(
        <React.StrictMode>
            <AuthContextProvider>
                <BrowserRouter>
                    <App />
                </BrowserRouter>
            </AuthContextProvider>
        </React.StrictMode>
    )

import React from "react"
import Header from "./Header"
import Footer from "./Footer"
import {Outlet} from "react-router-dom"

function Base() {
    return (
        <>
            <Header/>
            <Outlet/>
            <Footer/>
        </>
    )
}

export default Base

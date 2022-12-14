import React, {useContext} from "react"
import Header from "./Header"
import Footer from "./Footer"
import {Outlet} from "react-router-dom"
import PostFormModal from "./PostFormModal"
import AuthContext from "../contexts/AuthContext"

function Base() {
    const {currentUser} = useContext(AuthContext)

    return (
        <>
            <Header/>
            <Outlet/>
            <Footer/>

            {currentUser !== null && <PostFormModal modalId={"createPostModal"}/>}
        </>
    )
}

export default Base

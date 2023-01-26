import React from "react"
import Header from "./Header"
import Footer from "./Footer"
import {Outlet} from "react-router-dom"
import PostFormModal from "./PostFormModal"
import {useAuth} from "../contexts/AuthContext"

function Base() {
    const {currentUser} = useAuth()

    return (
        <>
            <Header/>

            <main className="py-3">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-8">
                            <Outlet/>
                        </div>
                    </div>
                </div>
            </main>

            <Footer/>

            {currentUser !== null && <PostFormModal modalId={"createPostModal"}/>}
        </>
    )
}

export default Base

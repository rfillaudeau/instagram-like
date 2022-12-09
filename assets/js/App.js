import React from "react"
import Header from "./components/Header"
import {Route, Routes, useLocation} from "react-router-dom"
import Footer from "./components/Footer"
import Feed from "./pages/Feed"
import Discover from "./pages/Discover"
import SignIn from "./pages/SignIn"
import SignUp from "./pages/SignUp"
import Profile from "./pages/Profile"
import Post from "./pages/Post"
import Settings from "./pages/Settings"
import ProtectedRoute from "./components/ProtectedRoute"
import PostForm from "./pages/PostForm"
import NotFound from "./pages/NotFound"

function App() {
    const {pathname} = useLocation()
    const hideHeaderAndFooterPaths = ["/sign-in", "/sign-up"]

    const showHeaderAndFooter = hideHeaderAndFooterPaths.indexOf(pathname) === -1

    return (
        <>
            {showHeaderAndFooter && <Header/>}

            <Routes>
                <Route path="/" element={
                    <ProtectedRoute><Feed /></ProtectedRoute>
                } />
                <Route path="/discover" element={<Discover />} />
                <Route path="/sign-in" element={<SignIn />} />
                <Route path="/sign-up" element={<SignUp />} />
                <Route path="/@:username" element={<Profile />} />
                <Route path="/settings" element={<Settings />} />
                <Route path="/posts/new" element={<PostForm />} />
                <Route path="/posts/:id/edit" element={<PostForm />} />
                <Route path="/posts/:id" element={<Post />} />
                <Route path="/admin" element={<div>Admin</div>} />
                <Route path="*" element={<NotFound />} />
            </Routes>

            {showHeaderAndFooter && <Footer />}
        </>
    )
}

export default App

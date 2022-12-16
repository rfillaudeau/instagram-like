import React from "react"
import {Route, Routes} from "react-router-dom"
import Feed from "./pages/Feed"
import Discover from "./pages/Discover"
import SignIn from "./pages/SignIn"
import SignUp from "./pages/SignUp"
import Profile from "./pages/Profile"
import Post from "./pages/Post"
import Settings from "./pages/Settings"
import ProtectedRoute from "./components/ProtectedRoute"
import NotFound from "./pages/NotFound"
import Base from "./components/Base"

// TODO: for admin
// const Admin = React.lazy(() => import("path_to_component"))
// <React.Suspense><Admin /></React.Suspense>

function App() {
    return (
        <Routes>
            <Route path="/" element={<Base/>}>
                <Route index element={<ProtectedRoute><Feed/></ProtectedRoute>}/>
                <Route path="/discover" element={<Discover/>}/>
                <Route path="/@:username" element={<Profile/>}/>
                <Route path="/settings" element={<ProtectedRoute><Settings/></ProtectedRoute>}/>
                <Route path="/posts/:id" element={<Post/>}/>
                <Route path="/admin" element={<div>Admin</div>}/>
            </Route>

            <Route path="/sign-in" element={<SignIn/>}/>
            <Route path="/sign-up" element={<SignUp/>}/>

            <Route path="*" element={<NotFound/>}/>
        </Routes>
    )
}

export default App

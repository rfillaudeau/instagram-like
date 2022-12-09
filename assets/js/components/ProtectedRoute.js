import React, {useContext} from "react"
import {Navigate, useLocation} from "react-router-dom"
import AuthContext from "../contexts/AuthContext"

function ProtectedRoute({children}) {
    const {currentUser} = useContext(AuthContext)
    const {pathname} = useLocation()

    if (currentUser === null) {
        return <Navigate to={`/sign-in?from=${pathname}`} replace />
    }

    return children
}

export default ProtectedRoute

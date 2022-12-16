import React, {useContext} from "react"
import {Navigate} from "react-router-dom"
import AuthContext from "../contexts/AuthContext"

function ProtectedRoute({children}) {
    const {currentUser} = useContext(AuthContext)

    if (currentUser === null) {
        return <Navigate to={`/sign-in`} replace />
    }

    return children
}

export default ProtectedRoute

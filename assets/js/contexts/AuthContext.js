import React, {createContext, useState} from "react"

const AuthContext = createContext(null)

export function AuthContextProvider(props) {
    const [currentUser, setCurrentUser] = useState(window.user)

    function updateUser(newUser) {
        setCurrentUser(prevUser => {
            if (prevUser === null) {
                return newUser
            }

            return {
                ...prevUser,
                ...newUser
            }
        })
    }

    return (
        <AuthContext.Provider value={{
            currentUser,
            updateUser
        }}>
            {props.children}
        </AuthContext.Provider>
    )
}

export default AuthContext

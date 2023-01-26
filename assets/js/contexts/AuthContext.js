import React, {createContext, useContext, useEffect, useState} from "react"
import axios, {CanceledError} from "axios"

const ACCESS_TOKEN_KEY = "access_token"
const USER_KEY = "user"

const AuthContext = createContext(null)

export function AuthContextProvider(props) {
    const [currentUser, setCurrentUser] = useState(getStoredUser())
    const [accessToken, setAccessToken] = useState(localStorage.getItem(ACCESS_TOKEN_KEY))

    let apiConfig = {
        baseURL: "/api",
        headers: {
            Accept: "application/json",
        }
    }

    if (accessToken !== null) {
        apiConfig = {
            ...apiConfig,
            headers: {
                ...apiConfig.headers,
                Authorization: `Bearer ${accessToken}`
            }
        }
    }

    const api = axios.create(apiConfig)

    useEffect(() => {
        if (accessToken === null) {
            return
        }

        const controller = new AbortController()

        api.get('/users/me', {
            signal: controller.signal
        }).then(response => {
            updateUser(response.data)
        }).catch(error => {
            if (error instanceof CanceledError) {
                return
            }

            console.error(error)
        })

        return () => controller.abort()
    }, [accessToken])

    useEffect(() => {
        if (currentUser === null) {
            return
        }

        localStorage.setItem(USER_KEY, JSON.stringify(currentUser))
    }, [currentUser])

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

    function getStoredUser() {
        const user = localStorage.getItem(USER_KEY)

        return user === null ? null : JSON.parse(user)
    }

    function updateAccessTokenData(data) {
        const {token, expiresAt} = data

        setAccessToken(token)

        localStorage.setItem(ACCESS_TOKEN_KEY, token)
    }

    function clearSession() {
        setAccessToken(null)
        setCurrentUser(null)

        localStorage.clear()
    }

    return (
        <AuthContext.Provider value={{
            currentUser,
            updateUser,
            api,
            updateAccessTokenData,
            clearSession
        }}>
            {props.children}
        </AuthContext.Provider>
    )
}

export const useAuth = () => useContext(AuthContext)

export default AuthContext

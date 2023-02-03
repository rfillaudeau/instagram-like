import React, {createContext, useContext, useEffect, useState} from "react"
import axios, {CanceledError} from "axios"

const ACCESS_TOKEN_KEY = "access_token"
const ACCESS_TOKEN_EXPIRY_DATE_KEY = "access_token_expiry_date"
const USER_KEY = "user"

const AuthContext = createContext(null)

export function AuthContextProvider(props) {
    const [currentUser, setCurrentUser] = useState(getStoredUser())
    const [accessToken, setAccessToken] = useState(localStorage.getItem(ACCESS_TOKEN_KEY))
    const [accessTokenExpiryDate, setAccessTokenExpiryDate] = useState(getStoredAccessTokenExpiryDate())

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
        if (accessTokenExpiryDate === null) {
            return
        }

        let delay = accessTokenExpiryDate.getTime() - (new Date()).getTime()
        if (delay < 0) {
            delay = 0
        }

        // setTimeout does not work with delays over 24 hours
        if (delay / 1000 / 60 / 60 >= 24) {
            return
        }

        const timeoutId = setTimeout(() => {
            clearSession()
        }, delay)

        return () => clearTimeout(timeoutId)
    }, [accessTokenExpiryDate])

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

    function getStoredAccessTokenExpiryDate() {
        const expiryDate = localStorage.getItem(ACCESS_TOKEN_EXPIRY_DATE_KEY)

        return expiryDate === null ? null : new Date(parseInt(expiryDate))
    }

    function updateAccessTokenData(data) {
        const {token, expiresAt} = data
        const expiryDate = new Date(expiresAt)

        setAccessToken(token)
        setAccessTokenExpiryDate(expiryDate)

        localStorage.setItem(ACCESS_TOKEN_KEY, token)
        localStorage.setItem(ACCESS_TOKEN_EXPIRY_DATE_KEY, expiryDate.getTime().toString())
    }

    function clearSession() {
        setAccessToken(null)
        setAccessTokenExpiryDate(null)
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

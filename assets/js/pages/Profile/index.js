import React, {useEffect, useState} from "react"
import {useParams} from "react-router-dom"
import ProfilePosts from "./ProfilePosts"
import NotFound from "../NotFound"
import ProfileCard from "./ProfileCard"
import ProfilePlaceholder from "./ProfilePlaceholder"
import {useAuth} from "../../contexts/AuthContext"

function Profile() {
    const {api} = useAuth()
    const {username} = useParams()
    const [user, setUser] = useState(null)
    const [isNotFound, setIsNotFound] = useState(false)

    useEffect(() => {
        const controller = new AbortController()

        setIsNotFound(false)

        api.get(`/users/username/${username}`, {
            signal: controller.signal
        }).then(response => {
            setUser(response.data)
        }).catch(error => {
            if (!error.response) {
                return
            }

            if (error.response.status === 404) {
                setIsNotFound(true)
            } else {
                console.error(error)
            }
        })

        return () => {
            controller.abort()
        }
    }, [username])

    if (isNotFound) {
        return <NotFound/>
    }

    if (user === null) {
        return (
            <ProfilePlaceholder/>
        )
    }

    return (
        <>
            <ProfileCard user={user}/>

            <ProfilePosts user={user}/>
        </>
    )
}

export default Profile

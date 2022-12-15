import React, {useEffect, useState} from "react"
import {useParams} from "react-router-dom"
import axios from "axios"
import ProfilePosts from "./ProfilePosts"
import NotFound from "../NotFound"
import ProfileCard from "./ProfileCard"

function Profile() {
    const {username} = useParams()
    const [user, setUser] = useState(null)
    const [isNotFound, setIsNotFound] = useState(false)

    useEffect(() => {
        const controller = new AbortController()

        setIsNotFound(false)

        axios
            .get(`/api/users/${username}`, {
                signal: controller.signal
            })
            .then(response => {
                setUser(response.data)
            })
            .catch(error => {
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
            <div>
                Loading...
            </div>
        )
    }

    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <ProfileCard user={user}/>

                        <ProfilePosts user={user}/>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Profile

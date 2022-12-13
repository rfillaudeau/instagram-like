import React, {useEffect, useState} from "react"
import {useParams} from "react-router-dom"
import axios, {CanceledError} from "axios"
import nl2br from "../../utils/nl2br"
import abbreviateNumber from "../../utils/abreviateNumber"
import FollowButton from "../../components/FollowButton"
import ProfilePosts from "./ProfilePosts"

function Profile() {
    const {username} = useParams()
    const [user, setUser] = useState(null)

    useEffect(() => {
        const controller = new AbortController()

        axios
            .get(`/api/users/${username}`, {
                signal: controller.signal
            })
            .then(response => {
                setUser(response.data)
            })
            .catch(error => {
                if (error instanceof CanceledError) {
                    return
                }

                console.error(error)
                // if (error.response.status === 404) {
                //     console.log("User not found")
                // } else {
                //     console.log("Unknown error")
                // }
            })

        return () => {
            controller.abort()
        }
    }, [])

    if (user === null) {
        return (
            <div>
                Loading...
            </div>
        )
    }

    function handleFollow(isFollowed) {
        setUser(prevUser => {
            let newUser = {
                ...prevUser,
                isFollowed: isFollowed
            }

            if (isFollowed) {
                newUser.followerCount++
            } else {
                newUser.followerCount--
            }

            return newUser
        })
    }

    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="card mb-2">
                            <div className="card-body">
                                <div className="d-flex">
                                    <div className="me-3">
                                        <img
                                            src={user.avatarFilepath}
                                            className="rounded img-fluid avatar-lg"
                                            alt={`${user.username}'s avatar`} />
                                    </div>

                                    <div className="flex-fill">
                                        <div className="d-flex mb-3">
                                            <div className="flex-fill fs-2 fw-semibold">
                                                {user.username}
                                            </div>
                                            <div className="align-self-center">
                                                <FollowButton
                                                    user={user}
                                                    onFollow={() => handleFollow(true)}
                                                    onUnfollow={() => handleFollow(false)}
                                                />
                                            </div>
                                        </div>

                                        <div className="row mb-3">
                                            <div className="col">
                                                <b>{abbreviateNumber(user.postCount)}</b> post{user.postCount > 1 ? "s" : ""}
                                            </div>
                                            <div className="col text-center">
                                                <b>{abbreviateNumber(user.followerCount)}</b> follower{user.followerCount > 1 ? "s" : ""}
                                            </div>
                                            <div className="col text-end">
                                                <b>{abbreviateNumber(user.followingCount)}</b> following
                                            </div>
                                        </div>

                                        <p className="mb-0">{nl2br(user.bio)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ProfilePosts user={user}/>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Profile

import React, {useRef, useState} from "react"
import {useAuth} from "../contexts/AuthContext"

function FollowButton({className, user: propsUser, onFollow, onUnfollow}) {
    const {currentUser, api} = useAuth()
    const [user, setUser] = useState(propsUser)
    const followButtonRef = useRef(null)

    function follow() {
        if (followButtonRef !== null) {
            followButtonRef.current.disabled = true
        }

        api.post(`/users/${user.username}/follow`).then(() => {
            setUser(prevUser => ({
                ...prevUser,
                isFollowed: true
            }))

            if (onFollow instanceof Function) {
                onFollow()
            }
        }).catch(error => {
            console.error(error)
        }).finally(() => {
            if (followButtonRef !== null) {
                followButtonRef.current.disabled = false
            }
        })
    }

    function unfollow() {
        if (followButtonRef !== null) {
            followButtonRef.current.disabled = true
        }

        api.delete(`/users/${user.username}/follow`).then(() => {
            setUser(prevUser => ({
                ...prevUser,
                isFollowed: false
            }))

            if (onUnfollow instanceof Function) {
                onUnfollow()
            }
        }).catch(error => {
            console.error(error)
        }).finally(() => {
            if (followButtonRef !== null) {
                followButtonRef.current.disabled = false
            }
        })
    }

    if (currentUser === null || currentUser.id === user.id) {
        return null
    }

    return (
        <button
            type="button"
            className={`btn btn-${user.isFollowed ? "outline-secondary" : "primary"}${className ? ` ${className}` : ""}`}
            ref={followButtonRef}
            onClick={user.isFollowed ? unfollow : follow}
        >
            {user.isFollowed ? "Unfollow" : "Follow"}
        </button>
    )
}

export default FollowButton

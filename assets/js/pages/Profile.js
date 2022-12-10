import React, {useEffect, useState} from "react"
import {useParams} from "react-router-dom"
import PostPreview from "../components/PostPreview"
import axios from "axios"
import nl2br from "../utils/nl2br"
import abbreviateNumber from "../utils/abreviateNumber"

function Profile() {
    const {username} = useParams()
    const [user, setUser] = useState(null)

    useEffect(() => {
        axios
            .get(`/api/users/${username}`)
            .then(response => {
                setUser(response.data)
            }).catch(error => {
                if (error.response.status === 404) {
                    console.log("User not found")
                } else {
                    console.log("Unknown error")
                }
            })
    }, [])

    if (user === null) {
        return (
            <div>
                Loading...
            </div>
        )
    }

    let posts = []
    for (let i = 0; i < 20; i++) {
        posts.push({
            id: i,
            picture: "/doge.jpg"
        })
    }

    const postElements = posts.map((post, index) => (
        <div key={index} className="col p-2">
            <PostPreview />
        </div>
    ))

    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="card mb-2">
                            <div className="card-body">
                                <div className="d-flex">
                                    <div className="me-3 w-auto">
                                        <img src="/doge.jpg" className="rounded img-fluid" alt="test" style={{width: 100}} />
                                    </div>

                                    <div className="w-100">
                                        <div className="d-flex mb-3">
                                            <div className="flex-grow-1 fs-2 fw-semibold">
                                                {user.username}
                                            </div>
                                            <div className="align-self-center">
                                                <a className="btn btn-primary">Follow</a>
                                            </div>
                                        </div>

                                        <div className="row mb-3">
                                            <div className="col">
                                                <b>{abbreviateNumber(user.postCount)}</b> post{user.postCount > 1 ? "s" : ""}
                                            </div>
                                            <div className="col text-center">
                                                <b>55k</b> followers
                                            </div>
                                            <div className="col text-end">
                                                <b>9k</b> following
                                            </div>
                                        </div>

                                        <p className="mb-0">{nl2br(user.bio)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="row row-cols-3">
                            {postElements}
                        </div>
                    </div>
                </div>


            </div>
        </main>
    )
}

export default Profile

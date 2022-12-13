import React, {useEffect, useState} from "react"
import {Link, useParams} from "react-router-dom"
import axios, {CanceledError} from "axios"
import nl2br from "../../utils/nl2br"
import PostPlaceholder from "./PostPlaceholder"
import FollowButton from "../../components/FollowButton"

function Post() {
    const {id} = useParams()
    const [post, setPost] = useState(null)

    useEffect(() => {
        const controller = new AbortController()

        axios
            .get(`/api/posts/${id}`, {
                signal: controller.signal
            })
            .then(response => {
                console.log(response.data)
                setPost(response.data)
            })
            .catch(error => {
                if (error instanceof CanceledError) {
                    return
                }

                if (error.response.status === 404) {
                    console.log("Post not found")
                } else {
                    console.log("Unknown error")
                }
            })

        return () => {
            controller.abort()
        }
    }, [])

    if (post === null) {
        return <PostPlaceholder/>
    }

    const createdAtDate = (new Date(post.createdAt)).toLocaleString()

    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="card mb-2">
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-7 pe-2">
                                        <img
                                            src={`/uploads/posts/${post.pictureFilename}`}
                                            className="rounded img-fluid"
                                            alt="..."
                                        />
                                    </div>
                                    <div className="col-5">
                                        <div className="d-flex mb-3">
                                            <div className="align-self-center">
                                                <Link to={`/@${post.user.username}`}>
                                                    <img
                                                        src="/doge.jpg"
                                                        className="rounded img-fluid avatar-sm"
                                                        alt="test"
                                                    />
                                                </Link>
                                            </div>
                                            <div className="align-self-center ps-1 fw-semibold small flex-fill mx-3">
                                                <Link to={`/@${post.user.username}`}
                                                      className="text-decoration-none link-dark">
                                                    {post.user.username}
                                                </Link>
                                            </div>
                                            <FollowButton user={post.user}/>
                                        </div>
                                        <p className="mb-3">{nl2br(post.description)}</p>
                                        <small className="text-muted">{createdAtDate}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Post

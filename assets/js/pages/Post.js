import React, {useEffect, useState} from "react"
import {Link, useParams} from "react-router-dom"
import axios from "axios"
import nl2br from "../utils/nl2br"

function Post() {
    const {id} = useParams()
    const [post, setPost] = useState(null)

    useEffect(() => {
        axios
            .get(`/api/posts/${id}`)
            .then(response => {
                console.log(response.data)
                setPost(response.data)
            }).catch(error => {
            if (error.response.status === 404) {
                console.log("Post not found")
            } else {
                console.log("Unknown error")
            }
        })
    }, [])

    if (post === null) {
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
                        <div className="card mb-2">
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-7">
                                        <img
                                            src={`/uploads/posts/${post.pictureFilename}`}
                                            className="rounded img-fluid"
                                            alt="..."
                                        />
                                    </div>
                                    <div className="col-5">
                                        <Link to={`/@${post.user.username}`} className="d-flex text-decoration-none">
                                            <img
                                                src="/doge.jpg"
                                                className="rounded img-fluid"
                                                alt="test"
                                                style={{width: 25}}
                                            />
                                            <div className="align-self-center ps-1 fw-semibold small link-dark">
                                                {post.user.username}
                                            </div>
                                            Follow
                                        </Link>
                                        <p className="mb-0">{nl2br(post.description)}</p>
                                        <p>Date</p>
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

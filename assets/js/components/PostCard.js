import React from "react"
import {Link} from "react-router-dom"
import nl2br from "../utils/nl2br"
import abbreviateNumber from "../utils/abreviateNumber"

function PostCard({post}) {
    const createdAtDate = (new Date(post.createdAt)).toLocaleString()

    return (
        <div className="card">
            <div className="p-3 d-flex">
                <div>
                    <Link to={`/@${post.user.username}`} className="">
                        <img
                            src="/doge.jpg"
                            className="rounded img-fluid avatar-sm"
                            alt={`${post.user.username}'s avatar`}
                        />
                    </Link>
                </div>

                <div className="align-self-center ms-3 flex-fill">
                    <Link to={`/@${post.user.username}`} className="fw-semibold link-dark text-decoration-none">
                        {post.user.username}
                    </Link>
                </div>
            </div>

            <img src={`/uploads/posts/${post.pictureFilename}`} className="img-fluid" alt="..." />

            <div className="card-body">
                <div className="d-flex align-items-center mb-3">
                    <div>
                        <i className="bi bi-heart fs-4"></i>
                    </div>
                    <div className="ms-auto">
                        {abbreviateNumber(post.likeCount)} like{post.likeCount > 1 ? "s" : ""}
                    </div>
                </div>

                {/*<h5 className="card-title">Card title</h5>*/}
                <p className="mb-3 text-justify">{nl2br(post.description)}</p>

                <small className="text-muted">{createdAtDate}</small>
            </div>
        </div>
    )
}

export default PostCard

import React, {useState} from "react"
import {Link} from "react-router-dom"
import abbreviateNumber from "../utils/abreviateNumber"

function PostPreview({post}) {
    const [showLikes, setShowLikes] = useState(false)

    const likesBlock = (
        <div className="w-100 h-100 position-absolute d-flex justify-content-center align-items-center">
            <div className="rounded bg-dark opacity-50 bg-dark w-100 h-100 position-absolute z-index-1">
            </div>

            <div className="text-light fs-3 z-index-2">
                <i className="bi bi-heart-fill"></i> <b>{abbreviateNumber(post.likeCount)}</b>
            </div>
        </div>
    )

    return (
        <Link
            to={`/posts/${post.id}`}
            className="d-block position-relative"
            onMouseEnter={() => setShowLikes(true)}
            onMouseLeave={() => setShowLikes(false)}
        >
            {showLikes && likesBlock}

            <img src={post.pictureFilePath} className="rounded img-fluid" alt="..."/>
        </Link>
    )
}

export default PostPreview

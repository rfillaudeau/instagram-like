import React from "react"
import {Link} from "react-router-dom"

function PostPreview() {
    return (
        <Link to="/posts/1" className="d-block">
            <img src="/doge.jpg" className="rounded img-fluid" alt="..." />
        </Link>
    )
}

export default PostPreview

import React, {useState} from "react"
import {Link} from "react-router-dom"
import abbreviateNumber from "../../utils/abreviateNumber"
import LikeButton from "../../components/LikeButton"
import ShowMoreText from "../../components/ShowMoreText"

function PostCard({post}) {
    const [likeCount, setLikeCount] = useState(post.likeCount)

    const createdAtDate = (new Date(post.createdAt)).toLocaleString()

    function handleLike(isLiked) {
        if (isLiked) {
            setLikeCount(prevLikeCount => prevLikeCount + 1)
        } else {
            setLikeCount(prevLikeCount => prevLikeCount - 1)
        }
    }

    return (
        <div className="card">
            <div className="p-3 d-flex">
                <div>
                    <Link to={`/@${post.user.username}`} className="">
                        <img
                            src={post.user.avatarFilepath}
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

            <img src={post.pictureFilepath} className="img-fluid" alt="..." />

            <div className="card-body">
                <div className="d-flex align-items-center mb-3">
                    <div className="fs-4 text-decoration-none">
                        <LikeButton
                            post={post}
                            onLike={() => handleLike(true)}
                            onUnlike={() => handleLike(false)}
                        />
                    </div>
                    <div className="ms-auto">
                        {abbreviateNumber(likeCount)} like{likeCount > 1 ? "s" : ""}
                    </div>
                </div>

                <p className="mb-3 text-justify">
                    <ShowMoreText text={post.description} maxLength={150}/>
                </p>

                <small className="text-muted">{createdAtDate}</small>
            </div>
        </div>
    )
}

export default PostCard

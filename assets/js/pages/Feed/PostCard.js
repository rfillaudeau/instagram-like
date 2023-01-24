import React, {useContext, useState} from "react"
import {Link} from "react-router-dom"
import abbreviateNumber from "../../utils/abreviateNumber"
import LikeButton from "../../components/LikeButton"
import ShowMoreText from "../../components/ShowMoreText"
import AuthContext from "../../contexts/AuthContext"

function PostCard({post}) {
    const {currentUser} = useContext(AuthContext)
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
                <div className="align-self-center">
                    <Link to={`/${post.user.username}`} className="">
                        <img
                            src={post.user.avatarFilepath}
                            className="rounded img-fluid avatar-sm"
                            alt={`${post.user.username}'s avatar`}
                        />
                    </Link>
                </div>

                <div className="align-self-center ms-3 flex-fill">
                    <Link to={`/${post.user.username}`} className="fw-semibold link-dark text-decoration-none">
                        {post.user.username}
                    </Link>
                </div>

                <div className="dropdown align-self-center">
                    <button className="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i className="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul className="dropdown-menu dropdown-menu-end">
                        <li>
                            <Link to={`/posts/${post.id}`} className="dropdown-item">
                                <i className="bi bi-box-arrow-up-right"></i> Go to post
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <img src={post.pictureFilepath} className="img-fluid" alt="..."/>

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

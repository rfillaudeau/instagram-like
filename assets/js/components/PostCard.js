import React, {useRef, useState} from "react"
import {Link} from "react-router-dom"
import nl2br from "../utils/nl2br"
import abbreviateNumber from "../utils/abreviateNumber"
import axios from "axios"

function PostCard({post}) {
    const [isLiked, setIsLiked] = useState(post.isLiked)
    const [likeCount, setLikeCount] = useState(post.likeCount)
    const likeButtonRef = useRef(null)

    const createdAtDate = (new Date(post.createdAt)).toLocaleString()

    function getAvatarPath(filename) {
        if (filename === null) {
            return "/doge.jpg"
        }

        return `/uploads/avatars/${filename}`
    }

    function changeLike() {
        likeButtonRef.current.disabled = true

        if (isLiked) {
            axios
                .delete(`/api/posts/${post.id}/like`)
                .then(() => {
                    setLikeCount(prevLikeCount => prevLikeCount - 1)
                    setIsLiked(prevIsLiked => !prevIsLiked)
                })
                .catch(error => {
                    console.error(error)
                })
                .finally(() => {
                    likeButtonRef.current.disabled = false
                })
        } else {
            axios
                .post(`/api/posts/${post.id}/like`)
                .then(response => {
                    if (response.status === 201) {
                        setLikeCount(prevLikeCount => prevLikeCount + 1)
                        setIsLiked(prevIsLiked => !prevIsLiked)
                    }
                })
                .catch(error => {
                    console.error(error)
                })
                .finally(() => {
                    likeButtonRef.current.disabled = false
                })
        }
    }

    return (
        <div className="card">
            <div className="p-3 d-flex">
                <div>
                    <Link to={`/@${post.user.username}`} className="">
                        <img
                            src={getAvatarPath(post.user.avatarFilename)}
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
                    <div className="fs-4 text-decoration-none">
                        <button
                            type="button"
                            className="btn btn-outline-danger"
                            onClick={changeLike}
                            ref={likeButtonRef}
                        >
                            {
                                isLiked ? (
                                    <i className="bi bi-heart-fill"></i>
                                ) : (
                                    <i className="bi bi-heart"></i>
                                )
                            }
                        </button>
                    </div>
                    <div className="ms-auto">
                        {abbreviateNumber(likeCount)} like{likeCount > 1 ? "s" : ""}
                    </div>
                </div>

                <p className="mb-3 text-justify">{nl2br(post.description)}</p>

                <small className="text-muted">{createdAtDate}</small>
            </div>
        </div>
    )
}

export default PostCard

import React, {useRef, useState} from "react"
import axios from "axios"

function LikeButton({post, onLike, onUnlike}) {
    const [isLiked, setIsLiked] = useState(post.isLiked)
    const likeButtonRef = useRef(null)

    function changeLike() {
        likeButtonRef.current.disabled = true

        if (isLiked) {
            axios
                .delete(`/api/posts/${post.id}/like`)
                .then(() => {
                    setIsLiked(prevIsLiked => !prevIsLiked)

                    if (onUnlike instanceof Function) {
                        onUnlike()
                    }
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
                        setIsLiked(prevIsLiked => !prevIsLiked)

                        if (onLike instanceof Function) {
                            onLike()
                        }
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
    )
}

export default LikeButton

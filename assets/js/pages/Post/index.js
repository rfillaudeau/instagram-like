import React, {useContext, useEffect, useRef, useState} from "react"
import {Link, useNavigate, useParams} from "react-router-dom"
import axios from "axios"
import PostPlaceholder from "./PostPlaceholder"
import FollowButton from "../../components/FollowButton"
import AuthContext from "../../contexts/AuthContext"
import LikeButton from "../../components/LikeButton"
import abbreviateNumber from "../../utils/abreviateNumber"
import ShowMoreText from "../../components/ShowMoreText"
import NotFound from "../NotFound"
import PostFormModal from "../../components/PostFormModal"

function Post() {
    const {id} = useParams()
    const navigate = useNavigate()
    const [post, setPost] = useState(null)
    const [likeCount, setLikeCount] = useState(0)
    const [isNotFound, setIsNotFound] = useState(false)
    const {currentUser} = useContext(AuthContext)
    const deleteButtonRef = useRef(null)

    useEffect(() => {
        const controller = new AbortController()

        setIsNotFound(false)

        axios
            .get(`/api/posts/${id}`, {
                signal: controller.signal
            })
            .then(response => {
                console.log(response.data)
                setPost(response.data)
                setLikeCount(response.data.likeCount)
            })
            .catch(error => {
                if (!error.response) {
                    return
                }

                if (error.response.status === 404) {
                    setIsNotFound(true)
                } else {
                    console.log("Unknown error")
                }
            })

        return () => {
            controller.abort()
        }
    }, [])

    useEffect(() => {
        const listener = ({detail}) => {
            setPost(detail.post)
        }

        document.addEventListener("app:post-updated", listener)

        return () => {
            document.removeEventListener("app:post-updated", listener)
        }
    }, [])

    if (isNotFound) {
        return <NotFound/>
    }

    if (post === null) {
        return <PostPlaceholder/>
    }

    const createdAtDate = (new Date(post.createdAt)).toLocaleString()

    function handleLike(isLiked) {
        if (isLiked) {
            setLikeCount(prevLikeCount => prevLikeCount + 1)
        } else {
            setLikeCount(prevLikeCount => prevLikeCount - 1)
        }
    }

    function handleDelete(event) {
        event.preventDefault()

        if (deleteButtonRef.current === null) {
            return
        }

        deleteButtonRef.current.className += " disabled"

        axios
            .delete(`/api/posts/${id}`)
            .then(() => {
                navigate(`/${post.user.username}`)
            })
            .catch(error => {
                console.error(error)
            })
            .finally(() => {
                deleteButtonRef.current.className = deleteButtonRef.current.className.replace("disabled", "")
            })
    }

    let actionsButtons = null
    if (currentUser !== null && currentUser.id === post.user.id) {
        actionsButtons = (
            <div className="dropdown">
                <button className="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i className="bi bi-three-dots-vertical"></i>
                </button>
                <ul className="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="#" className="dropdown-item" data-bs-toggle="modal" data-bs-target="#editPostModal">
                            <i className="bi bi-pencil-fill"></i> Edit post
                        </a>
                        <a href="#" className="dropdown-item" ref={deleteButtonRef} onClick={handleDelete}>
                            <i className="bi bi-trash-fill"></i> Delete post
                        </a>
                    </li>
                </ul>
            </div>
        )
    }

    return (
        <>
            <div className="card">
                <div className="card-body">
                    <div className="row">
                        <div className="col-7 pe-2">
                            <img
                                src={`/uploads/posts/${post.pictureFilename}`}
                                className="rounded img-fluid"
                                alt="..."
                            />
                        </div>
                        <div className="col-5 d-flex flex-column">
                            <div className="d-flex mb-3">
                                <div className="align-self-center">
                                    <Link to={`/${post.user.username}`}>
                                        <img
                                            src="/default_avatar.jpg"
                                            className="rounded img-fluid avatar-sm"
                                            alt="test"
                                        />
                                    </Link>
                                </div>
                                <div className="align-self-center ps-1 fw-semibold small flex-fill mx-3">
                                    <Link to={`/${post.user.username}`}
                                          className="text-decoration-none link-dark">
                                        {post.user.username}
                                    </Link>
                                </div>
                                <div>
                                    {actionsButtons}
                                    <FollowButton user={post.user} className="btn-sm"/>
                                </div>

                            </div>
                            <p className="mb-3 flex-fill">
                                <ShowMoreText text={post.description}/>
                            </p>
                            <div className="d-flex">
                                <div className="me-3">
                                    <LikeButton
                                        post={post}
                                        onLike={() => handleLike(true)}
                                        onUnlike={() => handleLike(false)}
                                    />
                                </div>
                                <div className="flex-fill align-self-center">
                                    {abbreviateNumber(likeCount)} like{likeCount > 1 ? "s" : ""}
                                </div>
                                <div className="align-self-end">
                                    <small className="text-muted">{createdAtDate}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <PostFormModal modalId="editPostModal" post={post}/>
        </>
    )
}

export default Post

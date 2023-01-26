import React, {useEffect, useState} from "react"
import {CanceledError} from "axios"
import useForceUpdate from "../../hooks/useForceUpdate"
import PostCard from "./PostCard"
import PostCardPlaceholder from "./PostCardPlaceholder"
import {useAuth} from "../../contexts/AuthContext"

function Feed() {
    const {api} = useAuth()
    const postsPerPage = 5
    const postsPlaceholder = [...Array(postsPerPage).keys()]
    const [posts, setPosts] = useState(postsPlaceholder)
    const [page, setPage] = useState(1)
    const [canLoadMore, setCanLoadMore] = useState(false)
    const {updateState, forceUpdate} = useForceUpdate()

    useEffect(() => {
        const listener = () => {
            setPage(1)
            setPosts(postsPlaceholder)

            forceUpdate()
        }

        document.addEventListener("app:post-created", listener)

        return () => {
            document.removeEventListener("app:post-created", listener)
        }
    }, [])

    useEffect(() => {
        const controller = new AbortController()

        setCanLoadMore(false)

        api.get("/posts/feed", {
            signal: controller.signal,
            params: {
                page: page,
                itemsPerPage: postsPerPage
            }
        }).then(response => {
            const newPosts = response.data

            setPosts(prevPosts => {
                let p = [
                    ...prevPosts,
                    ...newPosts
                ]

                if (page === 1) {
                    p = newPosts
                }

                return p
            })

            setCanLoadMore(newPosts.length >= postsPerPage)
        }).catch(error => {
            if (error instanceof CanceledError) {
                return
            }

            console.error(error)
        })

        return () => {
            controller.abort()
        }
    }, [page, updateState])

    function loadNewPage() {
        setPage(prevPage => prevPage + 1)
    }

    const postElements = posts.map((post, index) => (
        <div key={index} className="mb-3">
            {post.id === undefined ? <PostCardPlaceholder/> : <PostCard post={post}/>}
        </div>
    ))

    let loadMoreButton = null
    if (canLoadMore) {
        loadMoreButton = (
            <button
                type="button"
                className="btn btn-outline-secondary w-100"
                onClick={loadNewPage}
            >
                Load more
            </button>
        )
    }

    return (
        <div className="container">
            <div className="row justify-content-center">
                <div className="col-8">
                    <button
                        type="button"
                        className="btn btn-primary w-100 mb-3"
                        data-bs-toggle="modal"
                        data-bs-target="#createPostModal"
                    >
                        New post
                    </button>

                    {postElements.length > 0 ? postElements : <div className="text-center mb-3">No posts</div>}

                    {loadMoreButton}
                </div>
            </div>
        </div>
    )
}

export default Feed

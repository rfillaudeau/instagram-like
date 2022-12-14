import React, {useEffect, useRef, useState} from "react"
import PostCard from "../components/PostCard"
import axios, {CanceledError} from "axios"
import useForceUpdate from "../hooks/useForceUpdate"

function Feed() {
    const postsPerPage = 5
    const [posts, setPosts] = useState([])
    const [page, setPage] = useState(1)
    const [canLoadMore, setCanLoadMore] = useState(true)
    const loadMoreButtonRef = useRef(null)
    const {updateState, forceUpdate} = useForceUpdate()

    useEffect(() => {
        const listener = ({detail}) => {
            // const { name } = detail;
            console.log(detail)
            console.log("event received")

            setPage(1)
            setPosts([])
            setCanLoadMore(true)

            forceUpdate()
        }

        document.addEventListener("app:post-created", listener)

        return () => {
            document.removeEventListener("app:post-created", listener)
        }
    }, [])

    useEffect(() => {
        if (loadMoreButtonRef !== null) {
            loadMoreButtonRef.current.disabled = true
        }

        const controller = new AbortController()

        axios
            .get("/api/posts/feed", {
                signal: controller.signal,
                params: {
                    page: page,
                    itemsPerPage: postsPerPage
                }
            })
            .then(response => {
                const newPosts = response.data

                setPosts(prevPosts => [
                    ...prevPosts,
                    ...newPosts
                ])

                if (newPosts.length < postsPerPage) {
                    setCanLoadMore(false)
                }
            })
            .catch(error => {
                if (error instanceof CanceledError) {
                    return
                }

                console.error(error)
            })
            .finally(() => {
                if (loadMoreButtonRef !== null) {
                    loadMoreButtonRef.current.disabled = false
                }
            })

        return () => {
            controller.abort()
        }
    }, [page, updateState])

    function loadNewPage() {
        setPage(prevPage => prevPage + 1)
    }

    if (user === null) {
        return (
            <div>
                Loading...
            </div>
        )
    }

    const postElements = posts.map((post, index) => (
        <div key={index} className="row justify-content-center my-3">
            <div className="col-auto">
                <PostCard post={post} />
            </div>
        </div>
    ))

    let loadMoreButton = null
    if (canLoadMore) {
        loadMoreButton = (
            <button
                type="button"
                className="btn btn-outline-secondary w-100 my-3"
                ref={loadMoreButtonRef}
                onClick={loadNewPage}
            >
                Load more
            </button>
        )
    }

    return (
        <main className="p-2">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-5">
                        <button
                            type="button"
                            className="btn btn-primary w-100 my-3"
                            data-bs-toggle="modal"
                            data-bs-target="#createPostModal"
                        >
                            New post
                        </button>

                        {postElements}

                        {loadMoreButton}
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Feed

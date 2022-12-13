import React, {useEffect, useRef, useState} from "react"
import PostCard from "../components/PostCard"
import PostForm from "./PostForm"
import axios, {CanceledError} from "axios"
import useForceUpdate from "../hooks/useForceUpdate"
import PostFormModal from "../components/PostFormModal"

function Feed() {
    const postsPerPage = 5
    const [posts, setPosts] = useState([])
    const [page, setPage] = useState(1)
    const [canLoadMore, setCanLoadMore] = useState(true)
    const loadMoreButtonRef = useRef(null)
    const {updateState, forceUpdate} = useForceUpdate()

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
                className="btn btn-outline-secondary w-100"
                ref={loadMoreButtonRef}
                onClick={loadNewPage}
            >
                Load more
            </button>
        )
    }

    function handlePostCreation() {
        setPage(1)
        setPosts([])
        setCanLoadMore(true)

        forceUpdate()
    }

    return (
        <main className="p-2">
            <div className="container">


                <div className="row justify-content-center">
                    <div className="col-5">
                        {/*<PostForm onCreate={handlePostCreation} />*/}

                        <button
                            type="button"
                            className="btn btn-primary w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop"
                        >
                            New post
                        </button>

                        {postElements}

                        {loadMoreButton}
                    </div>
                </div>

                <PostFormModal/>
            </div>
        </main>
    )
}

export default Feed

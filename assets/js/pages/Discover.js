import React, {useEffect, useRef, useState} from "react"
import PostPreview from "../components/PostPreview"
import axios, {CanceledError} from "axios"

function Discover() {
    const postsPerPage = 9
    const [posts, setPosts] = useState([])
    const [page, setPage] = useState(1)
    const [canLoadMore, setCanLoadMore] = useState(true)
    const loadMoreButtonRef = useRef(null)

    useEffect(() => {
        if (loadMoreButtonRef !== null) {
            loadMoreButtonRef.current.disabled = true
        }

        const controller = new AbortController()

        axios
            .get("/api/posts/discover", {
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
    }, [page])

    function loadNewPage() {
        setPage(prevPage => prevPage + 1)
    }

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

    const postElements = posts.map((post, index) => (
        <div key={index} className="col-4 p-2">
            <PostPreview post={post} />
        </div>
    ))

    return (
        <main className="p-2">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="row">
                            {postElements}

                            {loadMoreButton}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Discover

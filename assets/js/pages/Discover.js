import React, {useEffect, useState} from "react"
import PostPreview from "../components/PostPreview"
import axios, {CanceledError} from "axios"
import PostPreviewPlaceholder from "../components/PostPreviewPlaceholder"

function Discover() {
    const postsPerPage = 9
    const postsPlaceholder = [...Array(postsPerPage).keys()]
    const [posts, setPosts] = useState(postsPlaceholder)
    const [page, setPage] = useState(1)
    const [canLoadMore, setCanLoadMore] = useState(false)

    useEffect(() => {
        setCanLoadMore(false)

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
            })
            .catch(error => {
                if (error instanceof CanceledError) {
                    return
                }

                console.error(error)
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
                className="btn btn-outline-secondary w-100 my-3"
                onClick={loadNewPage}
            >
                Load more
            </button>
        )
    }

    let postElements = posts.map((post, index) => (
        <div key={index} className="col-4 p-2">
            {post.id === undefined ? <PostPreviewPlaceholder /> : <PostPreview post={post} />}
        </div>
    ))

    return (
        <main className="p-2">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="row">
                            {postElements.length > 0 ? postElements : <div className="text-center">No posts</div>}

                            {loadMoreButton}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Discover

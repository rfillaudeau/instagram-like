import React, {useEffect, useRef, useState} from "react"
import PostCard from "../components/PostCard"
import PostForm from "./PostForm"
import axios from "axios"

function Feed() {
    const [posts, setPosts] = useState([])
    const [page, setPage] = useState(1)
    const loadMoreButtonRef = useRef(null)

    useEffect(() => {
        loadMoreButtonRef.current.disabled = true

        const controller = new AbortController()

        axios
            .get("/api/posts/feed", {
                signal: controller.signal,
                params: {
                    page: page
                }
            })
            .then(response => {
                // TODO: Fix double set on start (due to StrictMode)
                setPosts(prevPosts => [
                    ...prevPosts,
                    ...response.data
                ])
            })
            .catch(error => {
                console.log(error)
                console.log("Unknown error")
            })
            .finally(() => {
                loadMoreButtonRef.current.disabled = false
            })

        return () => {
            controller.abort()
        }
    }, [page])

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

    return (
        <main className="p-2">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-5">
                        <PostForm />

                        {postElements}

                        <button
                            type="button"
                            className="btn btn-outline-secondary w-100"
                            ref={loadMoreButtonRef}
                            onClick={loadNewPage}
                        >
                            Load more
                        </button>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Feed

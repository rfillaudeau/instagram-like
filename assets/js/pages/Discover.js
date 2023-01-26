import React, {useEffect, useState} from "react"
import PostPreview from "../components/PostPreview"
import {CanceledError} from "axios"
import PostPreviewPlaceholder from "../components/PostPreviewPlaceholder"
import useForceUpdate from "../hooks/useForceUpdate"
import {useAuth} from "../contexts/AuthContext"

function Discover() {
    const {api} = useAuth()
    const postsPerPage = 9
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
        setCanLoadMore(false)

        const controller = new AbortController()

        api.get("/posts", {
            signal: controller.signal,
            params: {
                page: page,
                itemsPerPage: postsPerPage,
                order: {
                    createdAt: "desc"
                },
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

    let loadMoreButton = null
    if (canLoadMore) {
        loadMoreButton = (
            <button
                type="button"
                className="btn btn-outline-secondary w-100 mt-3"
                onClick={loadNewPage}
            >
                Load more
            </button>
        )
    }

    let postElements = posts.map((post, index) => (
        <div key={index} className="col-4 p-2">
            {post.id === undefined ? <PostPreviewPlaceholder/> : <PostPreview post={post}/>}
        </div>
    ))

    return (
        <>
            <div className="row">
                {postElements.length > 0 ? postElements : <div className="text-center">No posts</div>}
            </div>

            {loadMoreButton}
        </>
    )
}

export default Discover

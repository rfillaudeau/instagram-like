import React, {useEffect, useRef, useState} from "react"
import PostPreview from "../../components/PostPreview"
import axios, {CanceledError} from "axios"

function ProfilePosts({user}) {
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
            .get(`/api/users/${user.username}/posts`, {
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

    const postElements = posts.map((post, index) => (
        <div key={index} className="col-4 p-2">
            <PostPreview post={post} />
        </div>
    ))

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

    return (
        <div className="row mx-0">
            {postElements}

            {loadMoreButton}
        </div>
    )
}

export default ProfilePosts

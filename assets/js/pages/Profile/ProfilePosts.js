import React, {useEffect, useState} from "react"
import PostPreview from "../../components/PostPreview"
import {CanceledError} from "axios"
import PostPreviewPlaceholder from "../../components/PostPreviewPlaceholder"
import {useAuth} from "../../contexts/AuthContext"

function ProfilePosts({user}) {
    const postsPerPage = 9
    const postsPlaceholder = [...Array(postsPerPage).keys()]
    const {api} = useAuth()
    const [posts, setPosts] = useState(postsPlaceholder)
    const [page, setPage] = useState(1)
    const [canLoadMore, setCanLoadMore] = useState(false)

    useEffect(() => {
        setCanLoadMore(false)

        const controller = new AbortController()

        api.get(`/users/${user.username}/posts`, {
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
    }, [page])

    const postElements = posts.map((post, index) => (
        <div key={index} className="col-4 p-2">
            {post.id === undefined ? <PostPreviewPlaceholder/> : <PostPreview post={post}/>}
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

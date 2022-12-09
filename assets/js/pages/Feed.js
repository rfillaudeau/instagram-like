import React from "react"
import PostCard from "../components/PostCard"

function Feed() {
    const posts = [1, 2, 3, 4, 5, 6].map((post, index) => (
        <div key={index} className="row justify-content-center">
            <div className="col-auto">
                <PostCard />
            </div>
        </div>
    ))

    return (
        <main className="p-2">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-5">
                        {posts}
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Feed

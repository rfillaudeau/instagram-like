import React from "react"
import PostPreview from "../components/PostPreview"

function Discover() {
    let posts = []
    for (let i = 0; i < 22; i++) {
        posts.push({
            id: i,
            picture: "/doge.jpg"
        })
    }

    const postElements = posts.map((post, index) => (
        <div key={index} className="col p-2">
            <PostPreview />
        </div>
    ))

    return (
        <main className="p-2">
            <div className="container">
                <div className="row row-cols-4">
                    {postElements}
                </div>
            </div>
        </main>
    )
}

export default Discover

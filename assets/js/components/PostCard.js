import React from "react"
import {Link} from "react-router-dom"

function PostCard() {
    const post = {
        id: 1,
        description: "Test maggle",
        likesCount: 2,
        picture: "/doge.jpg",
        user: "chii"
    }

    return (
        <div className="card m-3">
            {/*<img src="https://picsum.photos/600" className="card-img-top" alt="Test" />*/}
            <div className="card-header">
                <Link to="/@username" className="d-flex text-decoration-none">
                    <img src="/doge.jpg" className="rounded img-fluid" alt="test" style={{width: 25}} />
                    <div className="align-self-center ps-1 fw-semibold small link-dark">username</div>
                </Link>
            </div>

            <img src="/doge.jpg" className="img-fluid" alt="..." />

            <div className="card-body">

                <div className="d-flex align-items-center pb-2">
                    <div>
                        <i className="bi bi-heart fs-4"></i>
                    </div>
                    <div className="ms-auto">
                        1,593 likes
                    </div>
                </div>

                {/*<h5 className="card-title">Card title</h5>*/}
                <p className="card-text">This is a wider card with supporting text below as a natural lead-in to
                    additional content. This content is a little bit longer.</p>
            </div>

            <div className="card-footer">
                <small className="text-muted">One day ago</small>
            </div>
        </div>
    )
}

export default PostCard

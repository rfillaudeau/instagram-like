import React from "react"
import {Link, useParams} from "react-router-dom"

function Post() {
    const {id} = useParams()

    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="card mb-2">
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-7">
                                        <img src="/doge.jpg" className="rounded img-fluid" alt="..."/>
                                    </div>
                                    <div className="col-5">
                                        <Link to="/@username" className="d-flex text-decoration-none">
                                            <img src="/doge.jpg" className="rounded img-fluid" alt="test"
                                                 style={{width: 25}}/>
                                            <div className="align-self-center ps-1 fw-semibold small link-dark">username
                                            </div>
                                            Follow
                                        </Link>
                                        <p className="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                            eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                            veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                                            dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                                            sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                        <p>Date</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Post

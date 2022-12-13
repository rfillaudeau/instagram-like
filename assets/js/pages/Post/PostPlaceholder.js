import React from "react"

function PostPlaceholder() {
    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="card mb-2">
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-7 placeholder-glow pe-2">
                                        <div className="rounded img-fluid bg-dark placeholder square"></div>
                                    </div>
                                    <div className="col-5 placeholder-glow">
                                        <div className="d-flex mb-3">
                                            <div className="rounded img-fluid bg-dark avatar-sm placeholder align-self-center"></div>
                                            <div className="align-self-center ps-1 fw-semibold small flex-fill mx-3">
                                                <span className="placeholder col-12"></span>
                                            </div>
                                            <a href="#" className="btn btn-primary disabled placeholder col-4"></a>
                                        </div>

                                        <p className="mb-3">
                                            <span className="placeholder col-7"></span>
                                            <span className="placeholder col-4"></span>
                                            <span className="placeholder col-4"></span>
                                            <span className="placeholder col-6"></span>
                                            <span className="placeholder col-8"></span>
                                        </p>

                                        <p>
                                            <span className="placeholder col-7"></span>
                                        </p>
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

export default PostPlaceholder

import React from "react"

function PostPlaceholder() {
    return (
        <div className="card">
            <div className="card-body">
                <div className="row">
                    <div className="col-7 placeholder-glow pe-2">
                        <div className="rounded img-fluid bg-dark placeholder square"></div>
                    </div>
                    <div className="col-5 d-flex flex-column placeholder-glow">
                        <div className="d-flex mb-3">
                            <div className="rounded img-fluid bg-dark avatar-sm placeholder align-self-center"></div>
                            <div className="align-self-center ps-1 fw-semibold small flex-fill mx-3">
                                <span className="placeholder col-12"></span>
                            </div>
                            <a href="#" className="btn btn-sm btn-primary disabled placeholder col-3"></a>
                        </div>

                        <p className="mb-3 flex-fill">
                            <span className="placeholder col-7 me-2"></span>
                            <span className="placeholder col-4 me-2"></span>
                            <span className="placeholder col-4 me-2"></span>
                            <span className="placeholder col-6 me-2"></span>
                            <span className="placeholder col-8"></span>
                        </p>

                        <div className="d-flex">
                            <a href="#" className="btn btn-danger disabled placeholder col-1 me-3"></a>
                            <div className="flex-fill align-self-center">
                                <span className="placeholder col-4"></span>
                            </div>
                            <div className="align-self-end placeholder col-4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default PostPlaceholder

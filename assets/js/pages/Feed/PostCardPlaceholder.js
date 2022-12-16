import React from "react"

function PostCardPlaceholder() {
    return (
        <div className="card">
            <div className="p-3 d-flex placeholder-glow">
                <div>
                    <div className="rounded img-fluid bg-dark avatar-sm placeholder align-self-center"></div>
                </div>

                <div className="align-self-center ms-3 flex-fill">
                    <span className="placeholder col-4"></span>
                </div>
            </div>

            <div className="placeholder-glow">
                <div className="img-fluid bg-dark placeholder square"></div>
            </div>

            <div className="card-body placeholder-glow">
                <div className="d-flex align-items-center mb-3">
                    <div className="fs-4 text-decoration-none">
                        <a href="#" className="btn btn-danger disabled placeholder me-3 col-3"></a>
                    </div>
                    <div className="flex-fill">
                        <span className="placeholder col-4"></span>
                    </div>
                </div>

                <p className="mb-3 text-justify">
                    <span className="placeholder col-7 me-2"></span>
                    <span className="placeholder col-4 me-2"></span>
                    <span className="placeholder col-4 me-2"></span>
                    <span className="placeholder col-6 me-2"></span>
                    <span className="placeholder col-8"></span>
                </p>

                <small className="text-muted"><span className="placeholder col-4"></span></small>
            </div>
        </div>
    )
}

export default PostCardPlaceholder

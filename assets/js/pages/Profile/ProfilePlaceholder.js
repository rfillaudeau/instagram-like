import React from "react"
import FollowButton from "../../components/FollowButton"
import abbreviateNumber from "../../utils/abreviateNumber"
import nl2br from "../../utils/nl2br"
import PostPreviewPlaceholder from "../../components/PostPreviewPlaceholder"

function ProfilePlaceholder() {
    const postElements = [...Array(9).keys()].map((post, index) => (
        <div key={index} className="col-4 p-2">
            <PostPreviewPlaceholder/>
        </div>
    ))

    return (
        <>
            <div className="card mb-2">
                <div className="card-body placeholder-glow">
                    <div className="d-flex">
                        <div className="me-3">
                            <div className="rounded img-fluid bg-dark avatar-lg placeholder align-self-center"></div>
                        </div>

                        <div className="flex-fill">
                            <div className="d-flex mb-3">
                                <div className="flex-fill fs-2 fw-semibold d-flex">
                                    <span className="placeholder col-4 align-self-center"></span>
                                </div>
                                <a href="#" className="btn btn-primary disabled placeholder col-2 align-self-center"></a>
                            </div>

                            <div className="row mb-3">
                                <div className="col">
                                    <b><span className="placeholder col-1"></span></b> <span className="placeholder col-4"></span>
                                </div>
                                <div className="col text-center">
                                    <b><span className="placeholder col-1"></span></b> <span className="placeholder col-4"></span>
                                </div>
                                <div className="col text-end">
                                    <b><span className="placeholder col-1"></span></b> <span className="placeholder col-4"></span>
                                </div>
                            </div>

                            <p className="mb-0">
                                <span className="placeholder col-7 me-2"></span>
                                <span className="placeholder col-4 me-2"></span>
                                <span className="placeholder col-4 me-2"></span>
                                <span className="placeholder col-6 me-2"></span>
                                <span className="placeholder col-8"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="row mx-0">
                {postElements}
            </div>
        </>
    )
}

export default ProfilePlaceholder

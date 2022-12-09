import React from "react"
import {useParams} from "react-router-dom"

function PostForm() {
    const params = useParams()
    const isEdit = "id" in params

    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="card mb-3">
                            <div className="card-body">
                                <form>
                                    <h1 className="h3 mb-4">{isEdit ? "Edit post" : "New post"}</h1>

                                    <div className="mb-3">
                                        <label htmlFor="exampleInputPassword1" className="form-label">Picture</label>
                                        <input type="file" className="form-control" id="exampleInputPassword1" />
                                    </div>

                                    <div className="mb-3">
                                        <label htmlFor="exampleInputEmail1" className="form-label">Description</label>
                                        <input type="email" className="form-control" id="exampleInputEmail1" />
                                    </div>

                                    <div className="text-end">
                                        <button className="btn btn-primary" type="submit">
                                            Create post
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default PostForm

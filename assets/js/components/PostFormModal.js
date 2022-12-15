import React, {useRef, useState} from "react"
import useForm from "../hooks/useForm"
import axios from "axios"

function PostFormModal({modalId, post: defaultPost}) {
    const [post, setPost] = useState(defaultPost === undefined ? null : defaultPost)

    const defaultInputs = {
        description: post === null ? "" : post.description
    }

    const defaultFileInputs = {
        picture: []
    }

    const {inputs, setInputs, handleChange} = useForm(defaultInputs)
    const [fileInputs, setFileInputs] = useState(defaultFileInputs)
    const [error, setError] = useState("")
    const submitButtonRef = useRef(null)
    const cancelButtonRef = useRef(null)
    const fileInputRef = useRef(null)

    function handleSubmit(event) {
        event.preventDefault()

        submitButtonRef.current.disabled = true

        if (!handleValidation()) {
            submitButtonRef.current.disabled = false
            return
        }

        if (post === null) {
            handleCreate()
        } else {
            handleUpdate()
        }
    }

    function handleCreate() {
        let formData = new FormData()
        formData.append("picture", fileInputs.picture[0])
        formData.append("description", inputs.description)

        axios.post("/api/posts", formData, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        }).then(response => {
            const customEvent = new CustomEvent("app:post-created", {
                detail: {
                    post: response.data
                }
            })
            document.dispatchEvent(customEvent)

            setInputs(defaultInputs)
            clearFileInput()

            cancelButtonRef.current.click()
        }).catch(error => {
            console.log(error)
        }).finally(() => {
            submitButtonRef.current.disabled = false
        })
    }

    function handleUpdate() {
        let formData = new FormData()

        if (fileInputs.picture.length > 0) {
            formData.append("picture", fileInputs.picture[0])
        }

        formData.append("description", inputs.description)

        axios.post(`/api/posts/${post.id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        }).then(response => {
            const customEvent = new CustomEvent("app:post-updated", {
                detail: {
                    post: response.data
                }
            })
            document.dispatchEvent(customEvent)

            setPost(response.data)

            clearFileInput()

            cancelButtonRef.current.click()
        }).catch(error => {
            console.log(error)
        }).finally(() => {
            submitButtonRef.current.disabled = false
        })
    }

    function handleValidation() {
        setError("")

        if (inputs.description.length === 0) {
            setError("The description should not be empty.")
            return false
        }

        if (fileInputs.picture.length === 0 && post === null) {
            setError("Please select a picture")
            return false
        }

        if (fileInputs.picture.length > 0) {
            const pictureFile = fileInputs.picture[0]

            if (pictureFile.size > 2000000) {
                setError("The file is too large. Allowed maximum size is 2 MB.")
                return false
            }

            if (!/^image\/\w+$/g.test(pictureFile.type)) {
                setError("The file should be an image.")
                return false
            }
        }

        return true
    }

    function handleFileChange(event) {
        const {name, files} = event.target

        setFileInputs(prevFileInputs => ({
            ...prevFileInputs,
            [name]: files
        }))
    }

    function clearFileInput() {
        setFileInputs(defaultFileInputs)
        fileInputRef.current.value = ""
    }

    let picturePreview = (
        <div className="rounded img-fluid bg-secondary square"></div>
    )
    if (post !== null || fileInputs.picture.length > 0) {
        let pictureSrc = ""
        if (fileInputs.picture.length > 0) {
            pictureSrc = URL.createObjectURL(fileInputs.picture[0])
        } else {
            pictureSrc = post.pictureFilepath
        }

        picturePreview = (
            <div>
                <img src={pictureSrc} className="rounded img-fluid" alt="..."/>
            </div>
        )
    }

    return (
        <div
            className="modal fade"
            id={modalId}
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            tabIndex="-1"
            aria-labelledby={`${modalId}Label`}
            aria-hidden="true"
        >
            <div className="modal-dialog modal-lg">
                <div className="modal-content">
                    <form onSubmit={handleSubmit}>
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id={`${modalId}Label`}>
                                {post === null ? "New post" : "Edit post"}
                            </h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div className="modal-body">
                            <div className="container-fluid">
                                <div className="row">
                                    <div className="col-5">
                                        {picturePreview}
                                    </div>
                                    <div className="col-7">
                                        <div className="mb-3">
                                            <label htmlFor="inputPicture" className="form-label">Picture</label>
                                            <input
                                                type="file"
                                                className="form-control"
                                                id="inputPicture"
                                                name="picture"
                                                onChange={handleFileChange}
                                                ref={fileInputRef}
                                            />
                                        </div>

                                        <div>
                                            <label htmlFor="inputDescription" className="form-label">Description</label>
                                            <textarea
                                                className="form-control"
                                                id="inputDescription"
                                                name="description"
                                                rows={7}
                                                value={inputs.description}
                                                onChange={handleChange}
                                            />
                                        </div>

                                        {
                                            error.length > 0 &&
                                            <div className="alert alert-danger mt-3" role="alert">
                                                {error}
                                            </div>
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="modal-footer">
                            <button
                                type="button"
                                className="btn btn-secondary"
                                data-bs-dismiss="modal"
                                ref={cancelButtonRef}
                            >
                                Cancel
                            </button>

                            <button
                                className="btn btn-primary"
                                type="submit"
                                ref={submitButtonRef}
                            >
                                {post !== null ? "Update post" : "Create post"}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    )
}

export default PostFormModal

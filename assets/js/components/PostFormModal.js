import React, {useRef, useState} from "react"
import useForm from "../hooks/useForm"
import {useParams} from "react-router-dom"
import axios from "axios"

function PostFormModal({modalId}) {
    const defaultInputs = {
        description: ""
    }

    const defaultFileInputs = {
        picture: []
    }

    const {inputs, setInputs, handleChange} = useForm(defaultInputs)
    const [fileInputs, setFileInputs] = useState(defaultFileInputs)
    const [error, setError] = useState("")
    const [success, setSuccess] = useState("")
    const submitButtonRef = useRef(null)
    const cancelButtonRef = useRef(null)
    const params = useParams()
    const isEdit = "id" in params

    function handleSubmit(event) {
        event.preventDefault()

        setSuccess("")

        submitButtonRef.current.disabled = true

        if (!handleValidation()) {
            submitButtonRef.current.disabled = false
            return
        }

        let formData = new FormData()
        formData.append("picture", fileInputs.picture[0])
        formData.append("description", inputs.description)

        axios.post("/api/posts", formData, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        }).then(response => {
            console.log(response)

            setInputs(defaultInputs)
            setFileInputs(defaultFileInputs)

            setSuccess("Post successfully created.")

            const customEvent = new CustomEvent("app:post-created", { detail: { name: 'primary' } })
            document.dispatchEvent(customEvent)

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

        if (fileInputs.picture.length === 0) {
            setError("Please select a picture")
            return false
        }

        const pictureFile = fileInputs.picture[0]

        if (pictureFile.size > 2000000) {
            setError("The file is too large. Allowed maximum size is 2 MB.")
            return false
        }

        if (!/^image\/\w+$/g.test(pictureFile.type)) {
            setError("The file should be an image.")
            return false
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
            <div className="modal-dialog">
                <div className="modal-content">
                    <form onSubmit={handleSubmit}>
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id={`${modalId}Label`}>
                                {isEdit ? "Edit post" : "New post"}
                            </h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div className="modal-body">
                            <div className="mb-3">
                                <label htmlFor="inputPicture" className="form-label">Picture</label>
                                <input
                                    type="file"
                                    className="form-control"
                                    id="inputPicture"
                                    name="picture"
                                    onChange={handleFileChange}
                                />
                            </div>

                            <div className="mb-3">
                                <label htmlFor="inputDescription" className="form-label">Description</label>
                                <textarea
                                    className="form-control"
                                    id="inputDescription"
                                    name="description"
                                    cols="3"
                                    value={inputs.description}
                                    onChange={handleChange}
                                />
                            </div>

                            {
                                error.length > 0 &&
                                <div className="alert alert-danger mb-3" role="alert">
                                    {error}
                                </div>
                            }

                            {
                                success.length > 0 &&
                                <div className="alert alert-success mb-3" role="alert">
                                    {success}
                                </div>
                            }
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
                                Create post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    )
}

export default PostFormModal

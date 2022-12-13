import React, {useRef, useState} from "react"
import {useParams} from "react-router-dom"
import axios from "axios"
import useForm from "../hooks/useForm"

function PostForm(props) {
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

            if (props.onCreate instanceof Function) {
                props.onCreate()
            }
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
        <form onSubmit={handleSubmit}>
            <h1 className="h3 mb-4">{isEdit ? "Edit post" : "New post"}</h1>

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

            <div className="text-end">
                <button
                    className="btn btn-primary"
                    type="submit"
                    ref={submitButtonRef}
                >
                    Create post
                </button>
            </div>

        </form>
    )
}

export default PostForm

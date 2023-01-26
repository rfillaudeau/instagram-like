import React, {useRef, useState} from "react"
import {useAuth} from "../../contexts/AuthContext"

function ChangeAvatarForm() {
    const {currentUser, updateUser, api} = useAuth()
    const [fileInputs, setFileInputs] = useState({
        avatar: []
    })

    const [error, setError] = useState("")
    const [success, setSuccess] = useState("")
    const submitButtonRef = useRef(null)

    function handleSubmit(event) {
        event.preventDefault()

        setSuccess("")

        submitButtonRef.current.disabled = true

        if (!handleValidation()) {
            submitButtonRef.current.disabled = false
            return
        }

        let formData = new FormData()
        formData.append("avatar", fileInputs.avatar[0])

        api.post("/update-avatar", formData, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        }).then(response => {
            if (response.data.avatarFilename == null || response.data.avatarFilepath == null) {
                setError("Unknown Error.")
                return
            }

            setSuccess("Avatar successfully updated")

            updateUser({
                avatarFilename: response.data.avatarFilename,
                avatarFilepath: response.data.avatarFilepath
            })
        }).catch(error => {
            if (!error.response) {
                return
            }

            if (error.response.status === 422) {
                setError("The file is not valid.")
            } else {
                setError("Unknown Error.")
            }
        }).finally(() => {
            submitButtonRef.current.disabled = false
        })
    }

    function handleFileChange(event) {
        const {name, files} = event.target

        setFileInputs(prevFileInputs => ({
            ...prevFileInputs,
            [name]: files
        }))
    }

    function handleValidation() {
        setError("")

        if (fileInputs.avatar.length === 0) {
            setError("Please select an avatar.")
            return false
        }

        const avatarFile = fileInputs.avatar[0]

        if (avatarFile.size > 3000000) {
            setError("The file is too large. Allowed maximum size is 3 MB.")
            return false
        }

        if (!/^image\/\w+$/g.test(avatarFile.type)) {
            setError("The file should be an image.")
            return false
        }

        return true
    }

    return (
        <form onSubmit={handleSubmit}>
            <h1 className="h3 mb-4">Change your avatar</h1>

            <div className="mb-3 d-flex">
                <img
                    src={currentUser.avatarFilepath}
                    className="rounded avatar-lg align-self-end me-3"
                    alt="Avatar large"
                />

                <img
                    src={currentUser.avatarFilepath}
                    className="rounded avatar-md align-self-end me-3"
                    alt="Avatar medium"
                />

                <img
                    src={currentUser.avatarFilepath}
                    className="rounded avatar-sm align-self-end"
                    alt="Avatar small"
                />
            </div>

            <div className="mb-3">
                <label htmlFor="inputAvatar" className="form-label">New avatar</label>
                <input
                    type="file"
                    className="form-control"
                    accept="image/*"
                    id="inputAvatar"
                    name="avatar"
                    onChange={handleFileChange}
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
                    Update avatar
                </button>
            </div>

        </form>
    )
}

export default ChangeAvatarForm

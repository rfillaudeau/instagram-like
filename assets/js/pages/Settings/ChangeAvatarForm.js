import React, {useContext, useRef, useState} from "react"
import AuthContext from "../../contexts/AuthContext"
import axios from "axios"

function ChangeAvatarForm() {
    const {currentUser, updateUser} = useContext(AuthContext)
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

        axios.post("/api/update-avatar", formData, {
            headers: {
                "Content-Type": "multipart/form-data"
            }
        }).then(response => {
            setSuccess("Avatar successfully updated")

            console.log(response.data)

            updateUser({
                avatarFilename: response.data.avatarFilename,
                avatarFilepath: response.data.avatarFilepath
            })
        }).catch(error => {
            console.error(error)
            setError("Unknown Error.")
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

        if (avatarFile.size > 2000000) {
            setError("The file is too large. Allowed maximum size is 2 MB.")
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
                    className="rounded avatar-lg align-self-lg-end me-3"
                    alt="Avatar large"
                />

                <img
                    src={currentUser.avatarFilepath}
                    className="rounded avatar-md align-self-lg-end me-3"
                    alt="Avatar medium"
                />

                <img
                    src={currentUser.avatarFilepath}
                    className="rounded avatar-sm align-self-lg-end"
                    alt="Avatar small"
                />
            </div>

            <div className="mb-3">
                <label htmlFor="inputAvatar" className="form-label">New avatar</label>
                <input
                    type="file"
                    className="form-control"
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

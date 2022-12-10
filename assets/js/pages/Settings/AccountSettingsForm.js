import React, {useContext, useRef, useState} from "react"
import AuthContext from "../../contexts/AuthContext"
import axios from "axios"
import useForm from "../../hooks/useForm"

function AccountSettingsForm() {
    const {currentUser, updateUser} = useContext(AuthContext)
    const {inputs, handleChange} = useForm({
        username: currentUser.username,
        email: currentUser.email,
        bio: currentUser.bio === null ? "" : currentUser.bio
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

        axios.patch("/api/update-account", {
            username: inputs.username,
            email: inputs.email,
            bio: inputs.bio
        }).then(() => {
            setSuccess("Account settings successfully updated")

            updateUser({
                username: inputs.username,
                email: inputs.email,
                bio: inputs.bio
            })
        }).catch(error => {
            console.error(error)
            // setError("The current password is not correct.")
        }).finally(() => {
            submitButtonRef.current.disabled = false
        })
    }

    function handleValidation() {
        setError("")

        if (inputs.username.length < 2 || inputs.username.length > 30 || !/^\w+$/g.test(inputs.username)) {
            setError("The username should have between 2 and 30 characters. It should only be composed of letters, numbers and underscores.")
            return false
        }

        if (inputs.email.length <= 0 || !/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g.test(inputs.email)) {
            setError("The email address is invalid.")
            return false
        }

        return true
    }

    return (
        <form onSubmit={handleSubmit}>
            <h1 className="h3 mb-4">Account Settings</h1>

            <div className="mb-3">
                <label htmlFor="inputUsername" className="form-label">Username</label>
                <input
                    type="text"
                    className="form-control"
                    id="inputUsername"
                    name="username"
                    value={inputs.username}
                    onChange={handleChange}
                />
            </div>

            <div className="mb-3">
                <label htmlFor="inputEmail" className="form-label">Email address</label>
                <input
                    type="email"
                    className="form-control"
                    id="inputEmail"
                    name="email"
                    value={inputs.email}
                    onChange={handleChange}
                />
            </div>

            <div className="mb-3">
                <label htmlFor="inputBio" className="form-label">Bio</label>
                <textarea
                    className="form-control"
                    id="inputBio"
                    name="bio"
                    rows="5"
                    value={inputs.bio}
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
                    Update account
                </button>
            </div>

        </form>
    )
}

export default AccountSettingsForm

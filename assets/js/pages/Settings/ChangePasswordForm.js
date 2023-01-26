import React, {useRef, useState} from "react"
import {useAuth} from "../../contexts/AuthContext"

function ChangePasswordForm() {
    const defaultInputs = {
        currentPassword: "",
        newPassword: "",
        passwordConfirmation: ""
    }
    const [inputs, setInputs] = useState(defaultInputs)
    const [error, setError] = useState("")
    const [success, setSuccess] = useState("")
    const submitButtonRef = useRef(null)
    const {api} = useAuth()

    function handleChange(event) {
        const {name, value} = event.target

        setInputs(prevInputs => ({
            ...prevInputs,
            [name]: value
        }))
    }

    function handleSubmit(event) {
        event.preventDefault()

        setSuccess("")

        submitButtonRef.current.disabled = true

        if (!handleValidation()) {
            submitButtonRef.current.disabled = false
            return
        }

        api.patch("/update-password", {
            currentPlainPassword: inputs.currentPassword,
            newPlainPassword: inputs.newPassword
        }).then(() => {
            setSuccess("Password successfully updated")
            setInputs(defaultInputs)
        }).catch(error => {
            console.error(error)
            setError("The current password is not correct.")
        }).finally(() => {
            submitButtonRef.current.disabled = false
        })
    }

    function handleValidation() {
        setError("")

        if (inputs.currentPassword.length === 0) {
            setError("The current password should not be empty.")
            return false
        }

        if (inputs.newPassword.length < 6) {
            setError("The new password is too short. It should have 6 characters or more.")
            return false
        }

        if (inputs.newPassword !== inputs.passwordConfirmation) {
            setError("The new password and its confirmation do not match.")
            return false
        }

        return true
    }

    return (
        <form onSubmit={handleSubmit} noValidate>
            <h1 className="h3 mb-4">Change your password</h1>

            <div className="mb-3">
                <label htmlFor="inputCurrentPassword" className="form-label">Current password</label>
                <input
                    type="password"
                    className="form-control"
                    id="inputCurrentPassword"
                    name="currentPassword"
                    required={true}
                    value={inputs.currentPassword}
                    onChange={handleChange}
                />
            </div>

            <div className="mb-3">
                <label htmlFor="inputNewPassword" className="form-label">New password</label>
                <input
                    type="password"
                    className="form-control"
                    id="inputNewPassword"
                    name="newPassword"
                    required={true}
                    value={inputs.newPassword}
                    onChange={handleChange}
                />
            </div>

            <div className="mb-3">
                <label htmlFor="inputConfirmPassword" className="form-label">Confirm password</label>
                <input
                    type="password"
                    className="form-control"
                    id="inputConfirmPassword"
                    name="passwordConfirmation"
                    required={true}
                    value={inputs.passwordConfirmation}
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
                    Update password
                </button>
            </div>
        </form>
    )
}

export default ChangePasswordForm

import React, {useRef, useState} from "react"
import {Link, Navigate} from "react-router-dom"
import useForm from "../hooks/useForm"
import {useAuth} from "../contexts/AuthContext"

function SignIn() {
    const {currentUser, api, updateAccessTokenData} = useAuth()
    const {inputs, handleChange} = useForm({
        email: "",
        password: ""
    })
    const [error, setError] = useState(null)
    const submitButtonRef = useRef(null)

    if (currentUser !== null) {
        return <Navigate to="/" replace/>
    }

    function handleSubmit(event) {
        event.preventDefault()

        submitButtonRef.current.disabled = true

        if (!handleValidation()) {
            submitButtonRef.current.disabled = false
            return
        }

        api.post("/auth/token", {
            email: inputs.email,
            password: inputs.password
        }).then(response => {
            updateAccessTokenData(response.data)
        }).catch(error => {
            console.error(error)

            setError("Incorrect email address or password.")
        }).finally(() => {
            submitButtonRef.current.disabled = false
        })
    }

    function handleValidation() {
        setError(null)

        if (inputs.email.length === 0) {
            setError("Please fill in the email address.")
            return false
        }

        if (inputs.password.length === 0) {
            setError("Please fill in the password.")
            return false
        }

        return true
    }

    return (
        <main className="m-auto p-5" style={{width: 500}}>
            <div className="card">
                <div className="card-body">
                    <h1 className="h3 mb-4 text-center">Sign In</h1>

                    <form onSubmit={handleSubmit} noValidate>
                        <div className="mb-3">
                            <label htmlFor="inputEmail" className="form-label">Email address</label>
                            <input
                                type="email"
                                className="form-control"
                                id="inputEmail"
                                required={true}
                                name="email"
                                value={inputs.email}
                                onChange={handleChange}
                            />
                        </div>

                        <div className="mb-3">
                            <label htmlFor="inputPassword" className="form-label">Password</label>
                            <input
                                type="password"
                                className="form-control"
                                id="inputPassword"
                                required={true}
                                name="password"
                                value={inputs.password}
                                onChange={handleChange}
                            />
                        </div>

                        {/*<div className="mb-3 form-check">*/}
                        {/*    <input type="checkbox" className="form-check-input" id="inputRemember" />*/}
                        {/*    <label className="form-check-label" htmlFor="inputRemember">Remember me</label>*/}
                        {/*</div>*/}

                        {
                            error !== null &&
                            <div className="alert alert-danger" role="alert">
                                {error}
                            </div>
                        }

                        <button
                            className="w-100 btn btn-lg btn-primary"
                            type="submit"
                            ref={submitButtonRef}
                        >
                            Sign In
                        </button>
                    </form>
                </div>
            </div>

            <div className="m-3 text-center">
                Need an account? <Link to="/sign-up">Sign Up</Link>
            </div>
        </main>
    )
}

export default SignIn

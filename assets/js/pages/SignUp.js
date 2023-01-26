import React, {useContext, useRef, useState} from "react"
import {Link, Navigate, useNavigate} from "react-router-dom"
import axios from "axios"
import AuthContext from "../contexts/AuthContext"
import useForm from "../hooks/useForm"

function SignUp() {
    const {currentUser} = useContext(AuthContext)
    const {inputs, handleChange} = useForm({
        username: "",
        email: "",
        password: "",
        passwordConfirmation: ""
    })
    const [error, setError] = useState("")
    const submitButtonRef = useRef(null)
    const navigate = useNavigate()

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

        axios.post("/api/auth/register", {
            username: inputs.username,
            email: inputs.email,
            plainPassword: inputs.password
        }).then(() => {
            navigate("/sign-in")
        }).catch(error => {
            if (!error.response) {
                return
            }

            if (error.response.status === 422) {
                setError("Username or email already used.")
            } else {
                setError("Unknown error.")
            }
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

        if (inputs.password.length < 6) {
            setError("The password is too short. It should have 6 characters or more.")
            return false
        }

        if (inputs.password !== inputs.passwordConfirmation) {
            setError("The password and its confirmation do not match.")
            return false
        }

        return true
    }

    return (
        <main className="m-auto p-5" style={{width: 500}}>
            <div className="card">
                <div className="card-body">
                    <form onSubmit={handleSubmit}>
                        <h1 className="h3 mb-4 text-center">Sign Up</h1>

                        <div className="mb-3">
                            <label htmlFor="inputUsername" className="form-label">Username</label>
                            <input
                                type="text"
                                className="form-control"
                                id="inputUsername"
                                name="username"
                                value={inputs.username}
                                onChange={handleChange}
                                required={true}
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
                                required={true}
                            />
                        </div>

                        <div className="mb-3">
                            <label htmlFor="inputPassword" className="form-label">Password</label>
                            <input
                                type="password"
                                className="form-control"
                                id="inputPassword"
                                name="password"
                                value={inputs.password}
                                onChange={handleChange}
                                required={true}
                            />
                        </div>

                        <div className="mb-3">
                            <label htmlFor="inputConfirmPassword" className="form-label">Confirm password</label>
                            <input
                                type="password"
                                className="form-control"
                                id="inputConfirmPassword"
                                name="passwordConfirmation"
                                value={inputs.passwordConfirmation}
                                onChange={handleChange}
                                required={true}
                            />
                        </div>

                        {
                            error.length > 0 &&
                            <div className="alert alert-danger mb-3" role="alert">
                                {error}
                            </div>
                        }

                        <button
                            className="w-100 btn btn-lg btn-primary"
                            type="submit"
                            ref={submitButtonRef}
                        >
                            Sign Up
                        </button>
                    </form>
                </div>
            </div>

            <div className="m-3 text-center">
                Already have an account? <Link to="/sign-in">Sign In</Link>
            </div>
        </main>
    )
}

export default SignUp

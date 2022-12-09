import React, {useRef, useState} from "react"
import {Link} from "react-router-dom"
import axios from "axios"

function SignIn() {
    const [errorMessage, setErrorMessage] = useState(null)

    const emailRef = useRef(null)
    const passwordRef = useRef(null)
    function handleSubmit(event) {
        event.preventDefault()

        setErrorMessage(null)

        axios.post("/api/login", {
            email: emailRef.current.value,
            password: passwordRef.current.value
        }).then(response => {
            location.href = "/"
        }).catch(error => {
            // console.error(error)

            setErrorMessage("Incorrect email address or password.")
        })
    }

    return (
        <main className="m-auto p-5" style={{width: 500}}>
            <div className="card">
                <div className="card-body">
                    <h1 className="h3 mb-4 text-center">Sign In</h1>

                    {
                        errorMessage !== null &&
                        <div className="alert alert-danger" role="alert">
                            {errorMessage}
                        </div>
                    }

                    <form onSubmit={handleSubmit}>
                        <div className="mb-3">
                            <label htmlFor="inputEmail" className="form-label">Email address</label>
                            <input
                                ref={emailRef}
                                type="email"
                                className="form-control"
                                id="inputEmail"
                                required={true}
                            />
                        </div>

                        <div className="mb-3">
                            <label htmlFor="inputPassword" className="form-label">Password</label>
                            <input
                                ref={passwordRef}
                                type="password"
                                className="form-control"
                                id="inputPassword"
                                required={true}
                            />
                        </div>

                        {/*<div className="mb-3 form-check">*/}
                        {/*    <input type="checkbox" className="form-check-input" id="inputRemember" />*/}
                        {/*    <label className="form-check-label" htmlFor="inputRemember">Remember me</label>*/}
                        {/*</div>*/}

                        <button className="w-100 btn btn-lg btn-primary" type="submit">Sign In</button>
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

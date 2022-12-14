import React, {useContext} from "react"
import {Link, useLocation} from "react-router-dom"
import AuthContext from "../contexts/AuthContext"

function Header() {
    const {pathname} = useLocation()
    const {currentUser} = useContext(AuthContext)

    return (
        <header className="p-3 text-bg-dark">
            <div className="container">
                <div className="d-flex flex-wrap align-items-center justify-content-start">
                    <Link to="/" className="d-flex align-items-center text-white text-decoration-none me-2">
                        <i className="bi bi-moon-stars"></i>
                    </Link>

                    <ul className="nav me-auto justify-content-center">
                        <li>
                            <Link
                                to="/"
                                className={`nav-link px-2 ${pathname === "/" ? "text-white" : "text-secondary"}`}
                            >
                                Home
                            </Link>
                        </li>
                        <li>
                            <Link
                                to="/discover"
                                className={`nav-link px-2 ${pathname === "/discover" ? "text-white" : "text-secondary"}`}
                            >
                                Discover
                            </Link>
                        </li>
                    </ul>

                    <div className="text-end justify-content-center d-flex">
                        {
                            currentUser === null ? (
                                <ul className="nav">
                                    <li className="nav-item">
                                        <Link to="/Sign-in" className="btn btn-outline-light">
                                            Sign In
                                        </Link>
                                    </li>
                                    <li className="nav-item ms-3">
                                        <Link to="/sign-up" className="btn btn-primary">Sign Up</Link>
                                    </li>
                                </ul>
                            ) : (
                                <>
                                    <ul className="nav mx-3">
                                        <li className="nav-item">
                                            <button
                                                type="button"
                                                className="btn btn-outline-primary w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#createPostModal"
                                            >
                                                New post
                                            </button>
                                        </li>
                                    </ul>

                                    <ul className="navbar-nav">
                                        <li className="nav-item dropdown">
                                            <a
                                                className="nav-link dropdown-toggle"
                                                href="#"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                {currentUser.username}
                                            </a>
                                            <ul className="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <Link to={`/@${currentUser.username}`} className="dropdown-item">
                                                        <i className="bi bi-person-circle"></i> Profile
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link to="/settings" className="dropdown-item">
                                                        <i className="bi bi-gear-fill"></i> Settings
                                                    </Link>
                                                </li>
                                                <li>
                                                    <hr className="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <a href="/sign-out" className="dropdown-item">
                                                        <i className="bi bi-power"></i> Sign Out
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </>
                            )
                        }
                    </div>
                </div>
            </div>
        </header>
    )
}

export default Header

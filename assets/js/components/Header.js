import React from "react"
import {Link, useLocation} from "react-router-dom"
import {useAuth} from "../contexts/AuthContext"

function Header() {
    const {pathname} = useLocation()
    const {currentUser, api, clearSession} = useAuth()

    function signOut(event) {
        event.preventDefault()

        api.delete("/auth/revoke")

        clearSession()
    }

    return (
        <header>
            <nav className="navbar navbar-expand navbar-dark bg-dark">
                <div className="container">
                    <Link to="/" className="text-white text-decoration-none me-2">
                        <i className="bi bi-moon-stars"></i>
                    </Link>

                    <ul className="navbar-nav flex-fill">
                        <li className="nav-item">
                            <Link
                                to="/"
                                className={`nav-link ${pathname === "/" ? "text-white" : "text-secondary"}`}
                            >
                                Home
                            </Link>
                        </li>
                        <li className="nav-item">
                            <Link
                                to="/discover"
                                className={`nav-link ${pathname === "/discover" ? "text-white" : "text-secondary"}`}
                            >
                                Discover
                            </Link>
                        </li>
                    </ul>

                    <div className="text-end justify-content-center d-flex">
                        {
                            currentUser === null ? (
                                <ul className="navbar-nav">
                                    <li className="nav-item">
                                        <Link to="/Sign-in" className="btn btn-outline-light mx-2">
                                            Sign In
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link to="/sign-up" className="btn btn-primary mx-2">
                                            Sign Up
                                        </Link>
                                    </li>
                                </ul>
                            ) : (
                                <>
                                    <ul className="navbar-nav align-items-center">
                                        <li className="nav-item">
                                            <button
                                                type="button"
                                                className="btn btn-outline-primary mx-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#createPostModal"
                                            >
                                                New post
                                            </button>
                                        </li>
                                        <li className="nav-item dropdown">
                                            <a
                                                className="nav-link py-0 d-flex"
                                                href="#"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                <div className="align-self-center me-2">
                                                    <img
                                                        src={currentUser.avatarFilePath}
                                                        className="rounded avatar-sm"
                                                        alt={`${currentUser.username}'s avatar`}
                                                    />
                                                </div>
                                                <div className="align-self-center">
                                                    {currentUser.username} <i
                                                    className="bi bi-caret-down-fill small"></i>
                                                </div>
                                            </a>
                                            <ul className="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <Link to={`/${currentUser.username}`} className="dropdown-item">
                                                        <i className="bi bi-person-circle"></i> Profile
                                                    </Link>
                                                </li>
                                                <li>
                                                    <Link to="/settings" className="dropdown-item">
                                                        <i className="bi bi-gear-fill"></i> Settings
                                                    </Link>
                                                </li>
                                                <li>
                                                    <hr className="dropdown-divider"/>
                                                </li>
                                                <li>
                                                    <a href="#" className="dropdown-item" onClick={signOut}>
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
            </nav>
        </header>
    )
}

export default Header

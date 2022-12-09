import React from "react"

function Footer() {
    const year = (new Date()).getFullYear()

    return (
        <footer className="text-muted py-2">
            <div className="container">
                <p className="mb-0">
                    &copy; IGClone {year}
                </p>
            </div>
        </footer>
    )
}

export default Footer

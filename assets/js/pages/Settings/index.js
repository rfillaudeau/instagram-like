import React from "react"
import AccountSettingsForm from "./AccountSettingsForm"
import ChangePasswordForm from "./ChangePasswordForm"

function Settings() {
    return (
        <main className="py-3">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-8">
                        <div className="card mb-3">
                            <div className="card-body">
                                <AccountSettingsForm />
                            </div>
                        </div>

                        <div className="card">
                            <div className="card-body">
                                <ChangePasswordForm />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    )
}

export default Settings

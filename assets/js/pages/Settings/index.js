import React from "react"
import AccountSettingsForm from "./AccountSettingsForm"
import ChangePasswordForm from "./ChangePasswordForm"
import ChangeAvatarForm from "./ChangeAvatarForm"

function Settings() {
    return (
        <>
            <div className="card mb-3">
                <div className="card-body">
                    <AccountSettingsForm />
                </div>
            </div>

            <div className="card mb-3">
                <div className="card-body">
                    <ChangeAvatarForm />
                </div>
            </div>

            <div className="card">
                <div className="card-body">
                    <ChangePasswordForm />
                </div>
            </div>
        </>
    )
}

export default Settings

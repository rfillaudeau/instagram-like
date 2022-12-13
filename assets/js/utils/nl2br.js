import React, {Fragment} from "react"

export default function nl2br(s) {
    if (s === null) {
        return null
    }

    return s.split("\n").map((item, key) => {
        return (
            <Fragment key={key}>
                {item}<br/>
            </Fragment>
        )
    })
}

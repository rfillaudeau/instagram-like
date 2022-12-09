import React, {Fragment} from "react"

export default function nl2br(s) {
    return s.split("\n").map((item, key) => {
        return (
            <Fragment key={key}>
                {item}<br/>
            </Fragment>
        )
    })
}

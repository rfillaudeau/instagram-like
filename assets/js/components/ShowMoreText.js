import React, {useState} from "react"
import nl2br from "../utils/nl2br"

function ShowMoreText({text, maxLength, nl2brEnabled}) {
    const [showMore, setShowMore] = useState(false)

    maxLength = parseInt(maxLength)
    if (isNaN(maxLength)) {
        maxLength = 250
    }

    if (nl2brEnabled === undefined) {
        nl2brEnabled = true
    }

    if (text && text.length <= maxLength) {
        return nl2brEnabled ? nl2br(text) : text
    }

    function showMoreToggle(event) {
        event.preventDefault()

        setShowMore(prevShowMore => !prevShowMore)
    }

    let finalText = showMore ? text : `${text.substring(0, maxLength)}...`
    if (nl2brEnabled) {
        finalText = nl2br(finalText)
    }

    return (
        <>
            {finalText}
            <a href="#" className="d-inline-block" onClick={showMoreToggle}>
                {!showMore ? "Show more" : "Show less"}
            </a>
        </>
    )
}

export default ShowMoreText

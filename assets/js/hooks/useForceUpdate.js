import React, {useState} from "react"

function useForceUpdate() {
    const [updateState, setUpdateState] = useState(0)

    function forceUpdate() {
        setUpdateState(prevState => prevState + 1)
    }

    return {updateState, forceUpdate}
}

export default useForceUpdate

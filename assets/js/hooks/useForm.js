import {useState} from "react"

function useForm(defaultInputs) {
    const [inputs, setInputs] = useState(defaultInputs)

    function handleChange(event) {
        const {name, value} = event.target

        setInputs(prevInputs => ({
            ...prevInputs,
            [name]: value
        }))
    }

    return {inputs, handleChange}
}

export default useForm

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

    return {inputs, setInputs, handleChange}
}

export default useForm

export default function abbreviateNumber(value) {
    let length = (Math.abs(parseInt(value, 10)) + '').length,
        index = Math.ceil((length - 3) / 3),
        suffix = ['k', 'm', 'b', 't']

    if (length < 4) return value

    return (value / Math.pow(1000, index))
        .toFixed(1)
        .replace(/\.0$/, '') + suffix[index - 1]
}

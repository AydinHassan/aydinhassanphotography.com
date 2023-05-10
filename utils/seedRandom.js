import seedrandom from 'seedrandom'

export const generateSeed = () => {
    const now = new Date()
    const seed = `${now.getFullYear()}${now.getMonth()}${now.getDate()}`
    return seedrandom(seed);
}
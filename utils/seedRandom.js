import seedrandom from 'seedrandom'

export const generateSeed = () => {
    const now = new Date()
    const seed = `${now.getFullYear()}${now.getMonth()}${now.getDate()}${now.getHours()}`
    console.log('SEED: ' + seed);
    return seedrandom(seed);
}
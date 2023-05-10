import portfolio from  '../images.json';

// Function to fetch an image by its name
export function getImageByName(name) {
    return portfolio.images.find((image) => image.name === name);
}

// Function to fetch all images for a given album
export function getImagesByAlbum(album) {
    return portfolio.images.filter((image) => image.albums && image.albums.includes(album));
}

// Function to fetch all images
export function getAllImages() {
    return portfolio.images;
}
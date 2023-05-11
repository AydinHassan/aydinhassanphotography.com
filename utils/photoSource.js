export function photoSourceThumb(img) {
    if (img.orientation === 'landscape') {
        return '/photos/' + img.src + '?nf_resize=fit&w=1000';
    }
    return '/photos/' + img.src + '?nf_resize=fit&h=500';
}

export function photoSourceMain(img) {
    if (img.orientation === 'landscape') {
        return '/photos/' + img.src + '?nf_resize=fit&w=2000';
    }
    return '/photos/' + img.src + '?nf_resize=fit&h=1500';
}

export function photoSourceAlbum(name) {
    return '/photos/' + name + '?nf_resize=fit&w=1000';
}
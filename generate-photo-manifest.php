<?php

require_once __DIR__ . '/vendor/autoload.php';

use Thumbhash\Thumbhash;
use function Thumbhash\extract_size_and_pixels_with_imagick;


$json = json_decode(file_get_contents(__DIR__ . '/images.json'), true);

foreach ($json['images'] as $key => $image) {
    if (!file_exists(__DIR__ . '/public/photos/' . $image['src'])) {
        echo "File not found: /public/photos/{$image['src']}\n";
        continue;
    }

    $file = new SplFileInfo(__DIR__ . '/public/photos/' . $image['src']);

    [$width, $height] = getimagesize($file->getPathname());


    if ($image['description'] !== "" && !str_ends_with($image['description'], '.')) {
        $json['images'][$key]['description'] = $image['description'] . '.';
    }

    if (str_ends_with($image['title'], '.')) {
        $json['images'][$key]['title'] = substr($image['title'], 0, -1);
    }

    $json['images'][$key]['ratio'] = getRatio($file);
    $json['images'][$key]['orientation'] = $width > $height ? 'landscape' : 'portrait';
    $json['images'][$key]['exif'] = exif($file);

    $json['images'][$key]['hash'] = getImageHash($file);

    if (!isset($image['albums'])) {
        $json['images'][$key]['albums'] = [];
    }
}

function getImageHash(SplFileInfo $file): string
{
    $thumbContent = create_thumbnail($file);

    [$width, $height, $pixels] = extract_size_and_pixels_with_imagick($thumbContent);

    $hash = Thumbhash::RGBAToHash($width, $height, $pixels);
    return implode(",", $hash);
}

function create_thumbnail(SplFileInfo $file): string
{
    $image = new Imagick($file->getPathname());

    $width = $image->getImageWidth();
    $height = $image->getImageHeight();

    $longSide = max($width, $height);

    if ($longSide == $width) {
        $newWidth = 99;
        $newHeight = 0;
    } else {
        $newHeight = 99;
        $newWidth = 0;
    }

    $image->scaleImage($newWidth, $newHeight);
    return $image->__toString();
}

function getRatio($file): string
{
    [$width, $height] = getimagesize($file->getPathname());

    $divisor = gmp_intval(gmp_gcd($width, $height));

    return ($width / $divisor) . ' / ' . ($height / $divisor);
}

function exif(SplFileInfo $file): array
{
    $data = exif_read_data($file->getPathname(), 'EXIF', true);

    $fNumber = explode("/", $data['EXIF']['FNumber']);
    $actualFNumber = $fNumber[0] / $fNumber[1];

    $fLength = explode("/", $data['EXIF']['FocalLength']);
    $actualFLength = $fLength[0] / $fLength[1];

    $date = DateTime::createFromFormat('Y:m:d H:i:s', $data['EXIF']['DateTimeOriginal']);

    $model = implode(
        ", ",
        array_filter(
            [
                mapMake($data['IFD0']['Make'] ?? null),
                $data['IFD0']['Model'] ?? null
            ]
        )
    );

    return [
        'Aperture' => 'f/' . number_format($actualFNumber, 1),
        'Exposure' => $data['EXIF']['ExposureTime'] ?? 'N/A',
        'ISO' => $data['EXIF']['ISOSpeedRatings'] ?? 'N/A',
        'Focal Length' => $actualFLength . ' mm',
        'Date Taken' => $date->format('M j, Y'),
        'Model' => $model
    ];
}

function mapMake(?string $make): ?string {
    if (null === $make) {
        return null;
    }

    $replaces = [
        'NIKON CORPORATION' => 'Nikon'
    ];

    return $replaces[$make] ?? $make;
}

file_put_contents(__DIR__ . '/images.json', json_encode($json, JSON_PRETTY_PRINT));

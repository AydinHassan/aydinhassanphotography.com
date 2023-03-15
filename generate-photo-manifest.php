<?php

$json = json_decode(file_get_contents(__DIR__ . '/src/images.json'), true);

foreach ($json['images'] as $key => $image) {
    if (!file_exists(__DIR__ . '/src/photos/' . $image['src'])) {
        echo "File not found: /src/photos/{$image['src']}\n";
        continue;
    }

    $file = new SplFileInfo(__DIR__ . '/src/photos/' . $image['src']);

    [$width, $height] = getimagesize($file->getPathname());

    $json['images'][$key]['ratio'] = getRatio($file);
    $json['images'][$key]['orientation'] = $width > $height ? 'landscape' : 'portrait';
    $json['images'][$key]['exif'] = exif($file);
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

file_put_contents(__DIR__ . '/src/images.json', json_encode($json, JSON_PRETTY_PRINT));

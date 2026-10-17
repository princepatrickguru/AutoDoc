<?php

if (!defined('AUTODOC_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

function getNearbyHealthFacilities(
    float $latitude,
    float $longitude,
    string $apiKey
): array {
    $nearbyFacilities = [];
    $facilitySource = 'unavailable';

    if ($apiKey !== '') {
        $placesPayload = json_encode([
            'includedTypes' => ['hospital', 'pharmacy'],
            'maxResultCount' => 3,
            'rankPreference' => 'DISTANCE',
            'locationRestriction' => [
                'circle' => [
                    'center' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude
                    ],
                    'radius' => 5000
                ]
            ]
        ]);
        $placesRequest = curl_init(
            'https://places.googleapis.com/v1/places:searchNearby'
        );

        if ($placesRequest === false) {
            error_log('Unable to initialize the Google Places request.');
        } else {
            curl_setopt_array($placesRequest, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $placesPayload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Goog-Api-Key: ' . $apiKey,
                    'X-Goog-FieldMask: places.displayName,places.formattedAddress,places.rating,places.googleMapsUri,places.currentOpeningHours.openNow'
                ],
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10
            ]);

            $placesResponse = curl_exec($placesRequest);
            $placesCurlError = curl_errno($placesRequest);
            $placesCurlErrorMessage = curl_error($placesRequest);
            $placesHttpCode = curl_getinfo(
                $placesRequest,
                CURLINFO_HTTP_CODE
            );
            curl_close($placesRequest);

            if ($placesResponse === false || $placesCurlError !== 0) {
                error_log(
                    'Google Places cURL error (' . $placesCurlError . '): '
                    . (
                        $placesCurlErrorMessage !== ''
                            ? $placesCurlErrorMessage
                            : 'No error message.'
                    )
                );
            } elseif ($placesHttpCode < 200 || $placesHttpCode >= 300) {
                error_log(
                    'Google Places API returned HTTP status: '
                    . $placesHttpCode
                );
            } else {
                $placesData = json_decode($placesResponse, true);

                if (is_array($placesData) && isset($placesData['places'])) {
                    foreach (
                        array_slice($placesData['places'], 0, 3)
                        as $place
                    ) {
                        if (!is_array($place)) {
                            continue;
                        }

                        $name = $place['displayName']['text'] ?? null;

                        if (!is_string($name) || $name === '') {
                            continue;
                        }

                        $facility = [
                            'name' => $name,
                            'address' => is_string(
                                $place['formattedAddress'] ?? null
                            )
                                ? $place['formattedAddress']
                                : 'Address unavailable'
                        ];

                        if (is_numeric($place['rating'] ?? null)) {
                            $facility['rating'] = $place['rating'];
                        }

                        if (
                            is_bool(
                                $place['currentOpeningHours']['openNow']
                                    ?? null
                            )
                        ) {
                            $facility['open_now'] =
                                $place['currentOpeningHours']['openNow']
                                    ? 'Open now'
                                    : 'Closed';
                        }

                        if (is_string($place['googleMapsUri'] ?? null)) {
                            $facility['map_link'] = $place['googleMapsUri'];
                        }

                        $nearbyFacilities[] = $facility;
                    }
                } elseif (
                    is_string($placesData['error']['status'] ?? null)
                ) {
                    error_log(
                        'Google Places API error: '
                        . $placesData['error']['status']
                    );
                }
            }
        }
    }

    if ($nearbyFacilities !== []) {
        return [
            'facilities' => $nearbyFacilities,
            'source' => 'google_places'
        ];
    }

    $defaultLatitude = 7.6498;
    $defaultLongitude = 5.2214;
    $earthRadiusKm = 6371;
    $latitudeDelta = deg2rad($latitude - $defaultLatitude);
    $longitudeDelta = deg2rad($longitude - $defaultLongitude);
    $distance = 2 * $earthRadiusKm * asin(sqrt(
        sin($latitudeDelta / 2) ** 2
        + cos(deg2rad($defaultLatitude))
        * cos(deg2rad($latitude))
        * sin($longitudeDelta / 2) ** 2
    ));

    if ($distance <= 30) {
        $nearbyFacilities = [
            [
                'name' => 'W.OBA PHARMACY',
                'address' => 'No. 7 Similoluwa Street, Ado-Ekiti',
                'map_link' => 'https://maps.google.com/?cid=3937341496773734567'
            ],
            [
                'name' => 'Avon Healthcare St. Gregorys Hospital',
                'address' => 'Ado-Ekiti, Ekiti State',
                'map_link' => 'https://maps.google.com/?cid=16669797505921945222'
            ],
            [
                'name' => 'Blue Dove Pharmacy',
                'address' => 'Shop 6, Rosehanna Mall, Adebayo, Ado-Ekiti',
                'map_link' => 'https://maps.google.com/?cid=11688206956195472074'
            ]
        ];

        $facilitySource = 'local_fallback';
    }

    return [
        'facilities' => $nearbyFacilities,
        'source' => $facilitySource
    ];
}

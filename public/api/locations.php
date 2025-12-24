<?php
declare(strict_types=1);

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

$basePath = __DIR__ . '/../../app/data/';

switch ($type) {

    case 'countries':
        echo file_get_contents($basePath . 'countries.json');
        break;

    case 'states':
        $country = $_GET['country'] ?? '';
        $states = json_decode(file_get_contents($basePath . 'states.json'), true);

        $filtered = array_values(array_filter($states, fn($s) =>
            $s['country_code'] === $country
        ));

        echo json_encode($filtered);
        break;

    case 'cities':
        $country = $_GET['country'] ?? '';
        $state   = $_GET['state'] ?? '';

        $cities = json_decode(file_get_contents($basePath . 'cities.json'), true);

        $filtered = array_values(array_filter($cities, fn($c) =>
            $c['country_code'] === $country &&
            $c['state_code'] === $state
        ));

        echo json_encode($filtered);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
}

<?php

declare(strict_types = 1);

namespace App\Helpers;

class CoordinateHelper
{
    private const EARTH_RADIUS = 6371000;  // Earth's radius in meters

    function distanceBetweenCoordinates(
        float $latitudeFrom, 
        float $longitudeFrom, 
        float $latitudeTo, 
        float $longitudeTo): float
    {
        // Convert degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
    
        // Calculate delta of latitudes and longitudes
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
    
        // Haversine formula
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        // Distance in meters
        return self::EARTH_RADIUS * $c;
    }
}
<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Helpers\CoordinateHelper;
use App\Repositories\PostcodeRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function __construct(
        protected CoordinateHelper $coordinateHelper, 
        protected PostcodeRepository $postcodeRepository,
        protected ShopRepository $shopRepository
    ) {}

    public function create(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'open' => 'required|boolean',
            'type' => 'required|string|max:255',
            'max_delivery_distance' => 'required|integer|min:0',
        ]);

        // Create a new shop record
        $shop = Shop::create([
            'id' => (string) Str::uuid(), // Generate a UUID
            'name' => $validatedData['name'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
            'open' => $validatedData['open'],
            'type' => $validatedData['type'],
            'max_delivery_distance' => $validatedData['max_delivery_distance'],
        ]);

        // Return the created shop with a 201 status
        return response()->json($shop, 201);
    }    

    public function show(Request $request, string $id)
    {
        // Find the shop by its UUID
        $shop = Shop::find($id);

        // If shop is not found, return a 404 error response
        if (!$shop) {
            return response()->json(['message' => 'Shop not found'], 404);
        }

        // Return the shop data as JSON
        return response()->json($shop, 200);
    }

    public function nearest(Request $request, string $postcode, ?int $metres = 5000)
    {   
        // get the coordinates of the passed postcode
        // loop through shops calcating distance from postcode coordinates.
        // build collection of near shops

        if(!$postcode = $this->postcodeRepository->findPostcodeByCode($postcode)) {
            return response()->json(['message' => 'Postcode not found'], 404);
        }

        $shops = $this->shopRepository->getAllShops();

        $nearestShops = LazyCollection::make(function () use ($shops, $postcode, $metres) {
            foreach($shops as $shop) {
                $distanceInMetres = $this->coordinateHelper->distanceBetweenCoordinates(
                    $shop['latitude'], $shop['longitude'], $postcode['latitude'], $postcode['longitude']
                );

                if ($distanceInMetres < $metres) {
                    $shop['distanceFromPostcode'] = ceil($distanceInMetres);
                    yield $shop;
                }
            }
        });

        $nearestShops = $nearestShops->sortBy('distanceFromPostcode');

        return response()->json($nearestShops, 200);
    }

    public function deliverTo(Request $request, string $postcode)
    {   
        // get the coordinates of the passed postcode
        // loop through shops calcating distance from postcode coordinates.
        // build collection of near shops

        if(!$postcode = $this->postcodeRepository->findPostcodeByCode($postcode)) {
            return response()->json(['message' => 'Postcode not found'], 404);
        }

        $shops = $this->shopRepository->getAllShops();

        $nearestShops = LazyCollection::make(function () use ($shops, $postcode) {
            foreach($shops as $shop) {
                $distanceInMetres = $this->coordinateHelper->distanceBetweenCoordinates(
                    $shop['latitude'], $shop['longitude'], $postcode['latitude'], $postcode['longitude']
                );

                if ($distanceInMetres < $shop['max_delivery_distance']) {
                    $shop['distanceFromPostcode'] = ceil($distanceInMetres);
                    yield $shop;
                }
            }
        });

        $nearestShops = $nearestShops->sortBy('distanceFromPostcode');

        return response()->json($nearestShops, 200);
    }

}

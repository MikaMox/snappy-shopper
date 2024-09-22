<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use ZipArchive;

class ImportPostcodes extends Command
{
    protected $signature = 'postcodes:import {--url=https://parlvid.mysociety.org/os/ONSPD/2022-11.zip} {--csv=Data/ONSPD_NOV_2022_UK.csv} {--skip=1} {--limit=1000}';
    protected $description = 'Download a ZIP file containing a CSV of postcodes and import it into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Download the ZIP file
        $url = $this->option('url');
        $csvFileName = $this->option('csv');
        $skip = $this->option('skip');
        $limit = $this->option('limit');
        $zipFilePath = storage_path('app/postcodes.zip');

        $this->info('Downloading ZIP file...');
        $response = Http::get($url);

        if ($response->status() === 200) {
            file_put_contents($zipFilePath, $response->body());
            $this->info('Download complete.');
        } else {
            $this->error('Failed to download the file.');
            return;
        }

        // Extract the CSV file from the ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) === TRUE) {
            $this->info('Extracting ZIP file...');
            $zip->extractTo(storage_path('app'), $csvFileName);
            $zip->close();
            $this->info('Extraction complete.');
        } else {
            $this->error('Failed to extract ZIP file.');
            return;
        }

        $csvFilePath = storage_path('app/' . $csvFileName);

        // Import the CSV data into the database
        if (file_exists($csvFilePath)) {
            $this->info('Importing CSV data into the database...');

            try {
                set_time_limit(0);
                //It would probably best to either batch process this file or use the already batched csv data.
                LazyCollection::make(function () use ($csvFilePath) {
                    $file = fopen($csvFilePath, 'r');
                    while ($data = fgetcsv($file)) {
                        yield $data;
                    }
                })->skip($skip)->take($limit)->each(function ($data) {
                    // Indexes of the columns in the csv.  These probably should be configurable
                    $postcode = $data[0];
                    $latitude = $data[42];
                    $longitude = $data[43];

                    // Insert the data into the database
                    DB::table('postcodes')->insert([
                        'postcode' => str_replace(' ', '', $postcode),
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ]);
                });
                $this->info('Postcode import complete.');
            } catch (Exception) {
                $this->error('Failed to open CSV file.');
            }
        } else {
            $this->error('CSV file not found.');
        }
    }
}

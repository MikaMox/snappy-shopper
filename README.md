# Snappy Shopper Code Task

The purpose of this code is to fulfill the following requirements
- Create a command to download the mySociety poscode data
- Create a controller to add new stores to a database
- Create a controller with an API response to return stores near a postcode
- Create a controller action that can return stores that deliver to a certain postcode

## Installation

The database is a sqlite database, which can be initiated using 

```
php artisan migrate:fresh
```

## Postcode Import Command

The postcode import is performed by using the following command.
```
php artisan postcodes:import
```

### Command line options

The uri of the poscode zip and the default value
```
--url=https://parlvid.mysociety.org/os/ONSPD/2022-11.zip
```
The csv location within the extracted zip
```
--csv=Data/ONSPD_NOV_2022_UK.csv
```
The skip option starts the rows importing at a certain row. Skipping the first row by default omits the header row
```
--skip=1
```
The total rows to import.  The file is a large csv and this allows us to insert in chunks.  Though as the import uses a generator it will do the whole file in a single command.
```
--limit=1000
```

## Endpoints

There are 4 endpoints that have been implemented that can be accessed on localhost.

The first endpoint creates a shop. The delivery distance is in metres.  This will return the new shop record with the generated UUID.

```
POST: http://127.0.0.1:8000/api/shop

example payload:
{
    "name": "Local Shop",
    "latitude": 57.13894,
    "longitude": -2.075326,
    "open": true,
    "type": "grocery",
    "max_delivery_distance": 10000
}
```

The shop details endpoint returns the shop when the shop UUID is provided
```
GET: http://127.0.0.1:8000/api/shop/{shopId}
```

The nearest shops endpoint will return a list of shops in distance order.  It is limited to 10km by default, but this can be overridden by providing the optional metres parameter

This will find shops within 200 metres of the supplied postcode
```
GET: http://127.0.0.1:8000/api/nearest/AB13PX/200
```
This will find shops within 10km of the supplied postcode 
```
GET: http://127.0.0.1:8000/api/nearest/AB13PX
```

The deliver to endpoint uses the supplied max delivery distance value that is supplied when creating a shop.
```
GET: http://127.0.0.1:8000/api/delivers/AB13PX
```
## Test Data
Below are 3 shops that I used for testing.  Shop 3 is 0 metres from `AB13PX` the others are within a couple hundred metres. 
```
{
    "name": "Local Shop",
    "latitude": 57.138555,
    "longitude": -2.073574,
    "open": true,
    "type": "grocery",
    "max_delivery_distance": 10000
}

{
    "name": "Local Shop 2",
    "latitude": 57.137907,
    "longitude": -2.074448,
    "open": true,
    "type": "grocery",
    "max_delivery_distance": 10000
}

{
    "name": "Local Shop 3",
    "latitude": 57.13894,
    "longitude": -2.075326,
    "open": true,
    "type": "grocery",
    "max_delivery_distance": 10000
}


```
<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use ExternalBusiness\Service\GooglePlacesService;
use ExternalBusiness\Service\YelpService;

$google_service = new GooglePlacesService();
print_r($google_service->getBusiness("ChIJSdAnxzW562sRIOYKGpXDXUE"));

$yelp_service = new YelpService();
print_r($yelp_service->getBusiness("G5WCd5IFO31MoCbiFGiDCQ"));

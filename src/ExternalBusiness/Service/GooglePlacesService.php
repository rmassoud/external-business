<?php namespace ExternalBusiness\Service;

use ExternalBusiness\Model\ExternalBusiness;
use ExternalBusiness\Model\ExternalBusinessReview;
use SKAgarwal\GoogleApi\PlacesApi;

class GooglePlacesService
{

    /**
     * @var array
     */
    private $dayMap = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        0 => 'Sunday',
    ];


    private $google_places;

    private $key = 'AIzaSyAocvDCgiUaD9078kspYreZf0aMQSY9A3E';

    public function __construct()
    {
        if($this->google_places == null) {
            $this->google_places = new PlacesApi($this->key);
        }
    }

    public function getProviderName()
    {
        return "google";
    }

    public function findMatch($query, $longitude, $latitude)
    {
        $results = $this->google_places->placeAutocomplete($query, [
            'location' => "$longitude,$latitude",
            'radius' => 500,
            'strictbounds' => ''
        ]);
        if($results['status'] == 'OK') {
            return $results['predictions'][0]['place_id'];
        } else {
            return null;
        }
    }

    public function getBusiness($uuid, $params = [])
    {
        try {
            $result = $this->google_places->placeDetails($uuid);
            if($result) {
                $business = new ExternalBusiness();
                $business->setId($uuid);
                $business->setProviderName($this->getProviderName());
                $business->setData($result);
                $business->setName($result['result']['name']);
                $business->setOpeningHours( $this->getOpeningHours($result));
                $business->setOpenNow($result['result']['opening_hours']['open_now']);
                $business->setReviews( $this->getReviews($result));
                $business->setState($this->getAddressComponent($result, 'administrative_area_level_1'));
                $business->setCity($this->getAddressComponent($result, 'locality'));
                $business->setFormattedAddress( $this->getFormattedAddress($result) );
                $business->setCategories($this->getTags($result));
                $business->setRating($result['result']['rating']);
                $business->setPhone($result['result']['international_phone_number']);
                $business->setDisplayPhone($result['result']['formatted_phone_number']);
                $business->setLatitude($result['result']['geometry']['location']['lat']);
                $business->setLongitude($result['result']['geometry']['location']['lng']);
                $business->setWebsite($result['result']['website']);
                $business->setPrice(3); //todo
                $business->setPhotos($this->getPhotos($result));

                return $business;
            }
        } catch (ContextErrorException $e) {
            return null;
        }
    }

    private function getOpeningHours($result) {
        if($result['result']['opening_hours']) {
            foreach($this->dayMap as $dayIndex => $day) {
                $openingHours[$dayIndex] = [
                    'day_name' => $day,
                ];
                foreach( $periods = $result['result']['opening_hours']['periods'] as $key => $period ) {
                    if($period['close']['day'] == $dayIndex) {
                        $openingHours[$dayIndex]['periods'][] = [
                            'open_time' => date_format(date_create_from_format ("Hi", $period['open']['time']), 'h:ia'),
                            'close_time' => date_format(date_create_from_format ("Hi", $period['close']['time']), 'h:ia'),
                        ];
                    }
                }
            }
            return $openingHours;
        }
        return null;
    }

    private function getReviews($business) {
        $reviews = [];
        foreach($business['result']['reviews'] as $review_data) {
            $review = new ExternalBusinessReview();
            $review->setAuthorName($review_data['author_name']);
            $review->setAuthorUrl($review_data['author_url']);
            $review->setProfilePhotoUrl($review_data['profile_photo_url']);
            $review->setRating($review_data['rating']);
            $review->setText($review_data['text']);
            $review->setTime($review_data['time']);
            $review->setRelativeTimeDescription($review_data['relative_time_description']);
            $reviews[] = $review;
        }
        return $reviews;
    }

    private function getAddressComponent($result, $component)
    {
        if(isset($result['result']['address_components'])) {
            foreach( $result['result']['address_components'] as $address_component) {
                if(in_array($component, $address_component['types'])) {
                    return $address_component['short_name'];
                }
            }
        }
        return null;
    }

    private function getFormattedAddress($result) {
        if(isset($result['result']['formatted_address'])) {
            return explode(', ', $result['result']['formatted_address']);
        }
        return null;
    }

    private function getTags($result) {
        $tags = [];
        foreach($result['result']['types'] as $type) {
            $tags[] = [
                'alias' => $type,
                'title' => ucwords( str_replace("_", " ", $type) ),
            ];
        }
        return $tags;
    }

    private function getPhotos($result) {
        $photos = [];
        foreach($result['result']['photos'] as $photo) {
            $photo_reference = $photo['photo_reference'];
            $photos[] = "//maps.googleapis.com/maps/api/place/photo?photoreference=$photo_reference&key=$this->key";
        }
        return $photos;
    }

}

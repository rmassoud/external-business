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

    private $key;
    private $include_reviews;

    public function __construct($key, $proxy = FALSE, $include_reviews = FALSE, $stack = FALSE)
    {
        $this->key = $key;
        $this->include_reviews = $include_reviews;

        if($this->google_places == null) {
            $this->google_places = new PlacesApi($this->key, $proxy, $stack);
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
                $business->setName(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $result['result']['name'])); // Remove unicode characters
                $business->setOpeningHours( $this->getOpeningHours($result));
                $business->setOpenNow($this->getOpenNow($result));
                $business->setReviews( $this->include_reviews ? $this->getReviews($result) : null);
                $business->setReviewsNumber(count($this->getReviews($result)));
                $business->setState($this->getAddressComponent($result, 'administrative_area_level_1'));
                $business->setCity($this->getAddressComponent($result, 'locality'));
                $business->setFormattedAddress( $this->getFormattedAddress($result) );
                $business->setCategories($this->getTags($result));
                $business->setRating(
                    isset($result['result']['rating']) ? $result['result']['rating']: null
                );
                $business->setPhone(
                    isset($result['result']['international_phone_number']) ? $result['result']['international_phone_number'] : null
                );
                $business->setDisplayPhone(
                    isset($result['result']['formatted_phone_number']) ? $result['result']['formatted_phone_number'] :null
                );
                $business->setLatitude($result['result']['geometry']['location']['lat']);
                $business->setLongitude($result['result']['geometry']['location']['lng']);
                $business->setWebsite(
                    isset($result['result']['website']) ? $result['result']['website'] : null
                );
                $business->setPhotos($this->getPhotos($result));
                $business->setUrl($result['result']['url']);

                $business->setImage(
                    $business->getPhotos() ? $business->getPhotos()[0] : NULL
                );

                $business->setLocality($this->getAddressComponent($result, 'locality'));
                $business->setPostcode($this->getAddressComponent($result, 'postal_code'));

                return $business;
            }
        } catch (ContextErrorException $e) {
            return null;
        }
    }

    private function getOpeningHours($result) {
        if(!isset($result['result']['opening_hours'])) {
            return null;
        }

        foreach($this->dayMap as $dayIndex => $day) {
            $openingHours[$dayIndex] = [
                'day_name' => $day,
            ];
            foreach( $periods = $result['result']['opening_hours']['periods'] as $key => $period ) {
                if( isset($period['open']) && isset($period['close']) && $period['open']['day'] == $dayIndex) {
                    $openingHours[$dayIndex]['periods'][] = [
                        'open_time' => date_format(date_create_from_format ("Hi", $period['open']['time']), 'h:ia'),
                        'close_time' => date_format(date_create_from_format ("Hi", $period['close']['time']), 'h:ia'),
                    ];
                }
            }
        }
        return $openingHours;


    }

    private function getReviews($business) {
        if( !isset($business['result']['reviews'])) {
            return null;
        }
        $reviews = [];
        foreach($business['result']['reviews'] as $review_data) {
            $review = new ExternalBusinessReview();
            $review->setAuthorName($review_data['author_name']);
            $review->setAuthorUrl( isset($review_data['author_url']) ? $review_data['author_url'] : null);
            $review->setProfilePhotoUrl( isset($review_data['profile_photo_url']) ? $review_data['profile_photo_url'] : null);
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
        if(!isset($result['result']['address_components'])) {
            return null;
        }
        foreach( $result['result']['address_components'] as $address_component) {
            if(in_array($component, $address_component['types'])) {
                return $address_component['short_name'];
            }
        }

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
        if(isset($result['result']['photos'])) {
            $photos = [];
            foreach($result['result']['photos'] as $photo) {
                $photo_reference = $photo['photo_reference'];
                $photos[] = "//maps.googleapis.com/maps/api/place/photo?photoreference=$photo_reference&key=$this->key";
            }
            return $photos;
        }
        return null;
    }

    private function getOpenNow($result) {
        return isset($result['result']['opening_hours'])
            && isset($result['result']['opening_hours']['open_now']) ?
            $result['result']['opening_hours']['open_now'] : null;
    }

}

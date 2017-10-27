<?php namespace ExternalBusiness\Service;

use ExternalBusiness\Model\ExternalBusiness;
use ExternalBusiness\Model\ExternalBusinessReview;
use Stevenmaguire\OAuth2\Client\Provider\Yelp;
use Stevenmaguire\Yelp\Exception\HttpException;
use Stevenmaguire\Yelp\v3\Client;


class YelpService
{

    private $client;

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

    private $include_reviews;

    public function __construct($client_id, $client_secret, $proxy = FALSE, $include_reviews = FALSE)
    {
        if($this->client == null) {
            $provider = new Yelp([
                'clientId'          => $client_id,
                'clientSecret'      => $client_secret
            ]);
            $this->client = new Client(array(
                'accessToken' => (string) $provider->getAccessToken('client_credentials'),
                'apiHost' => 'api.yelp.com'
            ));
        }
        $this->include_reviews = $include_reviews;
    }

    public function getProviderName()
    {
        return "yelp";
    }

    /**
     * @param $query
     * @param $longitude
     * @param $latitude
     * @return null
     */
    public function findMatch($query, $longitude, $latitude)
    {
        $results = $this->client->getAutocompleteResults( [
            'text' => $this->removeStopdWords($query),
            'latitude' => $longitude,
            'longitude' => $latitude,
            'locale' => 'en_AU',
        ]);

        if( !empty($results->businesses) ) {
            return $results->businesses[0]->id;
        } else {
            return null;
        }
    }

    /**
     * @param $uuid
     * @param array $params
     * @return null|\Stevenmaguire\Yelp\v3\stdClass
     */
    public function getBusiness($uuid, $params = [])
    {
        try {
            $result = $this->client->getBusiness($uuid, $params);
            $business = new ExternalBusiness();
            $business->setId($uuid);
            $business->setProviderName($this->getProviderName());
            $business->setData($result);
            $business->setName($result->name);
            $business->setOpeningHours( $this->getOpeningHours($result));
            $business->setOpenNow( isset($result->hours) ? $result->hours[0]->is_open_now : null);
            $business->setReviews( $this->include_reviews ? $this->getReviews($uuid) : null);
            $business->setRating($this->getRating($business->getReviews()));
            $business->setState($result->location->state);
            $business->setCity($result->location->city);
            $business->setFormattedAddress($result->location->display_address);
            $business->setCategories((array) $result->categories);
            $business->setPhone($result->phone);
            $business->setDisplayPhone($result->display_phone);
            $business->setLatitude($result->coordinates->latitude);
            $business->setLongitude($result->coordinates->longitude);
            $business->setPrice(isset($result->price) ? strlen($result->price) * 5 / 4 : null);
            $business->setPhotos($this->getPhotos($result));
            $business->setUrl($this->url);
//            $business->setOpenNow(!$result->is_closed);

            return $business;

        } catch(HttpException $e) {
            return null;
        }
        return null;
    }

    /**
     * @param $uuid
     * @param array $params
     * @return null|\Stevenmaguire\Yelp\v3\stdClass
     */
    public function getReviews($uuid, $params = []) {
        try
        {
            $result = $this->client->getBusinessReviews($uuid, $params);

            $reviews = [];
            foreach($result->reviews as $review_data) {
                $review = new ExternalBusinessReview();
                $review->setAuthorName($review_data->user->name);
                $review->setProfilePhotoUrl($review_data->user->image_url);
                $review->setRating($review_data->rating);
                $review->setText($review_data->text);
                $review->setTime( strtotime($review_data->time_created));
                $reviews[] = $review;
            }

            return $reviews;
        }
        catch (HttpException $e) {
            return null;
        }
    }

    /**
     * @param $query
     * @return mixed
     */
    protected function removeStopdWords($query)
    {
        $stop_words = array("a", "an", "the");
        return trim(preg_replace('/\b('.implode('|', $stop_words ).')\b/i','', $query));
    }

    private function getOpeningHours($business)
    {
        if(isset($business->hours)) {
            $openingHours = [];
            foreach($this->dayMap as $dayIndex => $day) {
                $openingHours[$dayIndex] = [
                    'day_name' => $day,
                    'periods' => [],
                ];
                foreach( $periods = $business->hours[0]->open as $key => $period ) {
                    if($period->day == $dayIndex) {
                        $openingHours[$dayIndex]['periods'][] = [
                            'open_time' => date_format(date_create_from_format ("Hi", $period->start), 'h:ia'),
                            'close_time' => date_format(date_create_from_format ("Hi", $period->end), 'h:ia'),
                        ];
                    }
                }
            }
            return $openingHours;
        }
        return null;
    }

    private function getRating($reviews)
    {
        $score = 0;
        if($reviews !== null) {
            foreach($reviews as $review) {
                $score += $review->getRating();
            }
            return $score/sizeof($reviews);
        }
        return null;
    }

    private function getPhotos($result)
    {
        $photos = [];
        foreach($result->photos as $photo){
            $photos[] = preg_replace('#^https?://#', '//', $photo);
        }
        return $photos;
    }

}

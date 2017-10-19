<?php namespace Codestream\ExternalBusiness\Model;

class ExternalBusiness
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $provider_name;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $opening_hours;

    /**
     * @var bool
     */
    private $open_now;

    /**
     * @var array
     */
    private $reviews;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var array
     */
    private $formatted_address;

    /**
     * @var float
     */
    private $rating;

    /**
     * @var float
     */
    private $price;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $display_phone;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var string
     */
    private $website;

    /**
     * @var array
     */
    private $photos;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->provider_name;
    }

    /**
     * @param string $provider_name
     */
    public function setProviderName($provider_name)
    {
        $this->provider_name = $provider_name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getOpeningHours()
    {
        return $this->opening_hours;
    }

    /**
     * @param array $openingHours
     */
    public function setOpeningHours($opening_hours)
    {
        $this->opening_hours = $opening_hours;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function isOpenNow()
    {
        return $this->open_now;
    }

    /**
     * @param bool $open_now
     */
    public function setOpenNow($open_now)
    {
        $this->open_now = $open_now;
    }

    /**
     * @return Array
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param Array $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function getFormattedAddress()
    {
        return $this->formatted_address;
    }

    /**
     * @param array $formatted_address
     */
    public function setFormattedAddress($formatted_address)
    {
        $this->formatted_address = $formatted_address;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getDisplayPhone()
    {
        return $this->display_phone;
    }

    /**
     * @param mixed $display_phone
     */
    public function setDisplayPhone($display_phone)
    {
        $this->display_phone = $display_phone;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param mixed $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * PHP Magic method to get properties by calling var name
     *
     * @param $property
     * @return mixed
     */
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
}

<?php namespace ExternalBusiness\Model;


class ExternalBusinessReview
{

    /**
     * @var string
     */
    private $author_name;

    /**
     * @var string
     */
    private $author_url;

    /**
     * @var string
     */
    private $profile_photo_url;

    /**
     * @var float
     */
    private $rating;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @var string
     */
    private $relative_time_description;

    /**
     * @var string
     */
    private $review_url;

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->author_name;
    }

    /**
     * @param string $author_name
     */
    public function setAuthorName($author_name)
    {
        $this->author_name = $author_name;
    }

    /**
     * @return string
     */
    public function getAuthorUrl()
    {
        return $this->author_url;
    }

    /**
     * @param string $author_url
     */
    public function setAuthorUrl($author_url)
    {
        $this->author_url = $author_url;
    }

    /**
     * @return string
     */
    public function getProfilePhotoUrl()
    {
        return $this->profile_photo_url;
    }

    /**
     * @param string $profile_photo_url
     */
    public function setProfilePhotoUrl($profile_photo_url)
    {
        $this->profile_photo_url = $profile_photo_url;
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
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getReviewUrl()
    {
        return $this->review_url;
    }

    /**
     * @param string $review_url
     */
    public function setReviewUrl($review_url)
    {
        $this->review_url = $review_url;
    }

    /**
     * @return string
     */
    public function getRelativeTimeDescription()
    {
        return $this->relative_time_description;
    }

    /**
     * @param string $relative_time_description
     */
    public function setRelativeTimeDescription($relative_time_description)
    {
        $this->relative_time_description = $relative_time_description;
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

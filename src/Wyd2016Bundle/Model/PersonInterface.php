<?php

namespace Wyd2016Bundle\Model;

use DateTime;

/**
 * Model
 */
interface PersonInterface
{
    /** @const string */
    const SEX_MALE = 'm';

    /** @const string */
    const SEX_FEMALE = 'f';

    /** @const integer */
    const SHIRT_SIZE_XS = 1;

    /** @const integer */
    const SHIRT_SIZE_S = 2;

    /** @const integer */
    const SHIRT_SIZE_M = 3;

    /** @const integer */
    const SHIRT_SIZE_L = 4;

    /** @const integer */
    const SHIRT_SIZE_XL = 5;

    /** @const integer */
    const SHIRT_SIZE_XXL = 6;

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set first name
     *
     * @param string $firstName first name
     *
     * @return self
     */
    public function setFirstName($firstName);

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set last name
     *
     * @param string $lastName last name
     *
     * @return self
     */
    public function setLastName($lastName);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get birth date
     *
     * @return DateTime
     */
    public function getBirthDate();

    /**
     * Set birth date
     *
     * @param DateTime|null $birthDate birth date
     *
     * @return self
     */
    public function setBirthDate(DateTime $birthDate = null);

    /**
     * Get sex
     *
     * @return string
     */
    public function getSex();

    /**
     * Set sex
     *
     * @param string $sex sex
     *
     * @return self
     */
    public function setSex($sex);

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry();

    /**
     * Set country
     *
     * @param integer $country country
     *
     * @return self
     */
    public function setCountry($country);

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress();

    /**
     * Set address
     *
     * @param string $address address
     *
     * @return self
     */
    public function setAddress($address);

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone();

    /**
     * Set phone
     *
     * @param string $phone phone
     *
     * @return self
     */
    public function setPhone($phone);

    /**
     * Get e-mail
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set e-mail
     *
     * @param string $email e-mail
     *
     * @return self
     */
    public function setEmail($email);

    /**
     * Get shirt size
     *
     * @return integer|null
     */
    public function getShirtSize();

    /**
     * Set shirt size
     *
     * @param integer|null $shirtSize shirt size
     *
     * @return self
     */
    public function setShirtSize($shirtSize = null);
}

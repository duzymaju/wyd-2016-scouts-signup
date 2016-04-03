<?php

namespace Wyd2016Bundle\Model;

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
     * Get last name
     *
     * @return string
     */
    public function getLastName();

    /**
     * Get birth date
     *
     * @return DateTime
     */
    public function getBirthDate();

    /**
     * Get sex
     *
     * @return string
     */
    public function getSex();

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry();

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress();

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone();

    /**
     * Get e-mail
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get shirt size
     *
     * @return integer|null
     */
    public function getShirtSize();
}

<?php

namespace Wyd2016Bundle\Model;

/**
 * Model
 */
interface PersonInterface
{
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
}

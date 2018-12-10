<?php
namespace Liquid\Object;

use Liquid\Drop;

class Address extends Drop {

    private $name;
    private $first_name;
    private $last_name;
    private $address1;
    private $address2;
    private $street;
    private $company;
    private $city;
    private $province;
    private $province_code;
    private $zip;
    private $country;
    private $country_code;
    private $phone;

    public function __construct()
    {
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @param mixed $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @param mixed $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @param mixed $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * @param mixed $province_code
     */
    public function setProvinceCode($province_code)
    {
        $this->province_code = $province_code;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @param mixed $country_code
     */
    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function name() {
        return $this->name ? $this->name : '';
    }

    public function first_name() {
        return $this->first_name ? $this->first_name : '';
    }

    public function last_name() {
        return $this->last_name ? $this->last_name : '';
    }

    public function address1() {
        return $this->address1 ? $this->address1 : '';
    }

    public function address2() {
        return $this->address2 ? $this->address2 : '';
    }

    public function street() {
        $address1 = $this->address1();
        $address2 = $this->address2();
        return $address1 ? ($address2 ? $address1 . ', ' . $address2 : $address1) : $address2;
    }

    public function company() {
        return $this->company ? $this->company : '';
    }

    public function city() {
        return $this->city ? $this->city : '';
    }

    public function province() {
        return $this->province ? $this->province : '';
    }

    public function province_code() {
        return $this->province_code ? $this->province_code : '';
    }

    public function zip() {
        return $this->zip ? $this->zip : '';
    }

    public function country() {
        return $this->country ? $this->country : '';
    }

    public function country_code() {
        return $this->country_code ? $this->country_code : '';
    }

    public function phone() {
        return $this->phone ? $this->phone : '';
    }
}
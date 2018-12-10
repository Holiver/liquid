<?php
namespace Liquid\Object;

use Liquid\Drop;

class Form extends Drop {

    public $id;
    public $author;
    public $body;
    public $email;
    public $errors;
    public $set_as_default_checkbox;
    public $first_name;
    public $last_name;
    public $name;
    public $company;
    public $address1;
    public $address2;
    public $city;
    public $province;
    public $country;
    public $zip;
    public $phone;
    public $telephone;
    public $posted_successfully;
    public $password_needed;

    public function id() {
        return $this->id;
    }

    public function author() {
        return $this->author ? $this->author : '';
    }

    public function body() {
        return $this->body ? $this->body : '';
    }

    public function email() {
        return $this->email ? $this->email : '';
    }

    public function errors() {
        return $this->errors ? $this->errors : '';
    }

    public function set_as_default_checkbox() {
        return $this->set_as_default_checkbox ? $this->set_as_default_checkbox : '';
    }

    public function first_name() {
        return $this->first_name ? $this->first_name : '';
    }

    public function last_name() {
        return $this->last_name ? $this->last_name : '';
    }

    public function company() {
        return $this->company ? $this->company : '';
    }

    public function address1() {
        return $this->address1 ? $this->address1 : '';
    }

    public function address2() {
        return $this->address2 ? $this->address2 : '';
    }

    public function city() {
        return $this->city ? $this->city : '';
    }

    public function province() {
        return $this->province ? $this->province : '';
    }

    public function country() {
        return $this->country ? $this->country : '';
    }

    public function zip() {
        return $this->zip ? $this->zip : '';
    }

    public function phone() {
        return $this->phone ? $this->phone : '';
    }

    public function posted_successfully() {
        return $this->posted_successfully ? $this->posted_successfully : '';
    }

    public function password_needed() {
        return $this->password_needed ? $this->password_needed : '';
    }

    public function telephone() {
        return $this->telephone ? $this->telephone : '';
    }

    public function name() {
        return $this->name ? $this->name : '';
    }
}
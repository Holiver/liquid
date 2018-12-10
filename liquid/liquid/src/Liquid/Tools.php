<?php
/**
 * Created by IntelliJ IDEA.
 * User: heliwen
 * Date: 2018/12/10
 * Time: 下午2:39
 */
namespace Liquid;

class Tools {

    public static function getName($firstName, $lastName) {
        $firstName = $firstName ? $firstName : '';
        $lastName = $lastName ? $lastName : '';
        if ($firstName && $lastName)
            return $lastName.' '.$firstName;
        return $firstName ? $firstName : $lastName;
    }
}
<?php
namespace Liquid\Filter;

class MathFilters {

    /** math abs
     * @param array $input
     * @return number
     */
    public static function abs($input) {
        return abs($input);
    }

}

//$filters = new MathFilters();
//echo $filters::abs(-1.2);
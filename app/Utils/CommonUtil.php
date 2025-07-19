<?php
namespace App\Utils;

class CommonUtil{
    public static function getMaxPage($total, $perPage){
        return ceil($total / $perPage);
    }
}

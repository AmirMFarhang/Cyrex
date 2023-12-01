<?php
namespace HezarDastan;

class Dast
{
    public function IsPostSafely($str)
    {
        if(empty($str) || $str == null)
        {
            return false;
        }
        return true;
    }
}
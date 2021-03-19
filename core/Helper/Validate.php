<?php

namespace Core\Helper;

class Validate
{
    public function required($string)
    {
        return !empty($this->trimInput($string));
    }
    
    public function trimInput($input)
    {
        $data = trim($input);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    public function date($date)
    {
        $regex = '/^[1-2]{1}\d{3}-[0-9]{1}[1-9]{1}-[0-9]{2}$/i';
        return preg_match($regex, $date);
    }    
    
}
<?php

namespace Core\Controller;

use Core\Traits\View;

class BaseController
{
    use View;
    
    public function redirect($uri)
    {
        $hostName = app('request')->protocol.'://'.app('request')->hostname;
        header("Location: $hostName".$uri);
    }
}
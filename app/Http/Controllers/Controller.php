<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function urlWith($input, $key, $value)
    {
        return url()->current() . '?' . http_build_query(\array_set($input, $key, $value));
    }

    protected function urlWithout($input, $key)
    {
        return url()->current() . '?' . http_build_query(\array_except($input, $key));
    }
}

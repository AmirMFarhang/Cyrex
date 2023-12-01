<?php
namespace App\Controllers;
use App\Entities;

class ExampleController extends AppController
{

    function index()
    {
        $this->before();
        print_r("indexed");
        $this->Response->end();
    }
}

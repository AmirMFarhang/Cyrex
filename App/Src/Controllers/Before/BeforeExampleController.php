<?php

namespace App\Controllers\Before;

use App\Controllers\AppController;

class BeforeExampleController extends AppController
{

    public function index()
    {
        print_r("before");
    }

}
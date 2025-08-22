<?php

namespace App\Controllers;

class Template extends BaseController
{
    public function index()
    {
        return view('template'); // This loads app/Views/template.php
    }
}

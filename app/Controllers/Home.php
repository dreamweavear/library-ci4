<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('site/home', [
            'title' => 'Brilient Brains Library',
        ]);
    }

    public function about()
    {
        return view('site/about', [
            'title' => 'About · Brilient Brains Library',
        ]);
    }
}
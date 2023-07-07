<?php

namespace App\Http\Controllers;

use App\Services\AlkesService;

class HomeController extends Controller
{
    private $alkesService;
    public function __construct(AlkesService $alkesService){
        $this->alkesService = $alkesService;
    }

    public function index(){
        $alkes = $this->alkesService->getAlkes();
        return view('home.index', compact('alkes'));
    }
}

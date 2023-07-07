<?php

namespace App\Services;

use App\Models\Alkes;

class AlkesService{
    public function getAlkes(){
        return Alkes::orderBy('name')->get();
    }
}
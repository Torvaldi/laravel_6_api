<?php

namespace App\Services;

use Carbon\Carbon;

class DateService {

    public function getCurrentTimestamps() : string
    {
        return Carbon::now()->toDateTimeString();
    }
    
}
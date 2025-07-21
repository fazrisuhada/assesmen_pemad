<?php

namespace App\Helpers;

use Carbon\Carbon;

class Formatter
{
    public static function formatDateTime($datetime, $timezone = 'Asia/Jakarta', $format = 'd-m-Y H:i:s')
    {
        if (!$datetime) {
            return null;
        }

        return Carbon::parse($datetime)
            ->setTimezone($timezone)
            ->format($format);
    }
}

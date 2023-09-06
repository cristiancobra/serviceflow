<?php

namespace App\Services;

use DateTime;

class DateConversionService
{
    public static function convertJavascriptDate($date)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/', $date)) {
            return date('Y-m-d H:i:s', strtotime($date));
        } else {
            return $date;
        }
    }

    public static function calculateDurationTime($start, $end)
    {
        $startDateTime = new DateTime($start);
        $endDateTime = new DateTime($end);

        $duration = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();

        return $duration;
    }

    public static function calculateDurationDays($dateStart, $dateConclusion)
    {
        $formattedDateStart = new DateTime($dateStart);
        $formattedDateConclusion = new DateTime($dateConclusion);

        $duration = $formattedDateStart->diff($formattedDateConclusion);
        
        return $duration->days + 1;
    }
}

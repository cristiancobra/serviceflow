<?php

namespace App\Services;

use DateTime;
use DateTimeZone;

class DateConversionService
{
    public static function convertJavascriptDate($date)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/', $date)) {
            // Remover os milissegundos antes de converter
            $dateWithoutMilliseconds = preg_replace('/\.\d{3}Z$/', 'Z', $date);
            
            // Criar um objeto DateTime a partir da string de data UTC
            $utcDateTime = new DateTime($dateWithoutMilliseconds, new DateTimeZone('UTC'));
            
            // Garantir que a data esteja em UTC
            $utcDateTime->setTimezone(new DateTimeZone('UTC'));
    
            // Formatar a data como 'Y-m-d H:i:s'
            return $utcDateTime->format('Y-m-d H:i:s');
            // return date_default_timezone_set('UTC');
        } else {
            return $date;
        }
    }
    
    public static function convertToUtc($date)
    {
        // Verificar se a data está no formato ISO 8601
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{3})?Z$/', $date) ||
            preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{3})?[+-]\d{2}:\d{2}$/', $date)) {
    
            try {
                // Criar um objeto DateTime a partir da string de data
                $dateTime = new \DateTime($date);
                
                // Definir o fuso horário como UTC
                $dateTime->setTimezone(new \DateTimeZone('UTC'));
    
                // Formatar a data como 'Y-m-d H:i:s'
                return $dateTime->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // Retornar a data original em caso de erro
                return $date;
            }
        } else {
            // Retornar a data original se não corresponder ao formato ISO 8601
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

<?php

namespace App\Services;

use DateTime;
use DateTimeZone;

class DateTimeConversionService
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
    
    // Converte uma data/hora de um fuso horário específico para UTC para salvar no banco de dados
    public static function convertToUtc(string $dateTime, string $timezone = 'America/Sao_Paulo'): string
    {
        try {
            $dt = new \DateTime($dateTime, new \DateTimeZone($timezone));
            $dt->setTimezone(new \DateTimeZone('UTC'));
    
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $dateTime;
        }
    }

    // Converte uma data/hora de UTC para um fuso horário do usuario para exibição
    public static function convertFromUtc(
        ?string $dateTime,
        string $timezone = 'America/Sao_Paulo'
    ): ?string {
        if (!$dateTime) {
            return null;
        }
    
        try {
            $dt = new \DateTime($dateTime, new \DateTimeZone('UTC'));
            $dt->setTimezone(new \DateTimeZone($timezone));
    
            return $dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $dateTime;
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

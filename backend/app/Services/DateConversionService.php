<?php

namespace App\Services;

use DateTime;
use DateTimeZone;

class DateConversionService
{
    /**
     * Converte uma data simples (YYYY-MM-DD) do timezone do usuário para UTC
     * 
     * @param string $date Data no formato YYYY-MM-DD
     * @param string $timezone Timezone de origem (padrão: America/Sao_Paulo)
     * @return string Data convertida no formato Y-m-d
     */
    public static function convertToUtc($date, $timezone = 'America/Sao_Paulo')
    {
        // Se for data simples (YYYY-MM-DD)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            try {
                // Criar um objeto DateTime a partir da string de data no timezone do usuário
                $dateTime = new DateTime($date, new DateTimeZone($timezone));
                
                // Converter para UTC
                $dateTime->setTimezone(new DateTimeZone('UTC'));
        
                // Formatar a data como 'Y-m-d' (sem hora)
                return $dateTime->format('Y-m-d');
            } catch (\Exception $e) {
                // Retornar a data original em caso de erro
                return $date;
            }
        } else {
            // Retornar a data original se não corresponder ao formato esperado
            return $date;
        }
    }
}

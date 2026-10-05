<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cpf implements Rule
{
    /**
     * Valida os dígitos verificadores do CPF (espera só os 11 dígitos).
     */
    public function passes($attribute, $value)
    {
        if (!preg_match('/^\d{11}$/', (string) $value) || preg_match('/^(\d)\1{10}$/', $value)) {
            return false;
        }

        for ($position = 9; $position < 11; $position++) {
            $sum = 0;
            for ($i = 0; $i < $position; $i++) {
                $sum += $value[$i] * (($position + 1) - $i);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $value[$position] !== $digit) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'O CPF informado não é válido.';
    }
}

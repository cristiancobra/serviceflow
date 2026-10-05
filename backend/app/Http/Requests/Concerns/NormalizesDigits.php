<?php

namespace App\Http\Requests\Concerns;

trait NormalizesDigits
{
    /**
     * Remove pontuação dos campos informados (ex: "123.456.789-09" → "12345678909"),
     * para que o usuário possa digitar CPF, CEP e códigos com ou sem máscara.
     * Valor vazio vira null.
     */
    protected function normalizeDigits(array $fields): void
    {
        $normalized = [];

        foreach ($fields as $field) {
            if ($this->has($field) && $this->input($field) !== null) {
                $digits = preg_replace('/\D/', '', (string) $this->input($field));
                $normalized[$field] = $digits === '' ? null : $digits;
            }
        }

        $this->merge($normalized);
    }
}

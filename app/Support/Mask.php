<?php

namespace App\Support;

class Mask
{
    /**
     * Mascara uma chave Pix para exibição pública.
     */
    public static function pixKey(?string $key): string
    {
        if (! $key) {
            return 'chave não informada';
        }

        $key = (string) $key;

        if (mb_strlen($key) <= 6) {
            return str_repeat('*', mb_strlen($key));
        }

        return mb_substr($key, 0, 3).str_repeat('*', max(3, mb_strlen($key) - 6)).mb_substr($key, -3);
    }
}

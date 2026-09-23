<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HalfHourSlotRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Format waktu tidak valid.');

            return;
        }

        $parts = explode(':', $value);
        if (count($parts) < 2) {
            $fail('Format waktu harus jam dan menit (HH:mm).');

            return;
        }

        $minute = (int) $parts[1];
        $second = isset($parts[2]) ? (int) $parts[2] : 0;

        // Slot wajib kelipatan 30 menit: menit 00 atau 30, detik 00
        if (! in_array($minute, [0, 30], true) || $second !== 0) {
            $fail('Waktu harus menggunakan kelipatan slot 30 menit (contoh: 08:00, 08:30, 09:00).');
        }
    }
}

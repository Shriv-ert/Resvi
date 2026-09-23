<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OperationalHoursRule implements ValidationRule
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

        $hour = (int) $parts[0];
        $minute = (int) $parts[1];

        // Jam operasional: 07:00:00 s.d. 20:00:00
        $totalMinutes = ($hour * 60) + $minute;
        $minMinutes = 7 * 60;   // 07:00 -> 420
        $maxMinutes = 20 * 60;  // 20:00 -> 1200

        if ($totalMinutes < $minMinutes || $totalMinutes > $maxMinutes) {
            $fail('Waktu harus berada dalam jam operasional kampus (07.00 – 20.00 WIB).');
        }
    }
}

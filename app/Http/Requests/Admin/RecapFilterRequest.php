<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RecapFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'digits:4', 'min:2020'],
            'metric' => ['nullable', 'string', 'in:occupancy,damage_frequency'],
            'group_by' => ['nullable', 'string', 'in:facility,location'],
            'format' => ['nullable', 'string', 'in:csv,xlsx,pdf'],
        ];
    }

    /**
     * Get validated month with fallback to current month.
     */
    public function getSelectedMonth(): int
    {
        $month = $this->validated('month');

        return ! empty($month) ? (int) $month : (int) now()->month;
    }

    /**
     * Get validated year with fallback to current year.
     */
    public function getSelectedYear(): int
    {
        $year = $this->validated('year');

        return ! empty($year) ? (int) $year : (int) now()->year;
    }

    /**
     * Get validated metric with fallback to 'occupancy'.
     */
    public function getSelectedMetric(): string
    {
        $metric = $this->validated('metric');

        return ! empty($metric) ? (string) $metric : 'occupancy';
    }

    /**
     * Get validated group_by with fallback to 'facility'.
     */
    public function getSelectedGroupBy(): string
    {
        $groupBy = $this->validated('group_by');

        return ! empty($groupBy) ? (string) $groupBy : 'facility';
    }

    /**
     * Get validated format.
     */
    public function getSelectedFormat(): ?string
    {
        $format = $this->validated('format');

        return ! empty($format) ? (string) $format : null;
    }
}

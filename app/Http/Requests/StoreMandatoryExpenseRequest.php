<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMandatoryExpenseRequest extends FormRequest
{
    /**
     * Use a dedicated error bag so dashboard view can distinguish form errors.
     *
     * @var string
     */
    protected $errorBag = 'mandatoryExpense';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'percentage'   => $this->filled('percentage') ? $this->percentage : null,
            'fixed_amount' => $this->filled('fixed_amount') ? $this->fixed_amount : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'mandatory_expense_type_id' => 'required|exists:mandatory_expense_types,id',
            'key_stakeholder_id'        => 'nullable|exists:key_stakeholders,id',
            'percentage'                => 'nullable|numeric|min:0|max:1000',
            'fixed_amount'              => 'nullable|numeric|min:0',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->percentage && !$this->fixed_amount) {
                $validator->errors()->add('percentage', 'Provide either a percentage or a fixed amount.');
            }
        });
    }
}



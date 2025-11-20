<?php

namespace App\Http\Requests;

use App\Models\ExpenseRecipient;
use Illuminate\Foundation\Http\FormRequest;

class StoreOperationalExpenseRequest extends FormRequest
{
    /**
     * Dedicated error bag for operational expense form.
     *
     * @var string
     */
    protected $errorBag = 'operationalExpense';

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
            'operational_category_id' => 'required|exists:operational_categories,id',
            'expense_recipient_id'    => 'nullable|exists:expense_recipients,id',
            'percentage'              => 'nullable|numeric|min:0|max:1000',
            'fixed_amount'            => 'nullable|numeric|min:0',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->percentage && !$this->fixed_amount) {
                $validator->errors()->add('percentage', 'Provide either a percentage or a fixed amount.');
            }

            if ($this->operational_category_id && $this->expense_recipient_id) {
                $recipient = ExpenseRecipient::find($this->expense_recipient_id);
                if ($recipient && $recipient->operational_category_id !== (int) $this->operational_category_id) {
                    $validator->errors()->add('expense_recipient_id', 'Selected recipient does not belong to the chosen category.');
                }
            }
        });
    }
}



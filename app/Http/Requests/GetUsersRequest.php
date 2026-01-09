<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'limit'  => 'nullable|integer|min:1|max:100',
            'page'   => 'nullable|integer|min:1',
            'search' => 'nullable|string|max:255',
            'role'   => 'nullable|string|max:50',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'limit' => $this->limit ?? 20,
            'page'  => $this->page ?? 1,
        ]);
    }
}

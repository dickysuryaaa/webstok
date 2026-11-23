<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'produk_id' => ['required', 'integer'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:4294967295'],
        ];
    }

    public function attributes(): array
    {
        return ['produk_id' => 'produk', 'jumlah' => 'jumlah'];
    }

    public function messages(): array
    {
        return [
            'produk_id.required' => 'Pilih produk terlebih dahulu.',
            'jumlah.min' => 'Jumlah minimal 1.',
        ];
    }
}

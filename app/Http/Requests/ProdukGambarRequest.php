<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ProdukGambarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && in_array($this->user()->role, [
                User::ROLE_PEMILIK,
                User::ROLE_PEKERJA,
            ], true);
    }

    public function rules(): array
    {
        return [
            'gambar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'gambar' => 'gambar produk',
        ];
    }

    public function messages(): array
    {
        return [
            'gambar.uploaded' => 'File gambar gagal diunggah. Periksa ukuran file, format gambar, atau batas upload server.',
            'gambar.required' => 'Pilih gambar produk terlebih dahulu.',
            'gambar.image' => 'File yang dipilih harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus berformat JPG, JPEG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 5 MB.',
        ];
    }
}
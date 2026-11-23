<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPemilik() ?? false;
    }

    public function rules(): array
    {
        $unik = Rule::unique('produk', 'nama_produk');
        if ($produk = $this->route('produk')) {
            $unik->ignore($produk->id);
        }

        return [
            'nama_produk' => ['required', 'string', 'max:150', $unik],
            'stok' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'harga_beli' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'harga_jual' => [
                'required', 'integer', 'min:0', 'max:4294967295',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $beli = $this->input('harga_beli');
                    if (is_numeric($beli) && is_numeric($value)
                        && (int) $value < (int) $beli
                        && ! $this->boolean('konfirmasi_rugi')) {
                        $fail('Harga jual lebih rendah dari harga beli. Centang konfirmasi jual rugi jika memang disengaja.');
                    }
                },
            ],
            'konfirmasi_rugi' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_produk' => 'nama produk',
            'harga_beli' => 'harga beli',
            'harga_jual' => 'harga jual',
        ];
    }
}

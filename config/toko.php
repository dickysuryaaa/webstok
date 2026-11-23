<?php

return [

    /*
    | Ambang stok menipis: produk dengan stok <= nilai ini dianggap menipis.
    */
    'stok_menipis' => (int) env('TOKO_STOK_MENIPIS', 5),

    /*
    | Bila true, halaman chatbot dapat dipakai pelanggan tanpa login.
    | Belum dikonfirmasi mitra, default false.
    */
    'chatbot_public' => (bool) env('CHATBOT_PUBLIC', false),

];

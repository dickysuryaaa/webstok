<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Area Pelanggan - Toko Stok</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 font-sans antialiased"
      style="display:flex; flex-direction:column; min-height:100vh;">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200">

        <div style="
            max-width:1200px;
            margin:0 auto;
            padding:16px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
        ">

            <a href="{{ route('home') }}"
               style="display:flex; align-items:center; gap:12px; text-decoration:none;">

                <div style="
                    width:44px;
                    height:44px;
                    border-radius:12px;
                    background:#059669;
                    color:white;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:19px;
                    font-weight:700;
                    flex-shrink:0;
                ">
                    TS
                </div>

                <div>
                    <h1 style="font-size:19px; font-weight:700; color:#0f172a; margin:0;">
                        Toko Stok
                    </h1>

                    <p style="font-size:13px; color:#64748b; margin:2px 0 0;">
                        Area Pelanggan
                    </p>
                </div>

            </a>

            <a href="{{ route('home') }}"
               style="
                    color:#475569;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:500;
                    white-space:nowrap;
               ">
                Kembali ke Beranda
            </a>

        </div>

    </header>


    <!-- Main Content -->
    <main style="
        flex:1;
        display:flex;
        justify-content:center;
        align-items:flex-start;
        padding:48px 20px 32px;
    ">

        <div style="
            width:100%;
            max-width:480px;
            margin:0 auto;
        ">

            <!-- Form Card -->
            <div style="
                width:100%;
                box-sizing:border-box;
                background:white;
                border:1px solid #e2e8f0;
                border-radius:16px;
                padding:32px;
                box-shadow:0 2px 5px rgba(15,23,42,0.04);
            ">

                {{ $slot }}

            </div>

            <!-- Footer -->
            <p style="
                margin:24px 0 0;
                text-align:center;
                font-size:12px;
                color:#94a3b8;
            ">
                © {{ date('Y') }} Toko Stok. Area Pelanggan.
            </p>

        </div>

    </main>

</body>
</html>
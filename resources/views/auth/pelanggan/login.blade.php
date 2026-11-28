<x-pelanggan-guest-layout>

    <!-- Judul -->
    <div style="margin-bottom:28px;">

        <p style="
            color:#059669;
            font-size:12px;
            font-weight:700;
            letter-spacing:0.8px;
            margin:0 0 10px;
        ">
            CUSTOMER LOGIN
        </p>

        <h2 style="
            color:#0f172a;
            font-size:26px;
            font-weight:700;
            margin:0;
        ">
            Masuk ke Akun Anda
        </h2>
    </div>


    <form method="POST" action="{{ route('pelanggan.login') }}">
        @csrf

        <!-- Email -->
        <div style="margin-bottom:20px;">

            <label for="email" style="
                display:block;
                font-size:14px;
                font-weight:500;
                color:#334155;
                margin-bottom:8px;
            ">
                Alamat Email
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="nama@email.com"
                style="
                    display:block;
                    width:100%;
                    box-sizing:border-box;
                    height:48px;
                    border:1px solid #cbd5e1;
                    border-radius:8px;
                    padding:0 14px;
                    font-size:14px;
                    outline-color:#059669;
                "
            >

            @error('email')
                <p style="color:#dc2626; font-size:12px; margin:6px 0 0;">
                    {{ $message }}
                </p>
            @enderror

        </div>


        <!-- Password -->
        <div style="margin-bottom:18px;">

            <label for="password" style="
                display:block;
                font-size:14px;
                font-weight:500;
                color:#334155;
                margin-bottom:8px;
            ">
                Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
                style="
                    display:block;
                    width:100%;
                    box-sizing:border-box;
                    height:48px;
                    border:1px solid #cbd5e1;
                    border-radius:8px;
                    padding:0 14px;
                    font-size:14px;
                    outline-color:#059669;
                "
            >

        </div>


        <!-- Remember Me -->
        <div style="
            display:flex;
            align-items:center;
            gap:9px;
            margin-bottom:24px;
        ">

            <input
                type="checkbox"
                name="remember"
                id="remember"
                value="1"
                style="width:16px; height:16px; accent-color:#059669;"
            >

            <label for="remember" style="
                font-size:14px;
                color:#475569;
            ">
                Ingat saya
            </label>

        </div>


        <!-- Tombol Login -->
        <button
            type="submit"
            style="
                display:block;
                width:100%;
                height:48px;
                border:0;
                border-radius:8px;
                background:#059669;
                color:white;
                font-size:14px;
                font-weight:600;
                cursor:pointer;
            "
        >
            Masuk Sekarang
        </button>

    </form>


    <!-- Link Register -->
    <div style="
        margin-top:24px;
        padding-top:20px;
        border-top:1px solid #e2e8f0;
        text-align:center;
    ">

        <p style="font-size:14px; color:#64748b; margin:0;">

            Belum memiliki akun?

            <a href="{{ route('pelanggan.register') }}"
               style="
                    color:#059669;
                    font-weight:600;
                    text-decoration:none;
               ">
                Daftar Sekarang
            </a>

        </p>

    </div>

</x-pelanggan-guest-layout>
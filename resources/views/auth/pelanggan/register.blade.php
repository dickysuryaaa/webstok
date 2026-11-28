<x-pelanggan-guest-layout>

    <!-- Judul -->
    <div style="margin-bottom:26px;">

        <p style="
            color:#059669;
            font-size:12px;
            font-weight:700;
            letter-spacing:0.8px;
            margin:0 0 10px;
        ">
            CUSTOMER REGISTRATION
        </p>

        <h2 style="
            color:#0f172a;
            font-size:26px;
            font-weight:700;
            margin:0;
        ">
            Buat Akun Pelanggan
        </h2>
    </div>


    <form method="POST" action="{{ route('pelanggan.register.store') }}">
        @csrf

        <!-- Nama Lengkap -->
        <div style="margin-bottom:18px;">

            <label for="name" style="
                display:block;
                font-size:14px;
                font-weight:500;
                color:#334155;
                margin-bottom:8px;
            ">
                Nama Lengkap
            </label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
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

            @error('name')
                <p style="color:#dc2626; font-size:12px; margin:6px 0 0;">
                    {{ $message }}
                </p>
            @enderror

        </div>


        <!-- Email -->
        <div style="margin-bottom:18px;">

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
                autocomplete="email"
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
                autocomplete="new-password"
                placeholder="Buat password"
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

            @error('password')
                <p style="color:#dc2626; font-size:12px; margin:6px 0 0;">
                    {{ $message }}
                </p>
            @enderror

        </div>


        <!-- Konfirmasi Password -->
        <div style="margin-bottom:26px;">

            <label for="password_confirmation" style="
                display:block;
                font-size:14px;
                font-weight:500;
                color:#334155;
                margin-bottom:8px;
            ">
                Konfirmasi Password
            </label>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi password"
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


        <!-- Tombol Daftar -->
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
            Daftar Sekarang
        </button>

    </form>


    <!-- Link Login -->
    <div style="
        margin-top:24px;
        padding-top:20px;
        border-top:1px solid #e2e8f0;
        text-align:center;
    ">

        <p style="font-size:14px; color:#64748b; margin:0;">

            Sudah memiliki akun?

            <a href="{{ route('pelanggan.login') }}"
               style="
                    color:#059669;
                    font-weight:600;
                    text-decoration:none;
               ">
                Login
            </a>

        </p>

    </div>

</x-pelanggan-guest-layout>
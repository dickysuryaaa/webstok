<x-app-layout>

    <style>
        .customer-catalog {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 24px;
        }

        .customer-catalog-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 26px;
        }

        .customer-catalog-heading {
            flex: 1;
            min-width: 0;
        }

        .customer-catalog-title {
            margin: 0;
            color: #0f172a;
            font-size: 26px;
            font-weight: 700;
        }

        .customer-catalog-description {
            margin-top: 8px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .customer-catalog-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
            width: 360px;
            max-width: 100%;
        }

        .customer-search-wrapper {
            position: relative;
            flex: 1;
            min-width: 0;
        }

        .customer-search-input {
            display: block;
            width: 100%;
            height: 40px;
            box-sizing: border-box;
            padding: 0 38px 0 36px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
            outline: none;
        }

        .customer-search-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.12);
        }

        .customer-search-input::placeholder {
            color: #94a3b8;
        }

        .customer-search-icon {
            position: absolute;
            left: 11px;
            top: 12px;
            width: 16px;
            height: 16px;
            color: #94a3b8;
            pointer-events: none;
        }

        .customer-search-button {
            position: absolute;
            right: 4px;
            top: 4px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 6px;
            background: transparent;
            color: #64748b;
            cursor: pointer;
        }

        .customer-search-button:hover {
            background: #f1f5f9;
        }

        .customer-filter {
            position: relative;
            flex-shrink: 0;
        }

        .customer-filter-summary {
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            list-style: none;
        }

        .customer-filter-summary::-webkit-details-marker {
            display: none;
        }

        .customer-filter-summary:hover {
            background: #f8fafc;
        }

        .customer-filter-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 17px;
            height: 17px;
            padding: 0 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: #059669;
            color: #ffffff;
            font-size: 10px;
            font-weight: 600;
        }

        .customer-filter-panel {
            position: absolute;
            top: 48px;
            right: 0;
            z-index: 30;
            width: 235px;
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }

        .customer-filter-label {
            display: block;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
        }

        .customer-filter-select {
            width: 100%;
            height: 38px;
            margin-top: 9px;
            padding: 0 10px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
        }

        .customer-filter-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
        }

        .customer-filter-reset {
            color: #64748b;
            font-size: 12px;
            text-decoration: none;
        }

        .customer-filter-reset:hover {
            color: #059669;
        }

        .customer-filter-apply {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            background: #059669;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .customer-filter-apply:hover {
            background: #047857;
        }

        .customer-catalog-result {
            margin-bottom: 14px;
            color: #64748b;
            font-size: 13px;
        }

        .customer-product-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .customer-product-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            transition: box-shadow 0.2s ease;
        }

        .customer-product-card:hover {
            box-shadow: 0 5px 14px rgba(15, 23, 42, 0.09);
        }

        .customer-product-image {
            width: 100%;
            height: 175px;
            overflow: hidden;
            background: #f1f5f9;
        }

        .customer-product-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .customer-product-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #94a3b8;
        }

        .customer-product-info {
            padding: 15px;
        }

        .customer-product-name {
            min-height: 40px;
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 600;
        }

        .customer-product-price-label {
            margin-top: 10px;
            color: #64748b;
            font-size: 12px;
        }

        .customer-product-price {
            margin-top: 3px;
            color: #047857;
            font-size: 19px;
            font-weight: 700;
        }

        .customer-product-stock {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
        }

        .customer-stock-label {
            color: #64748b;
        }

        .customer-stock-available {
            padding: 5px 10px;
            border-radius: 20px;
            background: #ecfdf5;
            color: #047857;
            font-weight: 600;
        }

        .customer-stock-empty {
            padding: 5px 10px;
            border-radius: 20px;
            background: #fff1f2;
            color: #be123c;
            font-weight: 600;
        }

        .customer-stock-low {
            padding: 5px 10px;
            border-radius: 20px;
            background: #fffbeb;
            color: #a16207;
            font-weight: 600;
        }

        .customer-catalog-empty {
            padding: 45px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            text-align: center;
        }

        .customer-empty-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 18px;
            padding: 9px 15px;
            border-radius: 7px;
            background: #059669;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .customer-empty-button:hover {
            background: #047857;
        }

        .customer-catalog-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }

        .customer-result-count {
            color: #64748b;
            font-size: 12px;
        }

        .customer-footer-controls {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 15px;
        }

        .customer-per-page {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 12px;
        }

        .customer-per-page select {
            height: 34px;
            padding: 0 25px 0 9px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #ffffff;
            color: #334155;
            font-size: 12px;
        }

        .customer-pagination nav {
            display: flex;
            align-items: center;
        }

        .customer-pagination nav > div:first-child {
            display: none;
        }

        .customer-pagination nav a,
        .customer-pagination nav span span {
            min-width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #ffffff;
            color: #475569;
            font-size: 12px;
            text-decoration: none;
        }

        .customer-pagination nav span[aria-current="page"] span {
            border-color: #059669;
            background: #059669;
            color: #ffffff;
        }

        .customer-pagination nav a:hover {
            background: #f1f5f9;
        }

        @media (max-width: 800px) {

            .customer-catalog-header {
                flex-direction: column;
            }

            .customer-catalog-toolbar {
                width: 100%;
            }

            .customer-product-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

        }

        @media (max-width: 560px) {

            .customer-catalog {
                padding: 18px 14px;
            }

            .customer-product-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .customer-catalog-footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .customer-footer-controls {
                width: 100%;
                justify-content: space-between;
            }

        }

    </style>

    <div class="customer-catalog">

        <!-- Judul dan Toolbar -->
        <div class="customer-catalog-header">

            <div class="customer-catalog-heading">

                <h2 class="customer-catalog-title">
                    Katalog Produk
                </h2>

                <p class="customer-catalog-description">
                    Selamat datang, {{ Auth::user()->name }}.
                    Berikut produk yang tersedia di toko kami.
                </p>

            </div>


            <!-- Search dan Filter -->
            <form
                method="GET"
                action="{{ route('dashboard') }}"
                class="customer-catalog-toolbar"
            >

                <!-- Search -->
                <div class="customer-search-wrapper">

                    <svg
                        class="customer-search-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-4-4"/>
                    </svg>

                    <input
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search..."
                        class="customer-search-input"
                        aria-label="Cari nama produk"
                    >

                    <button
                        type="submit"
                        class="customer-search-button"
                        aria-label="Cari produk"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m5 12 14 0"/>
                            <path d="m13 6 6 6-6 6"/>
                        </svg>
                    </button>

                </div>


                <input
                    type="hidden"
                    name="per_page"
                    value="{{ $perPage }}"
                >


                <!-- Filter -->
                <details class="customer-filter">

                    <summary
                        class="customer-filter-summary"
                        title="Filter produk"
                        aria-label="Filter produk"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="18"
                            height="18"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M4 5h16l-6.5 7.5V19l-3 1v-7.5L4 5z"/>
                        </svg>

                        <span class="customer-filter-badge">
                            {{ $filter === 'all' ? 0 : 1 }}
                        </span>

                    </summary>


                    <div class="customer-filter-panel">

                        <select
                            name="filter"
                            id="filter"
                            class="customer-filter-select"
                        >

                            <option
                                value="all"
                                @selected($filter === 'all')
                            >
                                Semua Stok
                            </option>

                            <option
                                value="tersedia"
                                @selected($filter === 'tersedia')
                            >
                                Tersedia
                            </option>

                            <option
                                value="hampir_habis"
                                @selected($filter === 'hampir_habis')
                            >
                                Hampir Habis
                            </option>

                            <option
                                value="habis"
                                @selected($filter === 'habis')
                            >
                                Habis
                            </option>

                        </select>


                        <div class="customer-filter-actions">

                            <a
                                href="{{ route('dashboard') }}"
                                class="customer-filter-reset"
                            >
                                Reset
                            </a>

                            <button
                                type="submit"
                                class="customer-filter-apply"
                            >
                                Terapkan
                            </button>

                        </div>

                    </div>

                </details>

            </form>

        </div>

        @if ($produk->isEmpty())

            <div class="customer-catalog-empty">

                <h3 class="font-semibold text-slate-800">
                    Produk Tidak Ditemukan
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    Coba gunakan pencarian atau filter lainnya.
                </p>

                <a
                    href="{{ route('dashboard') }}"
                    class="customer-empty-button"
                >
                    Tampilkan Semua Produk
                </a>

            </div>

        @else

            <div class="customer-product-grid">

                @foreach ($produk as $p)

                    <div class="customer-product-card">

                        <!-- Gambar Produk -->
                        <div class="customer-product-image">

                            @if ($p->gambar)

                                <img
                                    src="{{ asset('storage/'.$p->gambar) }}"
                                    alt="{{ $p->nama_produk }}"
                                    loading="lazy"
                                >

                            @else

                                <div class="customer-product-placeholder">

                                    <span class="text-xs">
                                        Gambar belum tersedia
                                    </span>

                                </div>

                            @endif

                        </div>

                        <div class="customer-product-info">

                            <h3 class="customer-product-name">
                                {{ $p->nama_produk }}
                            </h3>

                            <p class="customer-product-price-label">
                                Harga Produk
                            </p>

                            <p class="customer-product-price">
                                Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                            </p>

                            <div class="customer-product-stock">

                                <span class="customer-stock-label">
                                    Stok tersedia
                                </span>

                                @if ($p->stok === 0)

                                    <span class="customer-stock-empty">
                                        Habis (0)
                                    </span>

                                @elseif ($p->stok <= (int) config('toko.stok_menipis', 5))

                                    <span class="customer-stock-low">
                                        Hampir Habis ({{ $p->stok }})
                                    </span>

                                @else

                                    <span class="customer-stock-available">
                                        {{ $p->stok }} unit
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            <div class="customer-catalog-footer">

                <div class="customer-result-count">

                    Showing
                    {{ $produk->firstItem() ?? 0 }}
                    to
                    {{ $produk->lastItem() ?? 0 }}
                    of
                    {{ $produk->total() }}
                    results

                </div>


                <div class="customer-footer-controls">

                    <!-- Jumlah Data Per Halaman -->
                    <form
                        method="GET"
                        action="{{ route('dashboard') }}"
                        class="customer-per-page"
                    >

                        <input
                            type="hidden"
                            name="search"
                            value="{{ $search }}"
                        >

                        <input
                            type="hidden"
                            name="filter"
                            value="{{ $filter }}"
                        >

                        <label for="per_page">
                            Per page
                        </label>

                        <select
                            name="per_page"
                            id="per_page"
                            onchange="this.form.submit()"
                        >

                            <option
                                value="6"
                                @selected($perPage === 6)
                            >
                                6
                            </option>

                            <option
                                value="9"
                                @selected($perPage === 9)
                            >
                                9
                            </option>

                            <option
                                value="12"
                                @selected($perPage === 12)
                            >
                                12
                            </option>

                            <option
                                value="15"
                                @selected($perPage === 15)
                            >
                                15
                            </option>

                        </select>

                    </form>


                    <!-- Navigasi Halaman -->
                    @if ($produk->hasPages())

                        <div class="customer-pagination">

                            {{ $produk->links() }}

                        </div>

                    @endif

                </div>

            </div>

        @endif

    </div>

</x-app-layout>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Web — Laragon</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --accent: #22c55e;
            --text: #f8fafc;
            --muted: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 100%);
            color: var(--text);
            min-height: 100vh;
        }
        header {
            padding: 2rem 1.5rem 1rem;
            max-width: 1100px;
            margin: 0 auto;
        }
        header h1 { font-size: 1.75rem; margin-bottom: 0.25rem; }
        header p { color: var(--muted); font-size: 0.95rem; }
        .badge {
            display: inline-block;
            margin-top: 0.75rem;
            padding: 0.35rem 0.75rem;
            background: rgba(34, 197, 94, 0.15);
            color: var(--accent);
            border-radius: 999px;
            font-size: 0.8rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
            padding: 1rem 1.5rem 3rem;
            max-width: 1100px;
            margin: 0 auto;
        }
        .card {
            background: var(--card);
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.35);
        }
        .card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: #334155;
        }
        .card-body { padding: 1rem; }
        .card h2 { font-size: 1rem; margin-bottom: 0.35rem; }
        .card .desc {
            color: var(--muted);
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .price {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--accent);
        }
        .stock {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }
        footer {
            text-align: center;
            padding: 1rem;
            color: var(--muted);
            font-size: 0.8rem;
        }
        code { background: #334155; padding: 0.15rem 0.4rem; border-radius: 4px; }
    </style>
</head>
<body>
    <header>
        <h1>Market Web</h1>
        <p>Katalog produk — backend Laravel siap untuk Flutter & hosting Laragon</p>
        <span class="badge">API: {{ $apiBaseUrl }}/products</span>
    </header>

    <main class="grid">
        @forelse ($products as $product)
            <article class="card">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                @endif
                <div class="card-body">
                    <h2>{{ $product->name }}</h2>
                    <p class="desc">{{ $product->description }}</p>
                    <p class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <p class="stock">Stok: {{ $product->stock }}</p>
                </div>
            </article>
        @empty
            <p style="grid-column: 1/-1; color: var(--muted);">Belum ada produk. Jalankan <code>php artisan db:seed</code></p>
        @endforelse
    </main>

    <footer>
        Laravel Market API · Terhubung ke app Flutter via <code>X-API-Key</code>
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Impor kamus terstruktur</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-950">
    <main class="mx-auto min-h-screen w-full max-w-3xl px-5 py-8 sm:px-6 lg:px-8">
        <header class="border-b border-slate-200 pb-6">
            <a href="{{ url('/admin/dictionary-entries') }}" class="text-sm font-medium text-emerald-700">Kembali ke Filament</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight">Impor / ekspor kamus terstruktur</h1>
            <p class="mt-3 text-base leading-7 text-slate-600">CSV skema v1 menyimpan satu lema per baris dan data bertingkat sebagai JSON UTF-8.</p>
        </header>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-900">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-900">
                <p class="font-semibold">Impor belum bisa diproses:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.lexicographic-import.template') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Unduh template v1</a>
                <a href="{{ route('admin.lexicographic-export') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Ekspor seluruh data yang dapat diakses</a>
            </div>

            <div class="mt-5 rounded-lg border border-sky-100 bg-sky-50/70 p-4 text-sm leading-6 text-slate-700">
                <p class="font-semibold text-slate-950">Aturan impor</p>
                <ul class="mt-1 list-disc space-y-1 pl-5">
                    <li>Gunakan header template persis; <code>forms_json</code>, <code>senses_json</code>, dan <code>pronunciations_json</code> berisi array JSON.</li>
                    <li><code>public_id</code> mengidentifikasi lema saat pembaruan; kosongkan untuk membuat lema baru.</li>
                    <li>Data cocok diperbarui atau ditambahkan. Data turunan yang tidak ada di berkas tidak dihapus.</li>
                    <li>Editor tidak dapat menerbitkan lewat CSV. Audio dan sitasi impor tetap perlu pemeriksaan hak, sumber, dan visibilitas.</li>
                    <li>Impor diproses sebagai satu transaksi; bila ada baris tidak valid, seluruh berkas ditolak.</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('admin.lexicographic-import.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4">
                @csrf
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">File CSV (maksimal 20 MB)</span>
                    <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-emerald-700 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
                </label>
                <button type="submit" class="h-11 rounded-lg bg-emerald-700 px-5 text-sm font-medium text-white hover:bg-emerald-800">Impor CSV v1</button>
            </form>
        </section>
    </main>
</body>
</html>

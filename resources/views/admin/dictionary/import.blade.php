<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Entri Kamus</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-950">
    <main class="mx-auto min-h-screen w-full max-w-3xl px-5 py-8 sm:px-6 lg:px-8">
        <header class="border-b border-slate-200 pb-6">
            <a href="{{ url('/admin/dictionary-entries') }}" class="text-sm font-medium text-emerald-700">Kembali ke Filament</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight">Import Entri Kamus</h1>
            <p class="mt-3 text-base leading-7 text-slate-600">
                Unggah file CSV dengan header sesuai template. Spasi berlebih akan dirapikan, dan baris duplikat akan dilewati otomatis.
            </p>
        </header>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-900">
                <p class="font-semibold">Import belum bisa diproses:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dictionary-entries.template') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Download Template
                </a>
                <a href="{{ route('admin.dictionary-entries.export') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Export CSV
                </a>
            </div>

            <div class="mt-5 rounded-lg border border-sky-100 bg-sky-50/70 p-4 text-sm leading-6 text-slate-700">
                <p class="font-semibold text-slate-950">Format wajib</p>
                <p class="mt-1">Kolom minimal yang harus terisi adalah <strong>tolaki_word</strong> dan <strong>indonesian_word</strong>. Kolom lain boleh kosong, tetapi header tidak boleh diubah.</p>
                <div class="mt-3 overflow-x-auto rounded-md bg-white p-3 font-mono text-xs text-slate-700">
                    tolaki_word, indonesian_word, english_word, part_of_speech, phonetic, definition, usage_note, cultural_note, category, status, tolaki_sentence, indonesian_sentence, english_sentence, example_notes
                </div>
                <p class="mt-3">Status yang dikenali: <strong>draft</strong>, <strong>submitted</strong>, <strong>reviewed</strong>, <strong>published</strong>, atau <strong>rejected</strong>. Editor tidak bisa menerbitkan langsung lewat import.</p>
            </div>

            <form method="POST" action="{{ route('admin.dictionary-entries.import.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-4">
                @csrf
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">File CSV</span>
                    <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required
                        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-emerald-700 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
                </label>

                <button type="submit" class="h-11 rounded-lg bg-emerald-700 px-5 text-sm font-medium text-white hover:bg-emerald-800">
                    Import Sekarang
                </button>
            </form>
        </section>
    </main>
</body>

</html>

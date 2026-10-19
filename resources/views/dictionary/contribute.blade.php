@extends('layouts.public', ['maxWidth' => '896px'])

@section('title', 'Usulkan Entri - Kamus Tolaki')

@section('content')
    <section class="py-8">
        <header class="border-b border-slate-200 pb-6">
            <a href="{{ route('dictionary.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-900">Kembali ke kamus</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Usulkan Entri Kamus</h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                Kirim kosakata baru untuk ditinjau editor. Usulan tidak langsung tampil di halaman publik sampai divalidasi.
            </p>
        </header>

        @if (session('status'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-900">
                <p class="font-semibold">Ada data yang perlu diperiksa:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('dictionary.contribute.store') }}" class="mt-6 grid gap-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            @csrf
            <input type="hidden" name="_contribution_token" value="{{ session('dictionary_contribution_token') }}">
            <div class="hidden" aria-hidden="true">
                <label>
                    Website
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
                </label>
            </div>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <h2 class="text-sm font-semibold text-slate-950">Kosakata</h2>
                <p class="mt-1 text-sm text-slate-500">Isi kata utama dan padanannya terlebih dahulu.</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Kata Tolaki</span>
                        <input name="tolaki_word" value="{{ old('tolaki_word') }}" required
                            class="mt-1 h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Arti Indonesia</span>
                        <input name="indonesian_word" value="{{ old('indonesian_word') }}" required
                            class="mt-1 h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Padanan Inggris</span>
                        <input name="english_word" value="{{ old('english_word') }}"
                            class="mt-1 h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Kelas kata</span>
                        <select name="part_of_speech"
                            class="mt-1 h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                            <option value="">Belum diketahui</option>
                            @foreach ($partsOfSpeech as $value => $label)
                                <option value="{{ $value }}" @selected(old('part_of_speech') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Kategori</span>
                        <select name="category_id"
                            class="mt-1 h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                            <option value="">Belum dikategorikan</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            <div class="grid gap-4">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Definisi atau penjelasan makna</span>
                    <textarea name="definition" rows="4"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('definition') }}</textarea>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Catatan penggunaan</span>
                    <textarea name="usage_note" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('usage_note') }}</textarea>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Catatan budaya</span>
                    <textarea name="cultural_note" rows="3"
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('cultural_note') }}</textarea>
                </label>
            </div>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <h2 class="text-sm font-semibold text-slate-900">Contoh kalimat</h2>
                <div class="mt-3 grid gap-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Kalimat Tolaki</span>
                        <textarea name="tolaki_sentence" rows="2"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('tolaki_sentence') }}</textarea>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Kalimat Indonesia</span>
                        <textarea name="indonesian_sentence" rows="2"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('indonesian_sentence') }}</textarea>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Kalimat Inggris</span>
                        <textarea name="english_sentence" rows="2"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('english_sentence') }}</textarea>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Catatan contoh</span>
                        <textarea name="example_notes" rows="2"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('example_notes') }}</textarea>
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2">
                <a href="{{ route('dictionary.index') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="h-11 rounded-lg bg-emerald-700 px-5 text-sm font-medium text-white hover:bg-emerald-800">
                    Kirim Usulan
                </button>
            </div>
        </form>
    </section>
@endsection

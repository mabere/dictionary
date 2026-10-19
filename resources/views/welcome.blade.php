@extends('layouts.public', ['maxWidth' => '896px'])

@section('title', config('app.name', 'Kamus Tolaki'))

@section('content')
    <style>
        @media (max-width: 639px) {
            .home-search-input {
                height: 56px !important;
                font-size: 16px !important;
                line-height: 40px !important;
            }

            .home-search-button {
                height: 40px !important;
            }
        }
    </style>

    <section class="flex min-h-[calc(100vh-180px)] flex-col justify-center py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Portal Pencarian Kosakata</p>
        <h1 class="mx-auto mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
            Kamus Tolaki, Indonesia, dan Inggris untuk mencari arti kata, contoh kalimat, dan pelafalan.
        </h1>
        <p class="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-600">
            Temukan kosakata Tolaki beserta padanan Indonesia dan Inggris. Data publik telah melalui proses validasi editorial.
        </p>

        <form method="GET" action="{{ route('dictionary.index') }}"
            class="mx-auto mt-8 flex w-full max-w-2xl flex-col gap-3 rounded-lg border border-slate-200 bg-white p-3 text-left shadow-sm sm:flex-row">
            <input id="q" name="q" type="search" autofocus placeholder="Cari kata, arti, atau definisi"
                class="home-search-input h-10 min-w-0 flex-1 rounded-md border border-slate-300 px-4 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
            <button type="submit"
                class="home-search-button h-10 flex-none rounded-md bg-emerald-700 px-6 text-sm font-semibold text-white hover:bg-emerald-800"
                style="min-width: 74px;">
                Cari
            </button>
        </form>

        <div class="mt-5 flex flex-wrap justify-center gap-3 text-sm">
            <a href="{{ route('dictionary.index') }}" class="font-medium text-emerald-700 hover:text-emerald-900">
                Lihat semua entri
            </a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('dictionary.contribute') }}" class="font-medium text-emerald-700 hover:text-emerald-900">
                Usulkan kata baru
            </a>
        </div>

    </section>
@endsection

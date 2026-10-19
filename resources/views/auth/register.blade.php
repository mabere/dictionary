@extends('layouts.auth')

@section('title', 'Register - Kamus Tolaki')
@section('eyebrow', 'Kontributor Kamus')
@section('headline', 'Buat akun untuk ikut mengembangkan kosakata.')
@section('description',
    'Akun baru dibuat sebagai pengguna publik. Akses editorial tetap diberikan terpisah oleh admin
    agar validasi kamus tetap tertata.')

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Register</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Daftar akun</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">Lengkapi data dasar untuk membuat akun pengguna kamus.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="mt-7 space-y-4">
            @csrf

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Nama</span>
                <input name="name" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap"
                    class="mt-2 h-11 w-full rounded-lg border border-slate-300 px-4 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('name')
                    <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Email</span>
                <input name="email" type="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                    class="mt-2 h-11 w-full rounded-lg border border-slate-300 px-4 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('email')
                    <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Password</span>
                <input name="password" type="password" required placeholder="Minimal 8 karakter"
                    class="mt-2 h-11 w-full rounded-lg border border-slate-300 px-4 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('password')
                    <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Konfirmasi password</span>
                <input name="password_confirmation" type="password" required placeholder="Ulangi password"
                    class="mt-2 h-11 w-full rounded-lg border border-slate-300 px-4 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
            </label>

            <button type="submit"
                class="mt-5 h-12 w-full rounded-lg bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800">
                Daftar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-900">Login</a>
        </p>
    </div>
@endsection

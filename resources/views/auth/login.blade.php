@extends('layouts.auth')

@section('title', 'Login - Kamus Tolaki')
@section('eyebrow', 'Akses Editorial')
@section('headline', 'Kelola kosakata dalam ruang kerja yang rapi.')
@section('description',
    'Masuk untuk membuka dashboard, menyunting entri, meninjau usulan, dan menjaga kualitas data
    kamus yang tampil di publik.')

@section('aside')
    @if (app()->environment('local', 'testing'))
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
            <div class="flex items-center justify-between gap-4">
                <p class="text-sm font-semibold text-slate-950">Akun demo</p>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Lokal</span>
            </div>

            <div class="mt-5 divide-y divide-slate-200 text-sm">
                <div class="grid gap-1 py-3.5 first:pt-0 sm:grid-cols-[92px_minmax(0,1fr)] sm:gap-4">
                    <p class="font-semibold text-slate-950">Admin</p>
                    <div class="space-y-1 text-slate-600">
                        <p>Email: <span class="font-semibold text-slate-900">admin@email.com</span></p>
                        <p>Password: <span class="font-semibold text-slate-900">password</span></p>
                    </div>
                </div>
                <div class="grid gap-1 py-3.5 last:pb-0 sm:grid-cols-[92px_minmax(0,1fr)] sm:gap-4">
                    <p class="font-semibold text-slate-950">Editor</p>
                    <div class="space-y-1 text-slate-600">
                        <p>Email: <span class="font-semibold text-slate-900">editor@email.com</span></p>
                        <p>Password: <span class="font-semibold text-slate-900">password</span></p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
            <p class="text-sm font-semibold text-slate-950">Akses Terlindungi</p>
            <p class="mt-2 text-xs leading-5 text-slate-600">Portal ini hanya diperuntukkan bagi tim redaksi dan kontributor resmi Kamus Tolaki. Silakan gunakan kredensial yang telah diberikan oleh administrator.</p>
        </div>
    @endif
@endsection

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8 lg:min-h-[688px]">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Login</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Masuk ke akun</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">Gunakan email dan password yang terdaftar.</p>
        </div>


        <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">
            @csrf

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Email</span>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    placeholder="nama@email.com"
                    class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('email')
                    <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Password</span>
                <input id="password" name="password" type="password" required placeholder="Masukkan password"
                    class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('password')
                    <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
                @enderror
            </label>

            <label
                class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1"
                    class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                <span>Ingat sesi login saya</span>
            </label>

            <button type="submit"
                class="h-12 w-full rounded-lg bg-emerald-700 px-5 text-sm font-semibold text-white hover:bg-emerald-800">
                Login
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-900">Daftar
                kontributor</a>
        </p>
    </div>
@endsection

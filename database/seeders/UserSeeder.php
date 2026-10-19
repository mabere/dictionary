<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@email.com')],
            [
                'name' => 'Admin Kamus',
                'role' => 'admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'KamusAdmin2026!Secured')),
            ]
        );

        User::updateOrCreate(
            ['email' => env('EDITOR_EMAIL', 'editor@email.com')],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make(env('EDITOR_PASSWORD', 'KamusEditor2026!Secured')),
            ]
        );

        User::updateOrCreate(
            ['email' => env('ASSESSOR_EMAIL', 'asesor@ramli.my.id')],
            [
                'name' => 'Asesor Portofolio',
                'role' => 'assessor',
                'password' => Hash::make(env('ASSESSOR_PASSWORD', 'AsesorKamus2026!')),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501002@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501002'),
            ]
        );

        User::updateOrCreate(
            ['email' => '224501003@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501003'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501004@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501004'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501005@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501005'),
            ]
        );

        User::updateOrCreate(
            ['email' => '224501007@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501007'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501008@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501008'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501009@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501009'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501010@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501010'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501013@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501013'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501014@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501014'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501015@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501015'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501016@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501016'),
            ]
        );
        User::updateOrCreate(
            ['email' => '224501017@email.com'],
            [
                'name' => 'Editor Kamus',
                'role' => 'editor',
                'password' => Hash::make('224501017'),
            ]
        );

    }
}

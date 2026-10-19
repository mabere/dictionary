<?php

namespace Database\Seeders;

use App\Models\DictionaryCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DictionaryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Keluarga', 'description' => 'Kosakata tentang relasi keluarga dan kekerabatan.'],
            ['name' => 'Benda', 'description' => 'Nama benda sehari-hari dan benda budaya.'],
            ['name' => 'Aktivitas', 'description' => 'Kata kerja dan ungkapan aktivitas.'],
            ['name' => 'Alam', 'description' => 'Kosakata tentang lingkungan, cuaca, tumbuhan, dan hewan.'],
            ['name' => 'Budaya', 'description' => 'Istilah adat, tradisi, dan kehidupan sosial.'],
            ['name' => 'Pendidikan', 'description' => 'Kosakata sekolah, kampus, dan pembelajaran.'],
        ];

        foreach ($categories as $category) {
            DictionaryCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                $category + ['slug' => Str::slug($category['name'])]
            );
        }
    }
}

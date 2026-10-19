<?php

namespace Database\Seeders;

use App\Models\OrthographyProfile;
use Illuminate\Database\Seeder;

class OrthographyProfileSeeder extends Seeder
{
    public function run(): void
    {
        OrthographyProfile::query()->updateOrCreate(
            ['name' => 'Profil historis Kamus Tolaki–Indonesia 1985', 'version' => '1985-draft-1'],
            [
                'status' => 'draft',
                'profile_type' => 'orthography',
                'rules' => [
                    'initials' => ['A', 'B', 'D', 'E', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'W'],
                    'short_vowels' => ['a', 'i', 'u', 'e', 'o'],
                    'long_vowels' => ['aa', 'ii', 'uu', 'ee', 'oo'],
                    'glottal_marker' => "'",
                    'prenasal_sequences' => ['mb', 'nd', 'ngg'],
                    'reduplication_hyphen' => true,
                ],
                'citation' => 'Abdul Muthalib, Alimuddin D.P., Husen Chalik, dan Arsamid. 1985. Kamus Tolaki–Indonesia. Jakarta: Pusat Pembinaan dan Pengembangan Bahasa.',
                'change_note' => 'Profil kandidat berbasis Petunjuk Pemakaian edisi 1985. Belum disahkan sebagai standar aktif; verifikasi urutan alfabet pada halaman pindai dan tinjau bersama editor/penutur.',
            ],
        );

        OrthographyProfile::query()->updateOrCreate(
            ['name' => 'Profil deskriptif morfologi verba Tolaki (Pattiasina dkk. 1983)', 'version' => '1983-research-draft-1'],
            [
                'status' => 'draft',
                'profile_type' => 'verb_morphology',
                'rules' => [
                    'scope' => 'descriptive_verb_morphology',
                    'dialect_and_location' => 'Dialek Konawe; data penelitian di Unaaha (nomenklatur lokasi sebagaimana sumber 1983).',
                    'research_framework' => 'Linguistik struktural.',
                    'reported_affix_inventory' => [
                        'prefixes' => ['me-', 'pe-', 'mbe-', 'mo-', 'mbo-', 'poko-', 'ni-', 'in-'],
                        'infixes' => ['-um-', '-in-'],
                        'suffixes' => ['-i', '-ako', '-kee'],
                        'combined_prefixes' => ['momboko-', 'pinoko-'],
                        'confixes' => ['-um-...-i', 'me-...-i', 'me-...-ako', '-um-...-ikee', 'in-...-ako', 'podo-...-i'],
                        'combined_suffixes' => ['-ikee', '-ito'],
                    ],
                    'other_processes' => ['reduplication', 'compounding'],
                    'base_categories_reported' => ['precategorial', 'verb', 'noun', 'adjective', 'numeral'],
                    'source_caveat' => 'Inventaris deskriptif dari penelitian 1983; bukan pedoman ejaan atau standar modern. Produktivitas, fungsi, makna, dan variasi dialek harus diperiksa pada uraian sumber dan diverifikasi oleh editor/penutur.',
                ],
                'citation' => 'Pattiasina, J.F., Abdul Muthalib, Haloma Balaks, Abdurrauf Tarimana, M. Mattalitti, dan Abd. Kadir Mulya. 1983. Sistem Morfologi Kata Kerja Bahasa Tolaki. Jakarta: Pusat Pembinaan dan Pengembangan Bahasa, Departemen Pendidikan dan Kebudayaan.',
                'change_note' => 'Profil rujukan deskriptif morfologi verba berdasarkan inventaris yang dilaporkan sumber. Dipisahkan dari profil ortografi agar tidak dipakai sebagai kebijakan ejaan lema.',
            ],
        );
    }
}

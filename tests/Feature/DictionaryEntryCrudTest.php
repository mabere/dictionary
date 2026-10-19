<?php

namespace Tests\Feature;

use App\Models\DictionaryCategory;
use App\Models\DictionaryEntry;
use App\Models\DictionaryVisit;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DictionaryEntryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dictionary_entry_crud_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin/dictionary-entries')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/dictionary-entries/create')
            ->assertOk();
    }

    public function test_dictionary_entries_and_users_are_scoped_to_their_owner(): void
    {
        $owner = User::factory()->create(['role' => 'editor']);
        $other = User::factory()->create(['role' => 'editor']);
        $ownedEntry = DictionaryEntry::create([
            'tolaki_word' => 'Laika', 'indonesian_word' => 'Rumah', 'created_by' => $owner->id,
        ]);
        $foreignEntry = DictionaryEntry::create([
            'tolaki_word' => 'Oda', 'indonesian_word' => 'Tidak', 'created_by' => $other->id,
        ]);
        $ownedUser = User::factory()->create(['managed_by' => $owner->id]);
        $foreignUser = User::factory()->create(['managed_by' => $other->id]);

        $this->actingAs($owner)->get('/admin/dictionary-entries')
            ->assertOk()->assertSee('Laika')->assertDontSee('Oda');
        $this->actingAs($owner)->get("/admin/dictionary-entries/{$ownedEntry->id}")->assertOk();
        $this->actingAs($owner)->get("/admin/dictionary-entries/{$foreignEntry->id}")->assertNotFound();
        $this->actingAs($owner)->get('/admin/users')
            ->assertForbidden();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin/users')
            ->assertOk()->assertSee($ownedUser->email)->assertSee($foreignUser->email);
    }

    public function test_published_entry_is_visible_publicly(): void
    {
        $category = DictionaryCategory::create([
            'name' => 'Keluarga',
            'slug' => 'keluarga',
        ]);

        $entry = DictionaryEntry::create([
            'source_word' => 'Ama',
            'target_word' => 'Ayah',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'part_of_speech' => 'noun',
            'definition' => 'Sebutan untuk orang tua laki-laki.',
            'category_id' => $category->id,
            'status' => DictionaryEntry::STATUS_PUBLISHED,
        ]);

        $this->get('/dictionary')
            ->assertOk()
            ->assertSee('Ama')
            ->assertSee('Ayah');

        $this->get("/dictionary/{$entry->public_id}")
            ->assertOk()
            ->assertSee('Sebutan untuk orang tua laki-laki.');

        $this->get("/dictionary/{$entry->id}")
            ->assertNotFound();
    }

    public function test_published_entry_can_show_pronunciation_audio(): void
    {
        $entry = DictionaryEntry::create([
            'source_word' => 'Ine',
            'target_word' => 'Ibu',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'pronunciation_audio' => 'pronunciations/ine.mp3',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
        ]);

        $this->get("/dictionary/{$entry->public_id}")
            ->assertOk()
            ->assertSee('Pelafalan')
            ->assertSee('storage/pronunciations/ine.mp3');
    }

    public function test_draft_entry_is_hidden_from_public_pages(): void
    {
        $entry = DictionaryEntry::create([
            'source_word' => 'Rahasia',
            'target_word' => 'Secret',
            'source_language' => 'Bahasa A',
            'target_language' => 'English',
            'status' => DictionaryEntry::STATUS_DRAFT,
        ]);

        $this->get('/dictionary')
            ->assertOk()
            ->assertDontSee('Rahasia');

        $this->get("/dictionary/{$entry->public_id}")
            ->assertNotFound();
    }

    public function test_duplicate_entries_are_rejected_by_normalized_key(): void
    {
        $entry = DictionaryEntry::create([
            'source_word' => 'Ama',
            'target_word' => 'Ayah',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'part_of_speech' => 'noun',
            'status' => DictionaryEntry::STATUS_DRAFT,
        ]);

        $this->expectException(QueryException::class);

        DictionaryEntry::create([
            'source_word' => ' ama ',
            'target_word' => 'AYAH',
            'source_language' => 'bahasa a',
            'target_language' => 'bahasa indonesia',
            'part_of_speech' => 'NOUN',
            'status' => DictionaryEntry::STATUS_DRAFT,
        ]);
    }

    public function test_public_dictionary_can_filter_entries_by_first_letter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        DictionaryEntry::create([
            'source_word' => 'Ama',
            'target_word' => 'Ayah',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
            'created_by' => $admin->id,
        ]);

        DictionaryEntry::create([
            'source_word' => 'Banu',
            'target_word' => 'Anak',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
            'created_by' => $admin->id,
        ]);

        $this->get('/dictionary?letter=a')
            ->assertOk()
            ->assertSee('Ama')
            ->assertDontSee('Banu');
    }

    public function test_public_dictionary_can_search_tolaki_indonesian_and_english_words(): void
    {
        DictionaryEntry::create([
            'tolaki_word' => 'Laika',
            'indonesian_word' => 'Rumah',
            'english_word' => 'House',
            'part_of_speech' => 'noun',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
        ]);

        $this->get('/dictionary?q=laika')
            ->assertOk()
            ->assertSee('Laika');

        $this->get('/dictionary?q=rumah')
            ->assertOk()
            ->assertSee('Laika');

        $this->get('/dictionary?q=house')
            ->assertOk()
            ->assertSee('Laika');
    }

    public function test_dictionary_detail_shows_three_languages(): void
    {
        $entry = DictionaryEntry::create([
            'tolaki_word' => 'Laika',
            'indonesian_word' => 'Rumah',
            'english_word' => 'House',
            'definition' => 'Tempat tinggal permanen.',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
        ]);

        $this->get("/dictionary/{$entry->public_id}")
            ->assertOk()
            ->assertSee('Laika')
            ->assertSee('Padanan Indonesia')
            ->assertSee('Rumah')
            ->assertSee('English equivalents')
            ->assertSee('House')
            ->assertSee('Cari lema atau padanan');
    }

    public function test_guest_can_submit_dictionary_contribution_as_draft(): void
    {
        $category = DictionaryCategory::create([
            'name' => 'Budaya',
            'slug' => 'budaya',
        ]);

        $this->get('/dictionary/contribute')
            ->assertOk()
            ->assertSee('Usulkan Entri Kamus');

        $token = session('dictionary_contribution_token');

        $response = $this->post('/dictionary/contribute', [
            '_contribution_token' => $token,
            'tolaki_word' => 'Mepokoaso',
            'indonesian_word' => 'Gotong royong',
            'english_word' => 'Mutual cooperation',
            'part_of_speech' => 'phrase',
            'category_id' => $category->id,
            'definition' => 'Kegiatan bekerja bersama untuk kepentingan umum.',
            'tolaki_sentence' => 'Mepokoaso i kampo.',
            'indonesian_sentence' => 'Gotong royong di kampung.',
            'english_sentence' => 'Mutual cooperation in the village.',
        ]);

        $response->assertRedirect('/dictionary/contribute');

        $entry = DictionaryEntry::query()->where('tolaki_word', 'Mepokoaso')->firstOrFail();

        $this->assertSame(DictionaryEntry::STATUS_SUBMITTED, $entry->status);
        $this->assertSame('Gotong royong', $entry->indonesian_word);
        $this->assertCount(1, $entry->examples);

        $this->get('/dictionary')
            ->assertOk()
            ->assertDontSee('Mepokoaso');
    }

    public function test_duplicate_contribution_is_rejected(): void
    {
        DictionaryEntry::create([
            'source_word' => 'Ama',
            'target_word' => 'Ayah',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'part_of_speech' => 'noun',
            'status' => DictionaryEntry::STATUS_DRAFT,
        ]);

        $this->get('/dictionary/contribute')->assertOk();

        $this->post('/dictionary/contribute', [
            '_contribution_token' => session('dictionary_contribution_token'),
            'tolaki_word' => ' ama ',
            'indonesian_word' => 'AYAH',
            'part_of_speech' => 'noun',
        ])->assertSessionHasErrors('tolaki_word');
    }

    public function test_contribution_rejects_direct_post_without_form_token(): void
    {
        $this->post('/dictionary/contribute', [
            'tolaki_word' => 'Lako',
            'indonesian_word' => 'Pergi',
        ])->assertSessionHasErrors('_contribution_token');

        $this->assertFalse(DictionaryEntry::query()->where('tolaki_word', 'Lako')->exists());
    }

    public function test_contribution_rejects_filled_honeypot_field(): void
    {
        $this->get('/dictionary/contribute')->assertOk();

        $this->post('/dictionary/contribute', [
            '_contribution_token' => session('dictionary_contribution_token'),
            'website' => 'https://spam.example',
            'tolaki_word' => 'Lako',
            'indonesian_word' => 'Pergi',
        ])->assertSessionHasErrors('website');

        $this->assertFalse(DictionaryEntry::query()->where('tolaki_word', 'Lako')->exists());
    }

    public function test_public_dictionary_records_search_and_detail_visits(): void
    {
        $entry = DictionaryEntry::create([
            'source_word' => 'Ine',
            'target_word' => 'Ibu',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
        ]);

        $this->get('/dictionary?q=ine')->assertOk();
        $this->get('/dictionary?q=ine')->assertOk();
        $this->get("/dictionary/{$entry->public_id}")->assertOk();
        $this->get("/dictionary/{$entry->public_id}")->assertOk();

        $this->assertSame(2, DictionaryVisit::query()->count());
        $this->assertTrue(DictionaryVisit::query()->where('keyword', 'ine')->exists());
        $this->assertTrue(DictionaryVisit::query()->where('dictionary_entry_id', $entry->id)->exists());
    }

    public function test_admin_can_import_dictionary_entries_from_csv(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $csv = implode("\n", [
            'tolaki_word,indonesian_word,english_word,part_of_speech,phonetic,definition,usage_note,cultural_note,category,status,tolaki_sentence,indonesian_sentence,english_sentence,example_notes',
            'Lalo,Jalan,Road,noun,,Tempat untuk lewat,,,Aktivitas,draft,Lalo moiko,Jalan bagus,Good road,',
        ]);

        $file = UploadedFile::fake()->createWithContent('entries.csv', $csv);

        $this->actingAs($admin)
            ->post('/admin/dictionary-import', ['csv_file' => $file])
            ->assertRedirect('/admin/dictionary-import');

        $entry = DictionaryEntry::query()->where('tolaki_word', 'Lalo')->firstOrFail();

        $this->assertSame('Jalan', $entry->indonesian_word);
        $this->assertSame('Road', $entry->english_word);
        $this->assertSame('Aktivitas', $entry->category?->name);
        $this->assertCount(1, $entry->examples);
    }

    public function test_import_cleans_bom_and_extra_spacing(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $csv = implode("\n", [
            "\xEF\xBB\xBFtolaki_word,indonesian_word,english_word,part_of_speech,phonetic,definition,usage_note,cultural_note,category,status,tolaki_sentence,indonesian_sentence,english_sentence,example_notes",
            '  Lalo   motaha  ,  Jalan   besar  , Big   road,noun,,  Jalan yang luas  ,,,Aktivitas,draft,,,,',
        ]);

        $file = UploadedFile::fake()->createWithContent('entries.csv', $csv);

        $this->actingAs($admin)
            ->post('/admin/dictionary-import', ['csv_file' => $file])
            ->assertRedirect('/admin/dictionary-import');

        $entry = DictionaryEntry::query()->where('tolaki_word', 'Lalo motaha')->firstOrFail();

        $this->assertSame('Jalan besar', $entry->indonesian_word);
        $this->assertSame('Big road', $entry->english_word);
        $this->assertSame('Jalan yang luas', $entry->definition);
    }

    public function test_admin_can_export_dictionary_entries_to_csv(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        DictionaryEntry::create([
            'source_word' => 'Lalo',
            'target_word' => 'Jalan',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'status' => DictionaryEntry::STATUS_DRAFT,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/dictionary-export')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString('tolaki_word', $content);
        $this->assertStringContainsString('Lalo', $content);
    }

    public function test_csv_export_escapes_spreadsheet_formulas(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        DictionaryEntry::create([
            'source_word' => '=HYPERLINK("https://example.com")',
            'target_word' => '+SUM(1,1)',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
            'created_by' => $admin->id,
        ]);

        $content = $this->actingAs($admin)
            ->get('/admin/dictionary-export')
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('\'=HYPERLINK', $content);
        $this->assertStringContainsString('\'+SUM(1,1)', $content);
    }

    public function test_import_rejects_invalid_csv_header(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $file = UploadedFile::fake()->createWithContent('entries.csv', "tolaki_word,indonesian_word\nAma,Ayah");

        $this->actingAs($admin)
            ->from('/admin/dictionary-import')
            ->post('/admin/dictionary-import', ['csv_file' => $file])
            ->assertRedirect('/admin/dictionary-import')
            ->assertSessionHasErrors('csv_file');
    }

    public function test_import_rejects_unknown_csv_header(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $file = UploadedFile::fake()->createWithContent('entries.csv', implode("\n", [
            'tolaki_word,indonesian_word,english_word,part_of_speech,phonetic,definition,usage_note,cultural_note,category,status,tolaki_sentence,indonesian_sentence,english_sentence,example_notes,unknown_column',
            'Ama,Ayah,Father,noun,,,,,Keluarga,draft,,,,,nilai',
        ]));

        $this->actingAs($admin)
            ->from('/admin/dictionary-import')
            ->post('/admin/dictionary-import', ['csv_file' => $file])
            ->assertRedirect('/admin/dictionary-import')
            ->assertSessionHasErrors('csv_file');
    }

    public function test_editor_can_import_but_guest_cannot_access_import_page(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
        ]);

        $this->get('/admin/dictionary-import')
            ->assertRedirect('/login');

        $this->actingAs($editor)
            ->get('/admin/dictionary-import')
            ->assertOk();
    }

    public function test_editor_import_cannot_publish_entries_directly(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
        ]);

        $csv = implode("\n", [
            'tolaki_word,indonesian_word,english_word,part_of_speech,phonetic,definition,usage_note,cultural_note,category,status,tolaki_sentence,indonesian_sentence,english_sentence,example_notes',
            'Somba,Hormat,Respect,verb,,,,,Budaya,published,,,,',
        ]);

        $file = UploadedFile::fake()->createWithContent('entries.csv', $csv);

        $this->actingAs($editor)
            ->post('/admin/dictionary-import', ['csv_file' => $file])
            ->assertRedirect('/admin/dictionary-import');

        $this->assertSame(
            DictionaryEntry::STATUS_REVIEWED,
            DictionaryEntry::query()->where('tolaki_word', 'Somba')->value('status')
        );
    }

    public function test_dashboard_shows_visual_visitor_statistics(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $entry = DictionaryEntry::create([
            'source_word' => 'Ine',
            'target_word' => 'Ibu',
            'source_language' => 'Bahasa A',
            'target_language' => 'Bahasa Indonesia',
            'status' => DictionaryEntry::STATUS_PUBLISHED,
        ]);

        DictionaryVisit::create([
            'path' => 'dictionary',
            'route_name' => 'dictionary.index',
            'keyword' => 'ine',
            'ip_hash' => str_repeat('a', 64),
            'user_agent_hash' => str_repeat('b', 64),
            'occurred_on' => today(),
        ]);

        DictionaryVisit::create([
            'path' => "dictionary/{$entry->public_id}",
            'route_name' => 'dictionary.show',
            'dictionary_entry_id' => $entry->id,
            'ip_hash' => str_repeat('a', 64),
            'user_agent_hash' => str_repeat('d', 64),
            'occurred_on' => today(),
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertViewHas('visitCount', 1)
            ->assertViewHas('visitTodayCount', 1)
            ->assertSee('Tren Visitor Publik')
            ->assertSee('Tipe Aktivitas')
            ->assertSee('Entri populer 30 hari terakhir')
            ->assertDontSee('Ine');
    }

    public function test_public_user_cannot_open_internal_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('dictionary.index'));
    }

    public function test_assessor_has_read_only_backoffice_access(): void
    {
        $assessor = User::factory()->create([
            'role' => 'assessor',
        ]);

        $category = DictionaryCategory::create([
            'name' => 'Demo Asesor',
            'slug' => 'demo-asesor',
        ]);

        $entry = DictionaryEntry::create([
            'tolaki_word' => 'Laika',
            'indonesian_word' => 'Rumah',
            'english_word' => 'House',
            'category_id' => $category->id,
            'status' => DictionaryEntry::STATUS_PUBLISHED,
            'created_by' => $assessor->id,
        ]);

        $this->actingAs($assessor)
            ->get('/dashboard')
            ->assertOk();

        $this->actingAs($assessor)
            ->get('/admin/dictionary-entries')
            ->assertOk();

        $this->actingAs($assessor)
            ->get("/admin/dictionary-entries/{$entry->id}")
            ->assertOk();

        $this->actingAs($assessor)
            ->get('/admin/dictionary-entries/create')
            ->assertRedirect(route('dictionary.index'));

        $this->actingAs($assessor)
            ->get("/admin/dictionary-entries/{$entry->id}/edit")
            ->assertRedirect(route('dictionary.index'));

        $this->actingAs($assessor)
            ->get('/admin/dictionary-import')
            ->assertRedirect(route('dictionary.index'));

        $this->actingAs($assessor)
            ->get('/admin/dictionary-export')
            ->assertRedirect(route('dictionary.index'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Page;
use App\Models\User;
use App\Services\ImportExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected ImportExportService $importExportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importExportService = app(ImportExportService::class);
        Storage::fake('local');
    }

    public function test_can_export_pages()
    {
        $page = Page::factory()->create([
            'title' => ['en' => 'Test Page'],
            'slug' => 'test-page',
        ]);

        $exportData = $this->importExportService->exportPages([$page->id]);

        $this->assertEquals('pages', $exportData['type']);
        $this->assertCount(1, $exportData['data']);
        $this->assertEquals('Test Page', $exportData['data'][0]['title']['en']);
    }

    public function test_can_export_forms()
    {
        $form = Form::factory()->create([
            'name' => ['en' => 'Test Form'],
        ]);

        $exportData = $this->importExportService->exportForms([$form->id]);

        $this->assertEquals('forms', $exportData['type']);
        $this->assertCount(1, $exportData['data']);
        $this->assertEquals('Test Form', $exportData['data'][0]['name']['en']);
    }

    public function test_can_import_pages()
    {
        $exportData = [
            'type' => 'pages',
            'data' => [
                [
                    'title' => ['en' => 'Imported Page'],
                    'slug' => 'imported-page',
                    'status' => 'draft',
                ]
            ]
        ];

        $results = $this->importExportService->importPages($exportData);

        $this->assertEquals(1, $results['imported']);
        $this->assertEquals(0, $results['skipped']);
        $this->assertEmpty($results['errors']);

        $this->assertDatabaseHas('pages', [
            'slug' => 'imported-page',
        ]);
    }

    public function test_can_import_forms()
    {
        $exportData = [
            'type' => 'forms',
            'data' => [
                [
                    'name' => ['en' => 'Imported Form'],
                    'settings' => [],
                    'elements' => [],
                ]
            ]
        ];

        $results = $this->importExportService->importForms($exportData);

        $this->assertEquals(1, $results['imported']);
        $this->assertEquals(0, $results['skipped']);
        $this->assertEmpty($results['errors']);

        $this->assertDatabaseHas('forms', [
            'name->en' => 'Imported Form',
        ]);
    }

    public function test_import_export_page_requires_authentication()
    {
        $response = $this->get(route('admin.import-export.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_import_export_page_loads_for_authenticated_user()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('admin.import-export.index'));

        $response->assertOk();
        $response->assertSee('Import & Export');
    }

    public function test_export_buttons_are_present_in_resource_tables()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('admin.resources.users.index'));

        $response->assertOk();
        $response->assertSee('Export');
    }

    public function test_export_buttons_are_present_in_pages_index()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('admin.pages.index'));

        $response->assertOk();
        $response->assertSee('Export');
    }

    public function test_export_buttons_are_present_in_forms_index()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('admin.forms.index'));

        $response->assertOk();
        $response->assertSee('Export');
    }
} 
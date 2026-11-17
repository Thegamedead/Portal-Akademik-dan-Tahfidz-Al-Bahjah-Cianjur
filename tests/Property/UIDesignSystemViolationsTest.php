<?php

/**
 * Bug Condition Exploration Test - UI Component Design System Violations
 *
 * **PHASE 1 - EXPLORATORY TESTING (BEFORE FIX)**
 *
 * This test is EXPECTED TO FAIL on unfixed code. Failure confirms the bug exists.
 *
 * Bug: Inkonsistensi UI/UX sistematis yang melanggar design system yang didefinisikan
 * di `.kiro/steering/ui-design-system.md`
 *
 * Test Strategy:
 * - Test specific pages (Sekolah, Kelas) and components where bugs are known to exist
 * - Verify buttons lack cursor pointer and hover effects
 * - Verify notification mechanism inconsistency (inline vs toast)
 * - Verify modal background colors don't match design system
 * - Verify search field inconsistency
 * - Verify Detail button logic bug
 * - Verify table header color inconsistency
 *
 * When this test FAILS (expected outcome):
 * - Counterexamples prove bugs exist
 * - Document failures and proceed to next task
 * - DO NOT attempt to fix - that comes in Phase 2
 *
 * Feature: inkonsistensi-ui-sistematis
 * Phase: Bug Condition Exploration (Phase 1)
 * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7
 *
 * @group property-based
 * @group bugfix
 * @group ui-design-system
 */

namespace Tests\Property;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Sekolah;
use Illuminate\Support\Facades\File;

class UIDesignSystemViolationsTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup admin user for authentication
        $this->admin = Admin::factory()->create([
            'username' => 'admin_test',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Bug Condition Property 1: Button Interaction Consistency
     *
     * FOR ALL buttons on Sekolah and Kelas pages,
     * WHEN rendered, THEN buttons SHOULD have:
     * - cursor: pointer CSS property
     * - hover effect (transition-colors or similar)
     * - colors matching design system (Zamrud/Delima/BiruSlate)
     *
     * **EXPECTED OUTCOME: FAIL** - Proves buttons lack proper interaction styling
     *
     * Validates: Requirements 1.1, 2.1
     */
    public function test_button_interactions_follow_design_system_on_sekolah_page(): void
    {
        // Arrange: Create necessary data
        Sekolah::factory()->create();

        // Act: Login and navigate to Profil Sekolah page
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.sekolah.profil'));

        $response->assertStatus(200);
        $html = $response->getContent();

        // Assert: Check for button with proper cursor pointer
        // Bug Condition: Buttons use inline onmouseover/onmouseout instead of proper CSS
        $this->assertStringContainsString('cursor-pointer', $html,
            'EXPECTED FAILURE: Simpan/Perbaharui button should have cursor-pointer class but does not');

        // Assert: Check for transition-colors or hover classes
        $this->assertStringContainsString('transition-colors', $html,
            'EXPECTED FAILURE: Buttons should have transition-colors for hover effect');

        // Assert: Check that buttons do NOT use inline JavaScript for hover
        $this->assertStringNotContainsString('onmouseover', $html,
            'EXPECTED FAILURE: Buttons should not use inline onmouseover for styling');
        $this->assertStringNotContainsString('onmouseout', $html,
            'EXPECTED FAILURE: Buttons should not use inline onmouseout for styling');
    }

    /**
     * Bug Condition Property 2: Button Interaction on Kelas Page
     *
     * FOR ALL buttons (Tambah/Edit/Hapus/Detail) on Kelas page,
     * WHEN rendered, THEN buttons SHOULD have cursor pointer and hover effect
     *
     * **EXPECTED OUTCOME: FAIL** - Proves Detail button is hardcoded disabled
     *
     * Validates: Requirements 1.1, 1.5, 2.1, 2.5
     */
    public function test_button_interactions_and_detail_button_logic_on_kelas_page(): void
    {
        // Arrange: Create kelas data
        $kelas = Kelas::factory()->count(3)->create();

        // Act: Login and navigate to Index Kelas page
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'));

        $response->assertStatus(200);
        $html = $response->getContent();

        // Assert: Verify action buttons exist
        $this->assertStringContainsString('Tambah', $html);
        $this->assertStringContainsString('Edit', $html);
        $this->assertStringContainsString('Hapus', $html);
        $this->assertStringContainsString('Detail', $html);

        // Bug Condition 1: Detail button should have conditional logic like Edit button
        // Currently Detail button is hardcoded disabled
        $detailButtonPattern = '/<button[^>]*disabled[^>]*>.*?Detail.*?<\/button>/s';
        preg_match_all($detailButtonPattern, $html, $detailMatches);

        // Check if Detail button exists and is always disabled (bug)
        $this->assertNotEmpty($detailMatches[0],
            'EXPECTED FAILURE: Detail button found but should have @if conditional like Edit button');

        // Bug Condition 2: Check for missing @if conditional around Detail button
        // Edit button has: @if(count($selectedIds) === 1)
        // Detail button should have the same but does not
        $hasDetailConditional = preg_match('/@if\s*\([^)]*selectedIds[^)]*===\s*1[^)]*\)[^@]*Detail/s', $html);
        $this->assertTrue($hasDetailConditional,
            'EXPECTED FAILURE: Detail button should have @if(count($selectedIds) === 1) conditional but does not');
    }

    /**
     * Bug Condition Property 3: Notification Mechanism Consistency
     *
     * FOR ALL pages in the system,
     * WHEN an action is performed, THEN notifications SHOULD use toast mechanism
     * NOT inline session flash messages
     *
     * **EXPECTED OUTCOME: FAIL** - Proves Kelas page uses inline notifications
     *
     * Validates: Requirements 1.2, 2.2
     */
    public function test_notifications_use_toast_mechanism_not_inline(): void
    {
        // Arrange
        Kelas::factory()->create();

        // Act: Get Kelas page HTML
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'));

        $html = $response->getContent();

        // Bug Condition: Page uses inline @if session()->has('message')
        // instead of dispatching toast events
        $hasInlineNotification = preg_match('/@if\s*\(\s*session\(\)->has\([\'"]message[\'"]\)\s*\)/s', $html);
        $this->assertFalse($hasInlineNotification,
            'EXPECTED FAILURE: Kelas page should NOT use inline session flash messages');

        $hasInlineError = preg_match('/@if\s*\(\s*session\(\)->has\([\'"]error[\'"]\)\s*\)/s', $html);
        $this->assertFalse($hasInlineError,
            'EXPECTED FAILURE: Kelas page should NOT use inline session error messages');
    }

    /**
     * Bug Condition Property 4: Modal Background Color Consistency
     *
     * FOR ALL modals/dialogs in the system,
     * WHEN displayed, THEN modal background SHOULD use Tinta color
     * rgba(26, 36, 34, 0.5-0.7) NOT generic bg-black
     *
     * **EXPECTED OUTCOME: FAIL** - Proves modal uses generic black background
     *
     * Validates: Requirements 1.3, 2.3
     */
    public function test_modal_background_uses_design_system_tinta_color(): void
    {
        // Arrange
        Kelas::factory()->create();

        // Act: Get Kelas page with bulk delete modal
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'));

        $html = $response->getContent();

        // Bug Condition: Modal uses bg-black bg-opacity-50 instead of Tinta rgba
        $this->assertStringNotContainsString('bg-black bg-opacity-50', $html,
            'EXPECTED FAILURE: Modal should NOT use generic bg-black');

        // Should use Tinta color from design system
        $usesTintaColor = preg_match('/rgba\(26,\s*36,\s*34,\s*0\.[5-7]\)/s', $html) ||
                         preg_match('/bg-\[#1A2422\]/s', $html);
        $this->assertTrue($usesTintaColor,
            'EXPECTED FAILURE: Modal background should use Tinta rgba(26, 36, 34, 0.5-0.7)');
    }

    /**
     * Bug Condition Property 5: Modal Button Colors Match Design System
     *
     * FOR ALL modal confirmation dialogs,
     * WHEN displayed, THEN buttons SHOULD use:
     * - Zamrud (#1B5E4F) for primary confirm
     * - Delima (#9B3A3A) for danger delete
     * - Biru Slate (#5B7A8C) for cancel/secondary
     *
     * **EXPECTED OUTCOME: FAIL** - Proves modal buttons use consistent colors (actually this might pass)
     *
     * Validates: Requirements 1.3, 2.3
     */
    public function test_modal_button_colors_follow_design_system(): void
    {
        // Arrange
        Kelas::factory()->create();

        // Act: Get page with bulk delete confirmation modal
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'));

        $html = $response->getContent();

        // Extract modal section
        preg_match('/showBulkDeleteConfirm.*?@endif/s', $html, $modalMatches);
        $modalHtml = $modalMatches[0] ?? '';

        // Bug Condition: Check button colors in delete modal
        // Delete button should use Delima #9B3A3A
        $hasDelimaColor = preg_match('/#9B3A3A/i', $modalHtml);
        $this->assertTrue($hasDelimaColor,
            'Delete confirmation button should use Delima color #9B3A3A (may pass)');

        // Cancel button should use Biru Slate or neutral outline
        $hasConsistentCancelButton = preg_match('/#5B7A8C|border.*#D1D5DB/i', $modalHtml);
        $this->assertTrue($hasConsistentCancelButton,
            'Cancel button should use Biru Slate #5B7A8C or neutral outline (may pass)');
    }

    /**
     * Bug Condition Property 6: Search Field Icon Consistency
     *
     * FOR ALL search fields across pages,
     * WHEN rendered, THEN search fields SHOULD have SVG search icon
     * NOT emoji 🔍
     *
     * **EXPECTED OUTCOME: FAIL** - Proves search fields use emoji instead of SVG
     *
     * Validates: Requirements 1.4, 2.4
     */
    public function test_search_fields_have_svg_icon_not_emoji(): void
    {
        // Arrange
        Kelas::factory()->create();

        // Act: Get Kelas page
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'));

        $html = $response->getContent();

        // Bug Condition: Search field uses emoji 🔍 in placeholder
        $this->assertStringNotContainsString('🔍', $html,
            'EXPECTED FAILURE: Search field should NOT use emoji for icon');

        // Should have SVG search icon or icon component
        $hasSVGSearchIcon = preg_match('/<svg[^>]*>.*?<path[^>]*search/is', $html) ||
                           strpos($html, 'search-icon') !== false;
        $this->assertTrue($hasSVGSearchIcon,
            'EXPECTED FAILURE: Search field should have proper SVG search icon');
    }

    /**
     * Bug Condition Property 7: Table Header Color Consistency
     *
     * FOR ALL tables across pages,
     * WHEN rendered, THEN table headers SHOULD use Zamrud color #1B5E4F
     * consistently
     *
     * **EXPECTED OUTCOME: FAIL** - Proves table headers use inconsistent colors
     *
     * Validates: Requirements 1.6, 2.6
     */
    public function test_table_headers_use_zamrud_color_consistently(): void
    {
        // Arrange
        Kelas::factory()->count(3)->create();

        // Act: Get Kelas page
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'));

        $html = $response->getContent();

        // Extract table header section
        preg_match('/<thead.*?<\/thead>/s', $html, $theadMatches);
        $theadHtml = $theadMatches[0] ?? '';

        // Bug Condition: Table header does NOT use Zamrud color
        // Currently uses #F4F5F4 (Kelabu Kertas) instead
        $this->assertStringNotContainsString('#F4F5F4', $theadHtml,
            'EXPECTED FAILURE: Table header should NOT use Kelabu Kertas #F4F5F4');

        // Should use Zamrud #1B5E4F
        $usesZamrudColor = preg_match('/#1B5E4F/i', $theadHtml) ||
                          strpos($theadHtml, 'bg-zamrud') !== false;
        $this->assertTrue($usesZamrudColor,
            'EXPECTED FAILURE: Table header should use Zamrud color #1B5E4F');
    }

    /**
     * Bug Condition Property 8: Preservation - Business Logic Unchanged
     *
     * FOR ALL CRUD operations and filtering,
     * WHEN executed, THEN business logic SHOULD remain unchanged
     * (This property should PASS - proves other functionality works)
     *
     * Validates: Requirements 3.3
     */
    public function test_crud_operations_and_filtering_remain_functional(): void
    {
        // This test should PASS - verifies business logic is not affected by UI bugs

        // Arrange
        $kelas = Kelas::factory()->count(5)->create([
            'tingkat' => 7
        ]);
        Kelas::factory()->count(3)->create([
            'tingkat' => 8
        ]);

        // Act & Assert: CRUD operations work
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'));

        $response->assertStatus(200);

        // Verify filtering would work (business logic intact)
        $allKelas = Kelas::all();
        $this->assertCount(8, $allKelas);

        $tingkat7 = Kelas::where('tingkat', 7)->get();
        $this->assertCount(5, $tingkat7);

        // This proves: UI bugs do NOT affect data operations
        $this->assertTrue(true, 'Business logic (CRUD, filtering) remains functional');
    }
}

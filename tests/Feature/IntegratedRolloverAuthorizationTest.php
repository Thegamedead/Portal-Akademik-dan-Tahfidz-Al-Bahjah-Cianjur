<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Feature Tests for IntegratedRolloverModal Authorization
 *
 * Tests authorization requirements for the integrated rollover system.
 * Validates that only authenticated Admin users can access the rollover interface.
 *
 * Requirements tested: 19.1, 19.2, 19.3, 19.4, 19.5
 */
class IntegratedRolloverAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that authenticated admin can access rollover interface
     *
     * Requirement 19.1: Verify that the current user is authenticated as Admin
     * before displaying rollover interface
     */
    public function test_authenticated_admin_can_access_rollover_interface(): void
    {
        // Arrange: Create and authenticate as admin
        $admin = Admin::factory()->create();

        // Act: Access rollover page as authenticated admin
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.rollover-tahun-ajaran'));

        // Assert: Admin can access the page
        $response->assertOk();
        $response->assertSeeLivewire('admin.integrated-rollover-modal');
    }

    /**
     * Test that unauthenticated users are redirected to login
     *
     * Requirement 19.3: IF an unauthenticated user attempts rollover,
     * THEN THE Rollover_System SHALL return HTTP 401 Unauthorized response
     *
     * Note: In web context, Laravel redirects to login instead of 401
     */
    public function test_unauthenticated_user_cannot_access_rollover(): void
    {
        // Act: Try to access rollover page without authentication
        $response = $this->get(route('admin.rollover-tahun-ajaran'));

        // Assert: Redirected to admin login page
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test that authenticated guru cannot access admin rollover interface
     *
     * Requirement 19.4: IF a non-Admin user attempts rollover,
     * THEN THE Rollover_System SHALL return HTTP 403 Forbidden response
     *
     * Note: In web context, middleware redirects to their own dashboard
     */
    public function test_authenticated_guru_cannot_access_admin_rollover(): void
    {
        // Arrange: Create and authenticate as guru
        $guru = Guru::factory()->create();

        // Act: Try to access admin rollover page as guru
        $response = $this->actingAs($guru, 'guru')
            ->get(route('admin.rollover-tahun-ajaran'));

        // Assert: Redirected to guru dashboard (guard isolation)
        $response->assertRedirect(route('guru.dashboard'));
    }

    /**
     * Test that authenticated siswa cannot access admin rollover interface
     *
     * Requirement 19.4: IF a non-Admin user attempts rollover,
     * THEN THE Rollover_System SHALL return HTTP 403 Forbidden response
     */
    public function test_authenticated_siswa_cannot_access_admin_rollover(): void
    {
        // Arrange: Create and authenticate as siswa
        $siswa = Siswa::factory()->create();

        // Act: Try to access admin rollover page as siswa
        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('admin.rollover-tahun-ajaran'));

        // Assert: Redirected to siswa dashboard (guard isolation)
        $response->assertRedirect(route('siswa.dashboard'));
    }

    /**
     * Test that route name is correctly registered
     *
     * Validates that the route can be accessed via its named route
     */
    public function test_rollover_route_is_correctly_named(): void
    {
        // Assert: Route exists with correct name
        $this->assertTrue(
            \Route::has('admin.rollover-tahun-ajaran'),
            'Route admin.rollover-tahun-ajaran should be registered'
        );
    }

    /**
     * Test that route has correct middleware applied
     *
     * Validates middleware configuration for security
     */
    public function test_rollover_route_has_correct_middleware(): void
    {
        // Get route instance
        $route = \Route::getRoutes()->getByName('admin.rollover-tahun-ajaran');

        // Assert: Route has required middleware
        $middleware = $route->middleware();

        $this->assertContains('web', $middleware, 'Route should have web middleware');
        $this->assertTrue(
            in_array('auth.admin', $middleware) ||
            in_array(\App\Http\Middleware\AuthenticateAdmin::class, $middleware),
            'Route should have admin authentication middleware'
        );
    }
}

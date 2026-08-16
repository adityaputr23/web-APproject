<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Enquiry;
use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        Setting::create(['key' => 'email', 'value' => 'test@apvisuals.com']);
        Setting::create(['key' => 'hero_subtitle', 'value' => 'A creative portfolio test.']);
    }

    /**
     * Test public landing page loads and logs page views.
     */
    public function test_portfolio_landing_page_loads_and_logs_view()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('APVISUALS');
        $response->assertSee('A creative portfolio test.');

        // Verify page view was logged in database
        $this->assertDatabaseCount('page_views', 1);
    }

    /**
     * Test AJAX contact form submissions store correctly.
     */
    public function test_contact_form_saves_enquiry_successfully()
    {
        $response = $this->postJson('/enquire', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Website Build',
            'message' => 'Hello APVISUALS, I want a modern portfolio.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Pesan berhasil dikirim! Kami akan membalas dalam 24 jam. 🚀',
        ]);

        $this->assertDatabaseHas('enquiries', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Website Build',
            'message' => 'Hello APVISUALS, I want a modern portfolio.',
        ]);
    }

    /**
     * Test unauthenticated guest is redirected from admin panel to login.
     */
    public function test_guest_is_redirected_from_admin_panel()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated admin user can access dashboard.
     */
    public function test_authenticated_admin_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Welcome back');
    }
}

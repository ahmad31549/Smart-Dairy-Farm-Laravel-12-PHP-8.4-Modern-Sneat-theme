<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_login_and_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Total Animals');
    }

    public function test_pending_employee_cannot_access_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'pending',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}

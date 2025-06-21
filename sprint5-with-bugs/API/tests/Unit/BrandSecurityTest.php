<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BrandSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteBrandWithoutAdminTokenShouldFail()
    {
        // Create a regular user (non-admin)
        $user = User::factory()->create(['role' => 'customer']);
        $brand = Brand::factory()->create();

        // Try to delete brand without admin privileges
        $response = $this->actingAs($user, 'api')
            ->deleteJson("/brands/{$brand->id}");

        // Should return 403 Forbidden, not allow deletion
        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
        
        // Brand should still exist
        $this->assertDatabaseHas('brands', ['id' => $brand->id]);
    }

    public function testDeleteBrandWithAdminTokenShouldSucceed()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);
        $brand = Brand::factory()->create();

        // Try to delete brand with admin privileges
        $response = $this->actingAs($admin, 'api')
            ->deleteJson("/brands/{$brand->id}");

        // Should succeed
        $response->assertStatus(ResponseAlias::HTTP_OK);
    }
}
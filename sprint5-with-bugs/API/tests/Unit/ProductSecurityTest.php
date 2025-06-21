<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function testProductApiShouldNotRevealStockToCustomers()
    {
        // Create a product with stock information
        $product = Product::factory()->create([
            'stock' => 50,
            'is_location_offer' => true
        ]);

        // Make request as non-admin user
        $customer = User::factory()->create(['role' => 'customer']);
        
        $response = $this->actingAs($customer, 'api')
            ->getJson('/products');

        $response->assertStatus(ResponseAlias::HTTP_OK);
        
        // Check that stock information is not exposed to customers
        $responseData = $response->json();
        
        foreach ($responseData['data'] as $productData) {
            $this->assertArrayNotHasKey('stock', $productData, 
                'Stock information should not be revealed to customers');
            $this->assertArrayNotHasKey('is_location_offer', $productData,
                'Location offer information should not be revealed to customers');
        }
    }

    public function testProductApiShouldRevealStockToAdmins()
    {
        // Create a product with stock information
        $product = Product::factory()->create([
            'stock' => 50
        ]);

        // Make request as admin user
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin, 'api')
            ->getJson('/products');

        $response->assertStatus(ResponseAlias::HTTP_OK);
        
        // Admin should be able to see stock information
        $responseData = $response->json();
        $this->assertArrayHasKey('stock', $responseData['data'][0] ?? [],
            'Admin should be able to see stock information');
    }
}
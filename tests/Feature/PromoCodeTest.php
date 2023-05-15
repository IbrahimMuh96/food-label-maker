<?php

namespace Tests\Feature;

use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PromoCodeTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $admin_role = Role::create([
            "name" => "admin",
            "guard_name" => "api"
        ]);

        $user_role = Role::create([
            "name" => "user",
            "guard_name" => "api"
        ]);


        $create_permission = Permission::create(['name' => 'create promo code', 'guard_name' => 'api']);
        $admin_role->syncPermissions($create_permission);

        $use_permission = Permission::create(['name' => 'use promo code', 'guard_name' => 'api']);
        $user_role->syncPermissions($use_permission);
    }

    /**
     * Test that a promo code is valid when it satisfies all the conditions.
     */
    public function testValidPromoCode()
    {
        $code = uniqid();

        $promo_code = PromoCode::factory()->create([
            'code' => $code,
            "expiry_date" => now()->addDays(1),
            "usage_count" => 5,
            "usage_count_per_user" => 3,
            "type" => "value",
            "discount" => 1.5,
        ]);

        $user = User::factory()->create();

        $user->assignRole('user');

        $this->actingAs($user, 'sanctum');


        $response = $this->post('api/promo-code/use', [
            'promo_code' => $promo_code->code,
            'price' => 12.5
        ]);

        $response->assertSuccessful();

        // Check that the response data indicates that the promo code is valid.
        $response->assertJson([
            'message' => "Promo Code Applied",
        ]);
    }

    /**
     * Test that a promo code is invalid when it is after its expiry date.
     */
    public function testPromoCodeInvalidAfterExpiryDate()
    {
        $code = uniqid();

        $promo_code = PromoCode::factory()->create([
            'code' => $code,
            "expiry_date" => now()->subDay(),
            "usage_count" => 5,
            "usage_count_per_user" => 3,
            "type" => "value",
            "discount" => 1.5
        ]);

        $user = User::factory()->create();

        $user->assignRole('user');

        $this->actingAs($user, 'sanctum');

        $response = $this->post('api/promo-code/use', [
            'promo_code' => $promo_code->code,
            'price' => 12.5
        ]);

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Invalid Promo Code',
        ]);
    }

    /**
     * Test that a promo code is invalid when a user has used it more than the maximum number of times.
     */
    public function testPromoCodeInvalidAfterMaxUsagePerUser()
    {
        $code = uniqid();

        $promo_code = PromoCode::factory()->create([
            'code' => $code,
            "expiry_date" => now()->addDays(2),
            "usage_count" => 5,
            "usage_count_per_user" => 3,
            "type" => "value",
            "discount" => 1.5
        ]);

        $user = User::factory()->create();

        $user->assignRole('user');

        $this->actingAs($user, 'sanctum');

        for ($i = 0; $i < 4; $i++) {
            $this->post('api/promo-code/use', [
                'promo_code' => $promo_code->code,
                'price' => 12.5
            ]);
        }

        $response = $this->post('api/promo-code/use', [
            'promo_code' => $promo_code->code,
            'price' => 12.5
        ]);

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Invalid Promo Code',
        ]);
    }

    /**
     * Test that a promo code is invalid when it has been used more than the maximum number of times.
     */
    public function testPromoCodeInvalidAfterMaxUsage()
    {
        $code = uniqid();

        $promo_code = PromoCode::factory()->create([
            'code' => $code,
            "expiry_date" => now()->addDays(2),
            "usage_count" => 5,
            "usage_count_per_user" => 3,
            "type" => "value",
            "discount" => 1.5
        ]);

        $users = User::factory()->count(5)->create();

        foreach ($users as $user) {
            $user->assignRole('user');

            $this->actingAs($user, 'sanctum');

            $this->post('api/promo-code/use', [
                'promo_code' => $promo_code->code,
                'price' => 12.5
            ]);
        }

        $response = $this->post('api/promo-code/use', [
            'promo_code' => $promo_code->code,
            'price' => 12.5
        ]);

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Invalid Promo Code',
        ]);
    }
}

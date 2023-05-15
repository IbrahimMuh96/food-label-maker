<?php

namespace Tests\Feature;

use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PromoCodeUsageTest extends TestCase
{
    use DatabaseTransactions;

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
     * Test that a non-admin user can't add a promo code.
     */
    public function testNonAdminUserCannotAddPromoCode()
    {
        $user = User::factory()->create();

        $user->assignRole('user');

         $this->actingAs($user, 'sanctum');

        // Generate a random promo code.
        $code = uniqid();

        $response = $this->post('api/promo-code/create', [
            'code' => $code,
            "expiry_date" => now()->addDays(1),
            "usage_count" => 2,
            "usage_count_per_user" => 1,
            "type" => "value",
            "discount" => 1.5,
        ]);

        $response->assertStatus(403);

        // Check that the promo code was not created in the database.
        $this->assertDatabaseMissing('promo_codes', [
            'code' => $code,
            'discount' => 1.5,
        ]);
    }

    /**
     * Test that an admin user can add a promo code.
     */
    public function testAdminUserCanAddPromoCode()
    {
        $user = User::factory()->create();

        $user->assignRole('admin');

        $this->actingAs($user, 'sanctum');

        $code = uniqid();

        $response = $this->post('api/promo-code/create', [
            'promo_code' => $code,
            "expiry_date" => now()->addDays(1),
            "usage_count" => 2,
            "usage_count_per_user" => 1,
            "type" => "value",
            "discount" => 1.5,
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('promo_codes', [
            'code' => $code,
            'discount' => 1.5,
        ]);
    }

    /**
     * Test that a non-admin user can't use a promo code.
     */
    public function testNonAdminUserCanUsePromoCode()
    {
        // Create a non-admin user to authenticate as.
        $user = User::factory()->create();

        $user->assignRole('user');

        $this->actingAs($user, 'sanctum');

        // Create a promo code in the database.
        $promo_code = PromoCode::factory()->create([
            "expiry_date" => now()->addDays(1),
            "usage_count" => 2,
            "usage_count_per_user" => 1,
            "type" => "value",
            "discount" => 1.5,
        ]);

        $response = $this->post('api/promo-code/use', [
            'promo_code' => $promo_code->code,
            'price' => 12.66
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('promo_code_usages', [
            'user_id' => $user->id,
            'promo_code_id' => $promo_code->id,
        ]);
    }

    /**
     * Test that an admin user can use a promo code.
     */
    public function testAdminUserCannotUsePromoCode()
    {
        // Create an admin user to authenticate as.
        $user = User::factory()->create();


        $user->assignRole('admin');

        $this->actingAs($user, 'sanctum');

        $promo_code = PromoCode::factory()->create([
            "expiry_date" => now()->addDays(1),
            "usage_count" => 2,
            "usage_count_per_user" => 1,
            "type" => "value",
            "discount" => 1.5,
        ]);

        $response = $this->post('api/promo-code/use', [
            'promo_code' => $promo_code->code,
            'price' => 12.66
        ]);

        $response->assertStatus(403);


        $this->assertDatabaseMissing('promo_code_usages', [
            'user_id' => $user->id,
            'promo_code_id' => $promo_code->id,
        ]);
    }
}


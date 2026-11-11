<?php

namespace Tests\Feature\Travel;

use App\Models\Travel\TravelVendorControl;
use App\Models\Travel\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterVendorTest extends TestCase
{
    use RefreshDatabase;

    protected User $hrdUser;
    protected User $adminUser;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic permissions and roles
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        // HR/GA HO uses existing HRD role
        $this->hrdUser = User::create([
            'username' => 'hrd.travel',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->hrdUser->assignRole('HRD');

        // System Administrator does not automatically have travel operational access
        $this->adminUser = User::create([
            'username' => 'system.admin',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('System Administrator');

        $this->unauthorizedUser = User::create([
            'username' => 'employee.regular',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->unauthorizedUser->assignRole('Employee');
    }

    public function test_guest_is_redirected_to_login_from_vendor_routes(): void
    {
        $response = $this->get(route('travel.vendors.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_cannot_access_vendor_management(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('travel.vendors.index'));
        $response->assertStatus(403);
    }

    public function test_system_admin_without_travel_permission_cannot_access_vendor_management(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('travel.vendors.index'));
        $response->assertStatus(403);
    }

    public function test_authorized_hrd_user_can_view_vendor_list(): void
    {
        $vendor = Vendor::create([
            'code' => 'VEND-01',
            'name' => 'PT Antavaya Tour',
            'email' => 'corporate@antavaya.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->hrdUser)->get(route('travel.vendors.index'));
        $response->assertStatus(200);
        $response->assertSee('PT Antavaya Tour');
        $response->assertSee('VEND-01');
    }

    public function test_authorized_hrd_user_can_create_new_vendor(): void
    {
        $payload = [
            'code' => 'VEND-BAYU',
            'name' => 'PT Bayu Buana Travel',
            'legal_name' => 'PT Bayu Buana Tbk',
            'email' => 'corp@bayubuana.com',
            'phone' => '021-2345678',
            'contact_person' => 'Budi Santoso',
            'address' => 'Jl. Ir. H. Juanda No. 12, Jakarta',
            'notes' => 'Rekanan korporat rute domestik & internasional',
        ];

        $response = $this->actingAs($this->hrdUser)->post(route('travel.vendors.store'), $payload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('vendors', [
            'code' => 'VEND-BAYU',
            'name' => 'PT Bayu Buana Travel',
            'is_active' => false,
        ]);
    }

    public function test_authorized_hrd_user_can_update_vendor(): void
    {
        $vendor = Vendor::create([
            'code' => 'VEND-PWT',
            'name' => 'PT Panorama World',
            'email' => 'corp@panorama.com',
            'phone' => '021-888899',
            'is_active' => false,
        ]);

        $payload = [
            'code' => 'VEND-PWT-MOD',
            'name' => 'PT Panorama Destination Tbk',
            'legal_name' => 'PT Panorama Destination Tbk',
            'email' => 'updated@panorama.com',
            'phone' => '021-999900',
            'contact_person' => 'Dewi',
            'address' => 'Tomang, Jakarta Barat',
            'notes' => 'Data diperbarui',
        ];

        $response = $this->actingAs($this->hrdUser)->put(route('travel.vendors.update', $vendor), $payload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'code' => 'VEND-PWT-MOD',
            'name' => 'PT Panorama Destination Tbk',
            'email' => 'updated@panorama.com',
        ]);
    }

    public function test_single_active_vendor_activation_and_concurrency_control(): void
    {
        $vendorA = Vendor::create([
            'code' => 'VEND-ACT-A',
            'name' => 'Vendor A',
            'email' => 'a@vendor.com',
            'is_active' => false,
        ]);

        $vendorB = Vendor::create([
            'code' => 'VEND-ACT-B',
            'name' => 'Vendor B',
            'email' => 'b@vendor.com',
            'is_active' => false,
        ]);

        // 1. Activate Vendor A
        $responseA = $this->actingAs($this->hrdUser)->post(route('travel.vendors.activate', $vendorA), [
            'effective_date' => '2026-11-01',
            'reason' => 'Penunjukan rekanan periode 2026',
        ]);
        $responseA->assertStatus(302);

        $this->assertTrue($vendorA->fresh()->is_active);
        $this->assertFalse($vendorB->fresh()->is_active);
        $this->assertDatabaseHas('travel_vendor_controls', [
            'id' => 1,
            'active_vendor_id' => $vendorA->id,
        ]);
        $this->assertDatabaseHas('vendor_active_histories', [
            'vendor_id' => $vendorA->id,
            'deactivated_date' => null,
            'changed_by' => $this->hrdUser->id,
        ]);
        $this->assertEquals('2026-11-01', \App\Models\Travel\VendorActiveHistory::where('vendor_id', $vendorA->id)->value('effective_date')->format('Y-m-d'));

        // 2. Activate Vendor B (Vendor A must be automatically deactivated)
        $responseB = $this->actingAs($this->hrdUser)->post(route('travel.vendors.activate', $vendorB), [
            'effective_date' => '2026-11-15',
            'reason' => 'Pergantian rekanan utama baru',
        ]);
        $responseB->assertStatus(302);

        $this->assertFalse($vendorA->fresh()->is_active);
        $this->assertTrue($vendorB->fresh()->is_active);

        // Check singleton control row points to Vendor B
        $this->assertDatabaseHas('travel_vendor_controls', [
            'id' => 1,
            'active_vendor_id' => $vendorB->id,
        ]);

        // Check Vendor A history is closed
        $this->assertDatabaseHas('vendor_active_histories', [
            'vendor_id' => $vendorA->id,
        ]);
        $this->assertNotNull(\App\Models\Travel\VendorActiveHistory::where('vendor_id', $vendorA->id)->first()->deactivated_date);

        // Check Vendor B history is opened
        $this->assertDatabaseHas('vendor_active_histories', [
            'vendor_id' => $vendorB->id,
            'deactivated_date' => null,
        ]);
        $this->assertEquals('2026-11-15', \App\Models\Travel\VendorActiveHistory::where('vendor_id', $vendorB->id)->value('effective_date')->format('Y-m-d'));
    }
}

<?php

namespace Tests\Feature\BusinessCard;

use App\Models\CostLevel;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeBusinessCard;
use App\Models\Position;
use App\Services\BusinessCard\DigitalBusinessCardService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeBusinessCardTest extends TestCase
{
    use RefreshDatabase;

    protected DigitalBusinessCardService $service;
    protected Division $division;
    protected Position $position;
    protected CostLevel $costLevel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DigitalBusinessCardService();

        $this->division = Division::create([
            'name' => 'Human Resources',
            'code' => 'HR',
            'is_active' => true,
        ]);

        $this->position = Position::create([
            'name' => 'HR Specialist',
            'division_id' => $this->division->id,
            'description' => 'HR Specialist Role',
        ]);

        $this->costLevel = CostLevel::create([
            'name' => 'Staff',
            'is_active' => true,
        ]);
    }

    protected function createEmployee(array $attributes = []): Employee
    {
        return Employee::create(array_merge([
            'nik' => 'EMP-' . uniqid(),
            'full_name' => 'Ahmad Pratama',
            'email' => 'ahmad.' . uniqid() . '@rapidinfrastruktur.com',
            'religion' => 'Islam',
            'gender' => 'Male',
            'phone_number' => '081299998888',
            'division_id' => $this->division->id,
            'position_id' => $this->position->id,
            'cost_level_id' => $this->costLevel->id,
            'employee_status' => 'Permanent',
            'spd_limit' => 10000000,
            'total_annual_leave' => 12,
        ], $attributes));
    }

    /**
     * Test business card record creation with default privacy flags.
     */
    public function test_business_card_created_with_default_privacy_flags(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);

        $this->assertDatabaseHas('employee_business_cards', [
            'employee_id' => $employee->id,
            'public_token' => $card->public_token,
            'is_active' => false,
            'show_phone' => false,
            'show_email' => false,
            'show_photo' => false,
            'show_department' => false,
            'show_location' => false,
            'custom_title' => null,
            'scan_count' => 0,
        ]);

        $this->assertFalse($card->is_active);
        $this->assertFalse($card->show_phone);
        $this->assertFalse($card->show_email);
        $this->assertFalse($card->show_photo);
        $this->assertFalse($card->show_department);
        $this->assertFalse($card->show_location);
        $this->assertSame(0, $card->scan_count);
        $this->assertNull($card->last_scanned_at);
    }

    /**
     * Test token idempotency: repeated requests return the same record and token.
     */
    public function test_token_idempotency_returns_same_token(): void
    {
        $employee = $this->createEmployee();

        $card1 = $this->service->getOrCreateCard($employee);
        $card2 = $this->service->getOrCreateCard($employee);

        $this->assertSame($card1->id, $card2->id);
        $this->assertSame($card1->public_token, $card2->public_token);
        $this->assertSame(1, EmployeeBusinessCard::where('employee_id', $employee->id)->count());
    }

    /**
     * Test unique constraint prevents duplicate business card records per employee.
     */
    public function test_unique_constraint_prevents_duplicate_card_per_employee(): void
    {
        $employee = $this->createEmployee();
        $this->service->getOrCreateCard($employee);

        $this->expectException(QueryException::class);

        // Attempting direct raw insertion with same employee_id must fail unique constraint
        EmployeeBusinessCard::create([
            'employee_id' => $employee->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test unique constraint on public_token.
     */
    public function test_unique_constraint_on_public_token(): void
    {
        $employee1 = $this->createEmployee();
        $employee2 = $this->createEmployee();

        $card1 = $this->service->getOrCreateCard($employee1);

        $this->expectException(QueryException::class);

        // Attempt to insert another card with duplicate public_token
        $card2 = new EmployeeBusinessCard([
            'employee_id' => $employee2->id,
            'is_active' => false,
        ]);
        $card2->public_token = $card1->public_token;
        $card2->save();
    }

    /**
     * Test mass assignment protection: public_token cannot be modified via mass assignment.
     */
    public function test_public_token_is_guarded_from_mass_assignment(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);
        $originalToken = $card->public_token;

        $card->update([
            'public_token' => 'hacked_fake_token_attempt_123456789012345678901234567890123456',
            'custom_title' => 'Lead Engineer',
        ]);

        $card->refresh();

        $this->assertSame($originalToken, $card->public_token, 'public_token must not be modified by mass assignment');
        $this->assertSame('Lead Engineer', $card->custom_title);
    }

    /**
     * Test Employee and Business Card relationships.
     */
    public function test_bidirectional_relationship(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);

        $this->assertInstanceOf(EmployeeBusinessCard::class, $employee->businessCard);
        $this->assertSame($card->id, $employee->businessCard->id);

        $this->assertInstanceOf(Employee::class, $card->employee);
        $this->assertSame($employee->id, $card->employee->id);
    }

    /**
     * Test cascade deletion: deleting employee deletes business card record.
     */
    public function test_cascade_delete_on_employee_force_delete(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);

        $cardId = $card->id;
        $employee->forceDelete();

        $this->assertDatabaseMissing('employee_business_cards', ['id' => $cardId]);
    }

    /**
     * Test employee updates do not rotate the token.
     */
    public function test_employee_update_does_not_rotate_token(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);
        $tokenBefore = $card->public_token;

        $employee->update([
            'full_name' => 'Ahmad Pratama Updated',
            'phone_number' => '081200001111',
        ]);

        $cardAfter = $this->service->getOrCreateCard($employee);

        $this->assertSame($tokenBefore, $cardAfter->public_token);
    }

    /**
     * Test privacy settings update.
     */
    public function test_update_settings(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);

        $updated = $this->service->updateSettings($card, [
            'is_active' => true,
            'show_phone' => true,
            'show_email' => true,
            'show_photo' => true,
            'show_department' => true,
            'show_location' => true,
            'custom_title' => 'Senior Technical Lead',
        ]);

        $this->assertTrue($updated->is_active);
        $this->assertTrue($updated->show_phone);
        $this->assertTrue($updated->show_email);
        $this->assertTrue($updated->show_photo);
        $this->assertTrue($updated->show_department);
        $this->assertTrue($updated->show_location);
        $this->assertSame('Senior Technical Lead', $updated->custom_title);
    }

    /**
     * Test recording a scan event increments count and updates timestamp.
     */
    public function test_record_scan(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);

        $this->assertSame(0, $card->scan_count);
        $this->assertNull($card->last_scanned_at);

        $this->service->recordScan($card);
        $card->refresh();

        $this->assertSame(1, $card->scan_count);
        $this->assertNotNull($card->last_scanned_at);
        $this->assertInstanceOf(Carbon::class, $card->last_scanned_at);
    }

    /**
     * Test soft-deleted employee fails eligibility even with active card.
     */
    public function test_soft_deleted_employee_fails_eligibility(): void
    {
        $employee = $this->createEmployee();
        $card = $this->service->getOrCreateCard($employee);
        $this->service->updateSettings($card, ['is_active' => true]);

        $this->assertTrue($this->service->isEligibleForPublic($employee, $card));

        // Soft delete employee
        $employee->delete();
        $employee->refresh();

        $this->assertTrue($employee->trashed());
        $this->assertFalse($this->service->isEligibleForPublic($employee, $card));
    }

    /**
     * Test migration can be rolled back safely and cleanly.
     */
    public function test_migration_rollback_and_reapplication_works(): void
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('employee_business_cards'));

        \Illuminate\Support\Facades\Artisan::call('migrate:rollback', [
            '--step' => 1,
        ]);

        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasTable('employee_business_cards'));

        \Illuminate\Support\Facades\Artisan::call('migrate');

        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('employee_business_cards'));
    }
}

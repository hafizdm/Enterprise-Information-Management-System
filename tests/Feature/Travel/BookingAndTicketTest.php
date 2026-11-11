<?php

namespace Tests\Feature\Travel;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Project;
use App\Models\Travel\TravelBooking;
use App\Models\Travel\TravelBookingService;
use App\Models\Travel\TravelInquiry;
use App\Models\Travel\TravelRequest;
use App\Models\Travel\TravelRequestPassenger;
use App\Models\Travel\TravelTicket;
use App\Models\Travel\Vendor;
use App\Models\Travel\VendorQuotationPackage;
use App\Models\Travel\VendorQuotationService;
use App\Models\User;
use App\Services\Travel\VendorLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingAndTicketTest extends TestCase
{
    use RefreshDatabase;

    protected User $hrdUser;
    protected User $siteUser;
    protected User $unauthorizedUser;
    protected Vendor $vendor;
    protected TravelRequest $travelRequest;
    protected TravelInquiry $inquiry;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $division = Division::create(['name' => 'HR', 'code' => 'HRD', 'is_active' => true]);
        $position = Position::create(['name' => 'Staff', 'code' => 'HRD-01', 'division_id' => $division->id, 'is_active' => true]);

        $project = Project::create([
            'name' => 'Proyek IKN Tol',
            'location' => 'Penajam Paser Utara',
            'cost_center' => 'CC-IKN-01',
        ]);

        // HR/GA HO uses role HRD
        $this->hrdUser = User::create([
            'username' => 'hrd.travel',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->hrdUser->assignRole('HRD');

        // HR/GA Site uses role HR/GA Site
        $this->siteUser = User::create([
            'username' => 'site.hr',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->siteUser->assignRole('HR/GA Site');

        $this->unauthorizedUser = User::create([
            'username' => 'employee.regular',
            'password' => bcrypt('password'),
            'must_change_password' => false,
            'is_active' => true,
        ]);
        $this->unauthorizedUser->assignRole('Employee');

        $this->vendor = Vendor::create([
            'code' => 'VEND-MAIN',
            'name' => 'PT Rekanan Utama Tour',
            'email' => 'contact@rekananutama.com',
            'is_active' => true,
        ]);

        app(VendorLifecycleService::class)->activateVendor($this->vendor->id, '2026-11-01', 'Initial activation', $this->hrdUser->id);

        $this->travelRequest = TravelRequest::create([
            'request_number' => 'TR-202611-0005',
            'source_type' => 'project',
            'project_id' => $project->id,
            'travel_category' => 'domestic',
            'status' => 'in_inquiry',
            'request_date' => now()->toDateString(),
            'departure_date' => now()->addDays(5)->toDateString(),
            'origin' => 'Jakarta (CGK)',
            'destination' => 'Balikpapan (BPN)',
            'need_flight' => 1,
            'need_hotel' => 1,
            'created_by' => $this->siteUser->id,
        ]);

        TravelRequestPassenger::create([
            'travel_request_id' => $this->travelRequest->id,
            'passenger_name' => 'Ahmad Dahlan',
            'id_card_type' => 'ktp',
            'id_card_number' => '3201012345678901',
            'phone_number' => '081234567890',
            'gender' => 'male',
        ]);

        $this->inquiry = TravelInquiry::create([
            'inquiry_number' => 'INQ-202611-0005',
            'travel_request_id' => $this->travelRequest->id,
            'vendor_id' => $this->vendor->id,
            'status' => 'sent',
            'sent_at' => now(),
            'created_by' => $this->hrdUser->id,
        ]);
    }

    public function test_hrd_can_input_quotation_received_from_vendor_via_email(): void
    {
        $payload = [
            'package_name' => 'Opsi A - Penerbangan Garuda + Hotel Aston',
            'valid_until' => now()->addDay()->toDateTimeString(),
            'vendor_notes' => 'Penawaran diterima via email dari sales PT Rekanan Utama Tour.',
            'services' => [
                [
                    'service_type' => 'flight',
                    'service_name' => 'Garuda Indonesia GA-582 (CGK-BPN)',
                    'service_date' => now()->addDays(5)->toDateString(),
                    'service_details' => '07:15 - 10:20 Ekonomi',
                    'estimated_cost' => 1850000,
                ],
                [
                    'service_type' => 'hotel',
                    'service_name' => 'Hotel Aston Balikpapan',
                    'service_date' => now()->addDays(5)->toDateString(),
                    'service_details' => 'Deluxe Room 2 Malam',
                    'estimated_cost' => 1500000,
                ],
            ],
        ];

        $response = $this->actingAs($this->hrdUser)->post(route('travel.quotations.store', $this->inquiry), $payload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('vendor_quotation_packages', [
            'travel_inquiry_id' => $this->inquiry->id,
            'vendor_id' => $this->vendor->id,
            'package_name' => 'Opsi A - Penerbangan Garuda + Hotel Aston',
            'total_estimated_cost' => 3350000,
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('vendor_quotation_services', [
            'service_type' => 'flight',
            'service_name' => 'Garuda Indonesia GA-582 (CGK-BPN)',
            'estimated_cost' => 1850000,
        ]);

        $this->assertEquals('quoted', $this->inquiry->fresh()->status);
        $this->assertEquals('quoted', $this->travelRequest->fresh()->status);
    }

    public function test_authorized_user_can_select_quotation_which_creates_confirmed_booking(): void
    {
        // 1. Create 2 quotation packages (Opsi 1 and Opsi 2)
        $pkg1 = VendorQuotationPackage::create([
            'quotation_number' => 'QUO-202611-0001',
            'travel_inquiry_id' => $this->inquiry->id,
            'vendor_id' => $this->vendor->id,
            'package_name' => 'Opsi 1 - Garuda',
            'total_estimated_cost' => 3350000,
            'valid_until' => now()->addDay(),
            'status' => 'submitted',
        ]);

        VendorQuotationService::create([
            'vendor_quotation_package_id' => $pkg1->id,
            'service_type' => 'flight',
            'service_name' => 'Garuda Indonesia GA-582',
            'service_date' => now()->addDays(5)->toDateString(),
            'estimated_cost' => 3350000,
        ]);

        $pkg2 = VendorQuotationPackage::create([
            'quotation_number' => 'QUO-202611-0002',
            'travel_inquiry_id' => $this->inquiry->id,
            'vendor_id' => $this->vendor->id,
            'package_name' => 'Opsi 2 - Batik Air',
            'total_estimated_cost' => 2800000,
            'valid_until' => now()->addDay(),
            'status' => 'submitted',
        ]);

        // 2. HR/GA Site can select the option
        $response = $this->actingAs($this->siteUser)->post(route('travel.quotations.select', $pkg1), [
            'notes' => 'Pilih opsi 1 jadwal pagi sesuai instruksi Project Manager.',
        ]);
        $response->assertStatus(302);

        // Verify status updates
        $this->assertEquals('selected', $pkg1->fresh()->status);
        $this->assertEquals('rejected', $pkg2->fresh()->status);
        $this->assertEquals('booked', $this->travelRequest->fresh()->status);

        // Verify Travel Booking was automatically created
        $this->assertDatabaseHas('travel_bookings', [
            'travel_request_id' => $this->travelRequest->id,
            'vendor_id' => $this->vendor->id,
            'vendor_quotation_package_id' => $pkg1->id,
            'status' => 'confirmed',
            'total_actual_amount' => 3350000,
        ]);

        $booking = TravelBooking::where('travel_request_id', $this->travelRequest->id)->first();
        $this->assertNotNull($booking);
        $this->assertCount(1, $booking->services);
        $this->assertEquals('Garuda Indonesia GA-582', $booking->services->first()->service_name);
    }

    public function test_hrd_uploads_ticket_received_from_vendor_and_verifies_it(): void
    {
        $pkg = VendorQuotationPackage::create([
            'quotation_number' => 'QUO-202611-0010',
            'travel_inquiry_id' => $this->inquiry->id,
            'vendor_id' => $this->vendor->id,
            'package_name' => 'Opsi Garuda',
            'total_estimated_cost' => 2000000,
            'valid_until' => now()->addDay(),
            'status' => 'selected',
        ]);

        $booking = TravelBooking::create([
            'booking_number' => 'BKG-202611-0010',
            'travel_request_id' => $this->travelRequest->id,
            'vendor_id' => $this->vendor->id,
            'vendor_quotation_package_id' => $pkg->id,
            'status' => 'confirmed',
            'total_actual_amount' => 2000000,
            'confirmed_at' => now(),
            'confirmed_by' => $this->hrdUser->id,
        ]);

        $bookingService = TravelBookingService::create([
            'travel_booking_id' => $booking->id,
            'service_type' => 'flight',
            'service_name' => 'Garuda GA-582',
            'actual_cost' => 2000000,
            'status' => 'booked',
        ]);

        // 1. HRD uploads ticket PDF received from vendor via email
        $fakePdf = UploadedFile::fake()->create('eticket_garuda_ahmad.pdf', 350, 'application/pdf');

        $uploadResponse = $this->actingAs($this->hrdUser)->post(
            route('travel.bookings.tickets.store', $booking),
            [
                'ticket_number' => '126-9876543210 (PNR: JKTBPN)',
                'ticket_type' => 'flight_eticket',
                'travel_booking_service_id' => $bookingService->id,
                'ticket_file' => $fakePdf,
                'notes' => 'Diterima via email sales rekanan jam 14:00.',
            ]
        );
        $uploadResponse->assertStatus(302);

        $this->assertDatabaseHas('travel_tickets', [
            'travel_booking_id' => $booking->id,
            'ticket_number' => '126-9876543210 (PNR: JKTBPN)',
            'ticket_type' => 'flight_eticket',
            'file_name' => 'eticket_garuda_ahmad.pdf',
            'is_verified' => false,
        ]);

        $this->assertEquals('ticket_issued', $booking->fresh()->status);
        $this->assertEquals('ticket_issued', $this->travelRequest->fresh()->status);

        $ticket = TravelTicket::where('travel_booking_id', $booking->id)->first();
        Storage::disk('local')->assertExists($ticket->file_path);

        // 2. HRD verifies the ticket
        $verifyResponse = $this->actingAs($this->hrdUser)->post(route('travel.tickets.verify', $ticket), [
            'notes' => 'Nomor tiket dan nama penumpang Ahmad Dahlan sesuai identitas.',
        ]);
        $verifyResponse->assertStatus(302);

        $this->assertTrue($ticket->fresh()->is_verified);
        $this->assertEquals('verified', $booking->fresh()->status);

        // 3. Test secure download by authorized user
        $downloadResponse = $this->actingAs($this->hrdUser)->get(route('travel.tickets.download', $ticket));
        $downloadResponse->assertStatus(200);

        // 4. Test unauthorized employee blocked from downloading ticket (403)
        $unauthorizedDownload = $this->actingAs($this->unauthorizedUser)->get(route('travel.tickets.download', $ticket));
        $unauthorizedDownload->assertStatus(403);
    }
}

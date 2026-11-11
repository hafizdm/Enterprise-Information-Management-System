<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Travel\Vendor;
use App\Models\Travel\TravelRequest;
use App\Models\Travel\TravelInquiry;
use App\Models\Travel\VendorQuotationPackage;
use App\Models\Travel\TravelBooking;
use App\Models\Travel\TravelTicket;
use App\Policies\EmployeePolicy;
use App\Policies\UserPolicy;
use App\Policies\Travel\VendorPolicy;
use App\Policies\Travel\TravelRequestPolicy;
use App\Policies\Travel\TravelInquiryPolicy;
use App\Policies\Travel\VendorQuotationPolicy;
use App\Policies\Travel\TravelBookingPolicy;
use App\Policies\Travel\TravelTicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\MicrosoftGraph\Transport\MicrosoftGraphTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Vendor::class, VendorPolicy::class);
        Gate::policy(TravelRequest::class, TravelRequestPolicy::class);
        Gate::policy(TravelInquiry::class, TravelInquiryPolicy::class);
        Gate::policy(VendorQuotationPackage::class, VendorQuotationPolicy::class);
        Gate::policy(TravelBooking::class, TravelBookingPolicy::class);
        Gate::policy(TravelTicket::class, TravelTicketPolicy::class);

        \App\Models\Spd::observe(\App\Observers\SpdObserver::class);

        $this->app->make(MailManager::class)->extend('microsoft_graph', function () {
            return (new MicrosoftGraphTransportFactory(
                null,
                HttpClient::create(),
                null
            ))->create(
                new Dsn(
                    'microsoftgraph+api',
                    'default',
                    config('services.microsoft_graph.client_id'),
                    config('services.microsoft_graph.client_secret'),
                    null,
                    [
                        'tenantId' => config('services.microsoft_graph.tenant_id'),
                    ]
                )
            );
        });
    }
}
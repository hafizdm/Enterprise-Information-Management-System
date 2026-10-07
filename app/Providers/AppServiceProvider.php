<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\User;
use App\Policies\EmployeePolicy;
use App\Policies\UserPolicy;
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
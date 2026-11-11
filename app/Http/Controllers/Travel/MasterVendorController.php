<?php

namespace App\Http\Controllers\Travel;

use App\Http\Controllers\Controller;
use App\Models\Travel\Vendor;
use App\Services\Travel\VendorLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MasterVendorController extends Controller
{
    public function __construct(
        protected VendorLifecycleService $lifecycleService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Vendor::class);

        $search = $request->input('search');
        $status = $request->input('status');

        $vendors = Vendor::withCount(['users', 'activeHistories'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', (bool) $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $activeVendor = $this->lifecycleService->getActiveVendor();

        return view('travel.vendors.index', compact('vendors', 'search', 'status', 'activeVendor'));
    }

    public function create()
    {
        Gate::authorize('create', Vendor::class);

        return view('travel.vendors.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Vendor::class);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:vendors,code'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $vendor = Vendor::create($validated);

        return redirect()
            ->route('travel.vendors.show', $vendor)
            ->with('success', "Vendor {$vendor->name} berhasil didaftarkan.");
    }

    public function show(Vendor $vendor)
    {
        Gate::authorize('view', $vendor);

        $vendor->load([
            'activeHistories.changedByUser',
            'users' => fn ($q) => $q->latest(),
        ]);

        return view('travel.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        Gate::authorize('update', $vendor);

        return view('travel.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        Gate::authorize('update', $vendor);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vendors', 'code')->ignore($vendor->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $vendor->update($validated);

        return redirect()
            ->route('travel.vendors.show', $vendor)
            ->with('success', "Data vendor {$vendor->name} berhasil diperbarui.");
    }

    public function activate(Request $request, Vendor $vendor)
    {
        Gate::authorize('activate', $vendor);

        $validated = $request->validate([
            'effective_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->lifecycleService->activateVendor(
            $vendor->id,
            $validated['effective_date'],
            $validated['reason'],
            auth()->id()
        );

        return redirect()
            ->route('travel.vendors.show', $vendor)
            ->with('success', "Vendor {$vendor->name} berhasil diaktifkan sebagai rekanan utama.");
    }

    public function destroy(Vendor $vendor)
    {
        Gate::authorize('delete', $vendor);

        if ($vendor->is_active) {
            return back()->with('error', 'Vendor yang sedang aktif tidak dapat dihapus. Nonaktifkan terlebih dahulu.');
        }

        if ($vendor->users()->exists()) {
            return back()->with('error', 'Vendor memiliki akun pengguna portal terkait. Hapus akun pengguna terlebih dahulu.');
        }

        $vendor->delete();

        return redirect()
            ->route('travel.vendors.index')
            ->with('success', "Vendor {$vendor->name} berhasil dihapus.");
    }
}

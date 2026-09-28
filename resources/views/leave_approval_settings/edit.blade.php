@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">Leave Approval Settings</h1>
            <p class="text-muted mb-0">
                Configure the user responsible for HR leave approval.
            </p>
        </div>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">

        <div class="card-body">

            <form
                action="{{ route('leave-approval-settings.update') }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label for="hr_approver_id" class="form-label">
                        HR Leave Approver
                    </label>

                    <select
                        name="hr_approver_id"
                        id="hr_approver_id"
                        class="form-select @error('hr_approver_id') is-invalid @enderror"
                    >

                        <option value="">
                            -- Select HR Leave Approver --
                        </option>

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                {{ old('hr_approver_id', $setting?->hr_approver_id) == $user->id ? 'selected' : '' }}
                            >
                               {{ $user->employee?->full_name }} ({{ $user->username }})
                            </option>

                        @endforeach

                    </select>

                    @error('hr_approver_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <button type="submit" class="btn btn-primary">
                    Save
                </button>

            </form>

        </div>

    </div>

</div>

@endsection
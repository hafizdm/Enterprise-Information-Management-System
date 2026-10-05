@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text me-2"></i>
                Create SPD Report
            </h4>
            <p class="text-muted mb-0">
                Submit your actual business trip expenses and supporting evidence.
            </p>
        </div>

        <div>
            <a href="{{ route('spd-reports.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to My SPD Reports
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Please correct the following errors:
            </div>

            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- No Eligible SPD --}}
    @if ($spds->isEmpty())

        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">

                <div class="mb-3">
                    <i class="bi bi-info-circle display-4 text-muted"></i>
                </div>

                <h5 class="fw-bold">
                    No SPD Available
                </h5>

                <p class="text-muted mb-4">
                    You currently have no approved SPD available for reporting.
                    Only SPD requests that have completed the approval process
                    can be submitted as an SPD Report.
                </p>

                <a href="{{ route('spd-reports.index') }}"
                   class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to My SPD Reports
                </a>

            </div>
        </div>

    @else

        {{-- Main Form --}}
        <form action="{{ route('spd-reports.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            {{-- ====================================================== --}}
            {{-- SECTION 1 : SELECT SPD --}}
            {{-- ====================================================== --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-file-earmark-check me-2"></i>
                        Select SPD
                    </h6>

                    <small class="text-muted">
                        Select an approved SPD to create or resubmit its expense report.
                    </small>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-lg-8">

                            <label for="spd_id"
                                   class="form-label fw-semibold">
                                SPD Number
                                <span class="text-danger">*</span>
                            </label>

                            <select name="spd_id"
                                    id="spd_id"
                                    class="form-select @error('spd_id') is-invalid @enderror"
                                    required>

                                <option value="">
                                    -- Select SPD --
                                </option>

                                @foreach ($spds as $spd)

                                    @php
                                        $isRejected =
                                            $spd->report &&
                                            $spd->report->status_report === 'rejected';
                                    @endphp

                                    <option value="{{ $spd->id }}"
                                        {{ old('spd_id') == $spd->id ? 'selected' : '' }}>

                                        SPD #{{ $spd->id }}
                                        —
                                        {{ $spd->project?->name ?? 'No Project' }}
                                        —
                                        {{ $spd->destination }}

                                        @if ($isRejected)
                                            — REJECTED / RESUBMIT
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            @error('spd_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Approved SPDs without a report and rejected SPD Reports
                                are available for submission.
                            </div>

                        </div>

                    </div>

                </div>
            </div>


            {{-- ====================================================== --}}
            {{-- SECTION : REJECTION INFORMATION --}}
            {{-- ====================================================== --}}
            <div id="rejectionInformation"
                 class="alert alert-warning border-0 shadow-sm mb-4 d-none">

                <div class="d-flex align-items-start">

                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>

                    <div>

                        <div class="fw-bold mb-1">
                            SPD Report Rejected
                        </div>

                        <div class="small mb-2">
                            This SPD Report was rejected by the Manager.
                            Please correct the report and submit it again.
                        </div>

                        <div class="small fw-semibold">
                            Manager Rejection Reason:
                        </div>

                        <div id="managerRejectionReason"
                             class="mt-1">
                            -
                        </div>

                    </div>

                </div>

            </div>


            {{-- ====================================================== --}}
            {{-- SECTION 2 : ORIGINAL SPD INFORMATION --}}
            {{-- ====================================================== --}}
            <div id="spdInformation"
                 class="card border-0 shadow-sm mb-4 d-none">

                <div class="card-header bg-white border-bottom py-3">

                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-file-earmark-check me-2"></i>
                        Original SPD Information
                    </h6>

                    <small class="text-muted">
                        The following information comes directly from the
                        approved SPD and cannot be modified.
                    </small>

                </div>

                <div class="card-body">

                    {{-- General Information --}}
                    <div class="mb-4">

                        <div class="small text-uppercase text-muted fw-semibold mb-3">
                            General Information
                        </div>

                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    SPD Number
                                </label>

                                <div id="spdNumber" class="fw-bold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    SPD Status
                                </label>

                                <div>
                                    <span id="spdStatus"
                                          class="badge text-bg-success">
                                        Approved
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Employee
                                </label>

                                <div id="spdEmployee"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Project
                                </label>

                                <div id="spdProject"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Cost Center
                                </label>

                                <div id="spdCostCenter"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Manager
                                </label>

                                <div id="spdManager"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Approval Document / PIC
                                </label>

                                <div id="spdApprovalDocument"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>


                    <hr class="my-4">


                    {{-- Travel Information --}}
                    <div class="mb-4">

                        <div class="small text-uppercase text-muted fw-semibold mb-3">
                            Travel Information
                        </div>

                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Travel Type
                                </label>

                                <div id="spdTravelType"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Transportation
                                </label>

                                <div id="spdTransportation"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    From
                                </label>

                                <div id="spdFrom"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Destination
                                </label>

                                <div id="spdDestination"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    SPD Date Departure
                                </label>

                                <div id="spdDeparture"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    SPD Date Return
                                </label>

                                <div id="spdReturn"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    SPD Total Days
                                </label>

                                <div id="spdTotalDays"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Advance Payment
                                </label>

                                <div id="spdAdvancePayment"
                                     class="fw-semibold">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>


                    <hr class="my-4">


                    {{-- Financial Information --}}
                    <div class="mb-4">

                        <div class="small text-uppercase text-muted fw-semibold mb-3">
                            Approved Financial Information
                        </div>

                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Meals per Day
                                </label>

                                <div id="spdMealsPerDay"
                                     class="fw-semibold">
                                    Rp 0
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Allowance per Day
                                </label>

                                <div id="spdAllowancePerDay"
                                     class="fw-semibold">
                                    Rp 0
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Local Transport
                                </label>

                                <div id="spdLocalTransport"
                                     class="fw-semibold">
                                    Rp 0
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">
                                    Contingencies
                                </label>

                                <div id="spdContingencies"
                                     class="fw-semibold">
                                    Rp 0
                                </div>
                            </div>

                            <div class="col-12">

                                <div class="p-3 rounded bg-light border">

                                    <label class="form-label text-muted small mb-1">
                                        Balance Received
                                    </label>

                                    <div id="spdBalanceReceived"
                                         class="fs-5 fw-bold text-primary">
                                        Rp 0
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <hr class="my-4">


                    {{-- Purpose and Note --}}
                    <div>

                        <div class="small text-uppercase text-muted fw-semibold mb-3">
                            Business Trip Details
                        </div>

                        <div class="row g-4">

                            <div class="col-12">

                                <label class="form-label text-muted small mb-1">
                                    Purpose
                                </label>

                                <div id="spdPurpose"
                                     class="p-3 bg-light rounded border">
                                    -
                                </div>

                            </div>

                            <div class="col-12">

                                <label class="form-label text-muted small mb-1">
                                    SPD Note
                                </label>

                                <div id="spdNote"
                                     class="p-3 bg-light rounded border">
                                    -
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>


            {{-- ====================================================== --}}
            {{-- SECTION 3 : ACTUAL TRAVEL & EXPENSE --}}
            {{-- ====================================================== --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">

                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-calendar-check me-2"></i>
                        Actual Travel &amp; Expenses
                    </h6>

                    <small class="text-muted">
                        Enter the actual travel dates and actual expenses.
                        Meals and allowance rates are automatically determined
                        from the employee's Cost Level.
                    </small>

                </div>

                <div class="card-body">

                    <div class="row g-4">

                        {{-- Actual Departure --}}
                        <div class="col-md-6">

                            <label for="date_departure"
                                   class="form-label fw-semibold">
                                Actual Date Departure
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="date_departure"
                                   id="date_departure"
                                   value="{{ old('date_departure') }}"
                                   class="form-control @error('date_departure') is-invalid @enderror"
                                   required>

                            @error('date_departure')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Actual Return --}}
                        <div class="col-md-6">

                            <label for="date_return"
                                   class="form-label fw-semibold">
                                Actual Date Return
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="date_return"
                                   id="date_return"
                                   value="{{ old('date_return') }}"
                                   class="form-control @error('date_return') is-invalid @enderror"
                                   required>

                            @error('date_return')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Actual Days --}}
                        <div class="col-12">

                            <div class="alert alert-light border mb-0">

                                <div class="d-flex align-items-center">

                                    <i class="bi bi-clock-history fs-5 me-2"></i>

                                    <div>

                                        <div class="small text-muted">
                                            Actual Travel Duration
                                        </div>

                                        <div id="actualDays"
                                             class="fw-bold">
                                            0 day
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Actual Meals --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Actual Meals per Day
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input type="text"
                                       id="meals_per_day"
                                       class="form-control bg-light fw-semibold"
                                       value="0"
                                       readonly>

                            </div>

                            <div class="form-text">
                                Automatically determined from the employee's
                                Cost Level and SPD Travel Type.
                            </div>

                        </div>


                        {{-- Actual Allowance --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                Actual Allowance per Day
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input type="text"
                                       id="allowance_per_day"
                                       class="form-control bg-light fw-semibold"
                                       value="0"
                                       readonly>

                            </div>

                            <div class="form-text">
                                Automatically determined from the employee's
                                Cost Level and SPD Travel Type.
                            </div>

                        </div>


                        {{-- Actual Meals Total --}}
                        <div class="col-md-6">

                            <label class="form-label text-muted small mb-1">
                                Actual Meals Total
                            </label>

                            <div class="p-3 rounded bg-light border">

                                <div id="actualMealsTotal"
                                     class="fw-bold">
                                    Rp 0
                                </div>

                                <div class="small text-muted mt-1">
                                    Meals per Day × Actual Days
                                </div>

                            </div>

                        </div>


                        {{-- Actual Allowance Total --}}
                        <div class="col-md-6">

                            <label class="form-label text-muted small mb-1">
                                Actual Allowance Total
                            </label>

                            <div class="p-3 rounded bg-light border">

                                <div id="actualAllowanceTotal"
                                     class="fw-bold">
                                    Rp 0
                                </div>

                                <div class="small text-muted mt-1">
                                    Allowance per Day × Actual Days
                                </div>

                            </div>

                        </div>


                        {{-- Actual Local Transport --}}
                        <div class="col-md-6">

                            <label for="local_transport"
                                   class="form-label fw-semibold">
                                Actual Local Transport
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input type="number"
                                       name="local_transport"
                                       id="local_transport"
                                       value="{{ old('local_transport', 0) }}"
                                       min="0"
                                       step="0.01"
                                       class="form-control @error('local_transport') is-invalid @enderror">

                            </div>

                            @error('local_transport')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Actual Contingencies --}}
                        <div class="col-md-6">

                            <label for="contingencies"
                                   class="form-label fw-semibold">
                                Actual Contingencies
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input type="number"
                                       name="contingencies"
                                       id="contingencies"
                                       value="{{ old('contingencies', 0) }}"
                                       min="0"
                                       step="0.01"
                                       class="form-control @error('contingencies') is-invalid @enderror">

                            </div>

                            @error('contingencies')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>
            </div>


            {{-- ====================================================== --}}
            {{-- SECTION 4 : EXPENSE EVIDENCE --}}
            {{-- ====================================================== --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">

                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-paperclip me-2"></i>
                        Expense Evidence
                    </h6>

                    <small class="text-muted">
                        Upload one combined file containing all receipts,
                        bills, tickets, and supporting documents.
                    </small>

                </div>

                <div class="card-body">

                    <label for="expense_evidence"
                           class="form-label fw-semibold">
                        Evidence File
                        <span class="text-danger">*</span>
                    </label>

                    <input type="file"
                           name="expense_evidence"
                           id="expense_evidence"
                           class="form-control @error('expense_evidence') is-invalid @enderror"
                           accept=".pdf,.jpg,.jpeg,.png"
                           required>

                    @error('expense_evidence')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        Allowed formats: PDF, JPG, JPEG, PNG.
                        Maximum size: 10 MB.
                    </div>

                </div>
            </div>


            {{-- ====================================================== --}}
            {{-- SECTION 5 : REPORT NOTE --}}
            {{-- ====================================================== --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">

                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-chat-left-text me-2"></i>
                        Report Note
                    </h6>

                </div>

                <div class="card-body">

                    <textarea name="note"
                              id="note"
                              rows="4"
                              class="form-control @error('note') is-invalid @enderror"
                              placeholder="Add any additional information about this SPD report...">{{ old('note') }}</textarea>

                    @error('note')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
            </div>


            {{-- ====================================================== --}}
            {{-- SECTION 6 : CALCULATION SUMMARY --}}
            {{-- ====================================================== --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">

                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-calculator me-2"></i>
                        Expense Calculation
                    </h6>

                </div>

                <div class="card-body">

                    <div class="row g-3">

                        {{-- Balance Received --}}
                        <div class="col-md-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Balance Received
                                </div>

                                <div id="summaryBalanceReceived"
                                     class="fs-5 fw-bold">
                                    Rp 0
                                </div>

                            </div>

                        </div>


                        {{-- Expense Balance --}}
                        <div class="col-md-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Actual Expense Balance
                                </div>

                                <div id="summaryExpenseBalance"
                                     class="fs-5 fw-bold">
                                    Rp 0
                                </div>

                            </div>

                        </div>


                        {{-- Expense Report Total --}}
                        <div class="col-md-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Expense Report Total
                                </div>

                                <div id="summaryExpenseReportTotal"
                                     class="fs-5 fw-bold">
                                    Rp 0
                                </div>

                            </div>

                        </div>


                        {{-- Settlement --}}
                        <div class="col-12">

                            <div class="alert alert-light border mb-0">

                                <div class="d-flex align-items-center">

                                    <i class="bi bi-arrow-left-right fs-5 me-2"></i>

                                    <div>

                                        <div class="small text-muted">
                                            Settlement Status
                                        </div>

                                        <div id="settlementLabel"
                                             class="fw-bold">
                                            Select an SPD and enter actual expenses.
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="mt-3 small text-muted">

                        <strong>Calculation:</strong>

                        Actual Expense Balance =
                        (Cost Level Meals per Day × Actual Days)
                        +
                        (Cost Level Allowance per Day × Actual Days)
                        +
                        Actual Local Transport
                        +
                        Actual Contingencies

                        <br>

                        Expense Report Total =
                        Balance Received − Actual Expense Balance

                    </div>

                </div>
            </div>


            {{-- ====================================================== --}}
            {{-- SECTION 7 : SUBMIT --}}
            {{-- ====================================================== --}}
            <div class="card border-0 shadow-sm mb-5">

                <div class="card-body">

                    <div class="d-flex flex-column flex-md-row
                                justify-content-between
                                align-items-md-center
                                gap-3">

                        <div>

                            <div id="submitTitle"
                                 class="fw-semibold">
                                Ready to submit?
                            </div>

                            <div id="submitDescription"
                                 class="small text-muted">
                                Make sure the actual expenses and evidence
                                are correct before submitting.
                            </div>

                        </div>


                        <div class="d-flex gap-2">

                            <a href="{{ route('spd-reports.index') }}"
                               class="btn btn-outline-secondary">

                                <i class="bi bi-x-lg me-1"></i>
                                Cancel

                            </a>


                            <button type="submit"
                                    id="submitButton"
                                    class="btn btn-primary">

                                <i class="bi bi-send me-1"></i>
                                <span id="submitButtonText">
                                    Submit SPD Report
                                </span>

                            </button>

                        </div>

                    </div>

                </div>
            </div>

        </form>

    @endif

</div>


{{-- ================================================================ --}}
{{-- SPD DATA FOR JAVASCRIPT --}}
{{-- ================================================================ --}}

@php

$spdData = $spds->mapWithKeys(function ($spd) {

    $report = $spd->report;

    return [
        $spd->id => [

            'id' => $spd->id,

            'employee' =>
                $spd->employee?->full_name ?? '-',

            'project' =>
                $spd->project?->name ?? '-',

            'cost_center' =>
                $spd->project?->cost_center ?? '-',

            'manager' =>
                $spd->manager?->full_name ?? '-',

            'approval_document' =>
                $spd->approvalDocument?->full_name ?? '-',

            'travel_type' =>
                $spd->travel_type
                    ? ucfirst($spd->travel_type)
                    : '-',

            'travel_type_raw' =>
                $spd->travel_type ?? 'domestic',

            'from' =>
                $spd->from ?? '-',

            'destination' =>
                $spd->destination ?? '-',

            'date_departure' =>
                $spd->date_departure?->format('d M Y') ?? '-',

            'date_return' =>
                $spd->date_return?->format('d M Y') ?? '-',

            'total_days' =>
                $spd->total_days ?? 0,

            'meals_per_day' =>
                (float) $spd->meals_per_day,

            'allowance_per_day' =>
                (float) $spd->allowance_per_day,

            'local_transport' =>
                (float) $spd->local_transport,

            'contingencies' =>
                (float) $spd->contingencies,

            'balance_received' =>
                (float) $spd->balance_received,

            'transportation' =>
                $spd->transportation
                    ? ucfirst($spd->transportation)
                    : '-',

            'advance_payment' =>
                $spd->advance_payment
                    ? 'Yes'
                    : 'No',

            'purpose' =>
                $spd->purpose ?? '-',

            'note' =>
                $spd->note ?? '-',

            'status' =>
                $spd->status ?? '-',

            /*
            |--------------------------------------------------------------------------
            | Existing SPD Report
            |--------------------------------------------------------------------------
            */

            'report_id' =>
                $report?->id,

            'report_status' =>
                $report?->status_report,

            'report_date_departure' =>
                $report?->date_departure?->format('Y-m-d'),

            'report_date_return' =>
                $report?->date_return?->format('Y-m-d'),

            'report_local_transport' =>
                $report
                    ? (float) $report->local_transport
                    : 0,

            'report_contingencies' =>
                $report
                    ? (float) $report->contingencies
                    : 0,

            'report_note' =>
                $report?->note ?? '',

            'manager_rejection_reason' =>
                $report?->manager_rejection_reason ?? '',
        ],
    ];
});

@endphp


<script>

    /*
    |--------------------------------------------------------------------------
    | SPD DATA
    |--------------------------------------------------------------------------
    */

    const spdData = @json($spdData);


    /*
    |--------------------------------------------------------------------------
    | SPD INFORMATION ELEMENTS
    |--------------------------------------------------------------------------
    */

    const spdSelect =
        document.getElementById('spd_id');

    const spdInformation =
        document.getElementById('spdInformation');

    const rejectionInformation =
        document.getElementById('rejectionInformation');

    const managerRejectionReason =
        document.getElementById('managerRejectionReason');

    const spdNumber =
        document.getElementById('spdNumber');

    const spdStatus =
        document.getElementById('spdStatus');

    const spdEmployee =
        document.getElementById('spdEmployee');

    const spdProject =
        document.getElementById('spdProject');

    const spdCostCenter =
        document.getElementById('spdCostCenter');

    const spdManager =
        document.getElementById('spdManager');

    const spdApprovalDocument =
        document.getElementById('spdApprovalDocument');

    const spdTravelType =
        document.getElementById('spdTravelType');

    const spdFrom =
        document.getElementById('spdFrom');

    const spdDestination =
        document.getElementById('spdDestination');

    const spdDeparture =
        document.getElementById('spdDeparture');

    const spdReturn =
        document.getElementById('spdReturn');

    const spdTotalDays =
        document.getElementById('spdTotalDays');

    const spdMealsPerDay =
        document.getElementById('spdMealsPerDay');

    const spdAllowancePerDay =
        document.getElementById('spdAllowancePerDay');

    const spdLocalTransport =
        document.getElementById('spdLocalTransport');

    const spdContingencies =
        document.getElementById('spdContingencies');

    const spdTransportation =
        document.getElementById('spdTransportation');

    const spdAdvancePayment =
        document.getElementById('spdAdvancePayment');

    const spdPurpose =
        document.getElementById('spdPurpose');

    const spdNote =
        document.getElementById('spdNote');

    const spdBalanceReceived =
        document.getElementById('spdBalanceReceived');


    /*
    |--------------------------------------------------------------------------
    | ACTUAL CALCULATION ELEMENTS
    |--------------------------------------------------------------------------
    */

    const summaryBalanceReceived =
        document.getElementById('summaryBalanceReceived');

    const summaryExpenseBalance =
        document.getElementById('summaryExpenseBalance');

    const summaryExpenseReportTotal =
        document.getElementById('summaryExpenseReportTotal');

    const settlementLabel =
        document.getElementById('settlementLabel');

    const dateDeparture =
        document.getElementById('date_departure');

    const dateReturn =
        document.getElementById('date_return');

    const actualDays =
        document.getElementById('actualDays');

    const mealsPerDay =
        document.getElementById('meals_per_day');

    const allowancePerDay =
        document.getElementById('allowance_per_day');

    const actualMealsTotal =
        document.getElementById('actualMealsTotal');

    const actualAllowanceTotal =
        document.getElementById('actualAllowanceTotal');

    const localTransport =
        document.getElementById('local_transport');

    const contingencies =
        document.getElementById('contingencies');


    /*
    |--------------------------------------------------------------------------
    | SUBMIT AREA
    |--------------------------------------------------------------------------
    */

    const submitTitle =
        document.getElementById('submitTitle');

    const submitDescription =
        document.getElementById('submitDescription');

    const submitButton =
        document.getElementById('submitButton');

    const submitButtonText =
        document.getElementById('submitButtonText');


    /*
    |--------------------------------------------------------------------------
    | FORMAT CURRENCY
    |--------------------------------------------------------------------------
    */

    function formatCurrency(value)
    {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value || 0);
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE ACTUAL DAYS
    |--------------------------------------------------------------------------
    */

    function calculateActualDays()
    {
        if (
            !dateDeparture ||
            !dateReturn ||
            !actualDays
        ) {
            return 0;
        }

        if (
            !dateDeparture.value ||
            !dateReturn.value
        ) {
            actualDays.textContent = '0 day';
            return 0;
        }

        const departure =
            new Date(
                dateDeparture.value + 'T00:00:00'
            );

        const returnDate =
            new Date(
                dateReturn.value + 'T00:00:00'
            );

        const difference =
            Math.round(
                (returnDate - departure)
                /
                (1000 * 60 * 60 * 24)
            ) + 1;

        if (difference <= 0) {

            actualDays.textContent =
                'Invalid date range';

            return 0;
        }

        actualDays.textContent =
            difference +
            (
                difference === 1
                    ? ' day'
                    : ' days'
            );

        return difference;
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE EXPENSE
    |--------------------------------------------------------------------------
    */

    function calculateExpense()
    {
        if (!spdSelect) {
            return;
        }

        const days =
            calculateActualDays();

        const meals =
            parseFloat(
                mealsPerDay?.value
            ) || 0;

        const allowance =
            parseFloat(
                allowancePerDay?.value
            ) || 0;

        const transport =
            parseFloat(
                localTransport?.value
            ) || 0;

        const contingency =
            parseFloat(
                contingencies?.value
            ) || 0;

        const balanceReceived =
            spdSelect.value &&
            spdData[spdSelect.value]
                ? parseFloat(
                    spdData[
                        spdSelect.value
                    ].balance_received
                ) || 0
                : 0;


        /*
        |--------------------------------------------------------------------------
        | Actual Meals / Allowance Total
        |--------------------------------------------------------------------------
        */

        const mealsTotal =
            meals * days;

        const allowanceTotal =
            allowance * days;


        /*
        |--------------------------------------------------------------------------
        | Expense Balance
        |--------------------------------------------------------------------------
        */

        const expenseBalance =
            mealsTotal +
            allowanceTotal +
            transport +
            contingency;


        /*
        |--------------------------------------------------------------------------
        | Expense Report Total
        |--------------------------------------------------------------------------
        */

        const expenseReportTotal =
            balanceReceived -
            expenseBalance;


        /*
        |--------------------------------------------------------------------------
        | Update Actual Totals
        |--------------------------------------------------------------------------
        */

        if (actualMealsTotal) {

            actualMealsTotal.textContent =
                formatCurrency(
                    mealsTotal
                );
        }

        if (actualAllowanceTotal) {

            actualAllowanceTotal.textContent =
                formatCurrency(
                    allowanceTotal
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Summary
        |--------------------------------------------------------------------------
        */

        if (summaryBalanceReceived) {

            summaryBalanceReceived.textContent =
                formatCurrency(
                    balanceReceived
                );
        }

        if (summaryExpenseBalance) {

            summaryExpenseBalance.textContent =
                formatCurrency(
                    expenseBalance
                );
        }

        if (summaryExpenseReportTotal) {

            summaryExpenseReportTotal.textContent =
                formatCurrency(
                    expenseReportTotal
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Settlement Status
        |--------------------------------------------------------------------------
        */

        if (settlementLabel) {

            if (!spdSelect.value) {

                settlementLabel.textContent =
                    'Select an SPD and enter actual expenses.';

            } else if (expenseReportTotal < 0) {

                settlementLabel.textContent =
                    'Reimburse — Company owes employee';

            } else if (expenseReportTotal === 0) {

                settlementLabel.textContent =
                    'Cash Clear — No balance remaining';

            } else {

                settlementLabel.textContent =
                    'Refund Employee — Employee returns balance';
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD SELECTED SPD
    |--------------------------------------------------------------------------
    */

  function loadSelectedSpd()
    {
        if (!spdSelect) {
            return;
        }

        const selectedId =
            spdSelect.value;


        /*
        |--------------------------------------------------------------------------
        | No SPD Selected
        |--------------------------------------------------------------------------
        */

        if (
            !selectedId ||
            !spdData[selectedId]
        ) {

            spdInformation?.classList.add(
                'd-none'
            );

            rejectionInformation?.classList.add(
                'd-none'
            );

            if (spdNumber)
                spdNumber.textContent = '-';

            if (spdStatus) {
                spdStatus.textContent = 'Approved';
                spdStatus.className = 'badge text-bg-success';
            }

            if (spdEmployee)
                spdEmployee.textContent = '-';

            if (spdProject)
                spdProject.textContent = '-';

            if (spdCostCenter)
                spdCostCenter.textContent = '-';

            if (spdManager)
                spdManager.textContent = '-';

            if (spdApprovalDocument)
                spdApprovalDocument.textContent = '-';

            if (spdTravelType)
                spdTravelType.textContent = '-';

            if (spdFrom)
                spdFrom.textContent = '-';

            if (spdDestination)
                spdDestination.textContent = '-';

            if (spdDeparture)
                spdDeparture.textContent = '-';

            if (spdReturn)
                spdReturn.textContent = '-';

            if (spdTotalDays)
                spdTotalDays.textContent = '-';

            if (spdMealsPerDay)
                spdMealsPerDay.textContent = 'Rp 0';

            if (spdAllowancePerDay)
                spdAllowancePerDay.textContent = 'Rp 0';

            if (spdLocalTransport)
                spdLocalTransport.textContent = 'Rp 0';

            if (spdContingencies)
                spdContingencies.textContent = 'Rp 0';

            if (spdTransportation)
                spdTransportation.textContent = '-';

            if (spdAdvancePayment)
                spdAdvancePayment.textContent = '-';

            if (spdPurpose)
                spdPurpose.textContent = '-';

            if (spdNote)
                spdNote.textContent = '-';

            if (spdBalanceReceived)
                spdBalanceReceived.textContent = 'Rp 0';

            if (managerRejectionReason)
                managerRejectionReason.textContent = '-';

            if (mealsPerDay)
                mealsPerDay.value = '0';

            if (allowancePerDay)
                allowancePerDay.value = '0';

            calculateExpense();

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Selected SPD
        |--------------------------------------------------------------------------
        */

        const spd =
            spdData[selectedId];


        /*
        |--------------------------------------------------------------------------
        | Show SPD Information
        |--------------------------------------------------------------------------
        */

        spdInformation?.classList.remove(
            'd-none'
        );


        /*
        |--------------------------------------------------------------------------
        | SPD STATUS
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | This is the status of the ORIGINAL SPD.
        |
        | A rejected SPD REPORT does NOT mean
        | that the SPD itself is rejected.
        |
        */

        if (spdStatus) {

            spdStatus.textContent =
                'Approved';

            spdStatus.className =
                'badge text-bg-success';
        }


        /*
        |--------------------------------------------------------------------------
        | Detect Rejected SPD Report
        |--------------------------------------------------------------------------
        */

        const isRejected =
            spd.report_status === 'rejected';


        /*
        |--------------------------------------------------------------------------
        | Rejected SPD Report
        |--------------------------------------------------------------------------
        */

        if (isRejected) {

            /*
            |--------------------------------------------------------------------------
            | Show rejection information
            |--------------------------------------------------------------------------
            */

            rejectionInformation?.classList.remove(
                'd-none'
            );


            /*
            |--------------------------------------------------------------------------
            | Manager rejection reason
            |--------------------------------------------------------------------------
            */

            if (managerRejectionReason) {

                managerRejectionReason.textContent =
                    spd.manager_rejection_reason ||
                    'No rejection reason provided.';
            }


            /*
            |--------------------------------------------------------------------------
            | Submit area
            |--------------------------------------------------------------------------
            */

            if (submitTitle) {

                submitTitle.textContent =
                    'Ready to resubmit?';
            }

            if (submitDescription) {

                submitDescription.textContent =
                    'Correct the rejected SPD Report and submit it again for Manager approval.';
            }

            if (submitButton) {

                submitButton.classList.remove(
                    'btn-primary'
                );

                submitButton.classList.add(
                    'btn-warning'
                );
            }

            if (submitButtonText) {

                submitButtonText.textContent =
                    'Resubmit SPD Report';
            }


            /*
            |--------------------------------------------------------------------------
            | Prefill rejected report data
            |--------------------------------------------------------------------------
            */

            if (dateDeparture) {

                dateDeparture.value =
                    spd.report_date_departure || '';
            }

            if (dateReturn) {

                dateReturn.value =
                    spd.report_date_return || '';
            }

            if (localTransport) {

                localTransport.value =
                    spd.report_local_transport ?? 0;
            }

            if (contingencies) {

                contingencies.value =
                    spd.report_contingencies ?? 0;
            }

            if (document.getElementById('note')) {

                document.getElementById('note').value =
                    spd.report_note || '';
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | Normal New SPD Report
            |--------------------------------------------------------------------------
            */

            rejectionInformation?.classList.add(
                'd-none'
            );


            if (submitTitle) {

                submitTitle.textContent =
                    'Ready to submit?';
            }

            if (submitDescription) {

                submitDescription.textContent =
                    'Make sure the actual expenses and evidence are correct before submitting.';
            }


            if (submitButton) {

                submitButton.classList.remove(
                    'btn-warning'
                );

                submitButton.classList.add(
                    'btn-primary'
                );
            }


            if (submitButtonText) {

                submitButtonText.textContent =
                    'Submit SPD Report';
            }


            /*
            |--------------------------------------------------------------------------
            | Clear form for a new report
            |--------------------------------------------------------------------------
            */

            if (dateDeparture) {

                dateDeparture.value =
                    @json(old('date_departure', ''));
            }

            if (dateReturn) {

                dateReturn.value =
                    @json(old('date_return', ''));
            }

            if (localTransport) {

                localTransport.value =
                    @json(old('local_transport', 0));
            }

            if (contingencies) {

                contingencies.value =
                    @json(old('contingencies', 0));
            }

            if (document.getElementById('note')) {

                document.getElementById('note').value =
                    @json(old('note', ''));
            }
        }


        /*
        |--------------------------------------------------------------------------
        | General Information
        |--------------------------------------------------------------------------
        */

        if (spdNumber) {

            spdNumber.textContent =
                'SPD #' + spd.id;
        }


        /*
        |--------------------------------------------------------------------------
        | Employee / Project
        |--------------------------------------------------------------------------
        */

        if (spdEmployee) {

            spdEmployee.textContent =
                spd.employee;
        }

        if (spdProject) {

            spdProject.textContent =
                spd.project;
        }

        if (spdCostCenter) {

            spdCostCenter.textContent =
                spd.cost_center;
        }

        if (spdManager) {

            spdManager.textContent =
                spd.manager;
        }

        if (spdApprovalDocument) {

            spdApprovalDocument.textContent =
                spd.approval_document;
        }


        /*
        |--------------------------------------------------------------------------
        | Travel Information
        |--------------------------------------------------------------------------
        */

        if (spdTravelType) {

            spdTravelType.textContent =
                spd.travel_type;
        }

        if (spdFrom) {

            spdFrom.textContent =
                spd.from;
        }

        if (spdDestination) {

            spdDestination.textContent =
                spd.destination;
        }

        if (spdDeparture) {

            spdDeparture.textContent =
                spd.date_departure;
        }

        if (spdReturn) {

            spdReturn.textContent =
                spd.date_return;
        }

        if (spdTotalDays) {

            spdTotalDays.textContent =
                spd.total_days +
                (
                    spd.total_days == 1
                        ? ' day'
                        : ' days'
                );
        }

        if (spdTransportation) {

            spdTransportation.textContent =
                spd.transportation;
        }

        if (spdAdvancePayment) {

            spdAdvancePayment.textContent =
                spd.advance_payment;
        }


        /*
        |--------------------------------------------------------------------------
        | Approved Financial Information
        |--------------------------------------------------------------------------
        */

        if (spdMealsPerDay) {

            spdMealsPerDay.textContent =
                formatCurrency(
                    spd.meals_per_day
                );
        }

        if (spdAllowancePerDay) {

            spdAllowancePerDay.textContent =
                formatCurrency(
                    spd.allowance_per_day
                );
        }

        if (spdLocalTransport) {

            spdLocalTransport.textContent =
                formatCurrency(
                    spd.local_transport
                );
        }

        if (spdContingencies) {

            spdContingencies.textContent =
                formatCurrency(
                    spd.contingencies
                );
        }

        if (spdBalanceReceived) {

            spdBalanceReceived.textContent =
                formatCurrency(
                    spd.balance_received
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Purpose / Note
        |--------------------------------------------------------------------------
        */

        if (spdPurpose) {

            spdPurpose.textContent =
                spd.purpose;
        }

        if (spdNote) {

            spdNote.textContent =
                spd.note;
        }


        /*
        |--------------------------------------------------------------------------
        | Actual Meals / Allowance Rate
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Use raw numeric values here.
        |
        */

        if (mealsPerDay) {

            mealsPerDay.value =
                Number(
                    spd.meals_per_day || 0
                );
        }

        if (allowancePerDay) {

            allowancePerDay.value =
                Number(
                    spd.allowance_per_day || 0
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Calculation
        |--------------------------------------------------------------------------
        */

        calculateExpense();
    }


    /*
    |--------------------------------------------------------------------------
    | EVENT LISTENERS
    |--------------------------------------------------------------------------
    */

    spdSelect?.addEventListener(
        'change',
        loadSelectedSpd
    );


    dateDeparture?.addEventListener(
        'change',
        calculateExpense
    );


    dateReturn?.addEventListener(
        'change',
        calculateExpense
    );


    localTransport?.addEventListener(
        'input',
        calculateExpense
    );


    contingencies?.addEventListener(
        'input',
        calculateExpense
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */

    loadSelectedSpd();

</script>

@endsection


@extends('layouts.bootstrap')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="h3 mb-1 fw-bold">Admin Settings</h1>
        <p class="text-muted mb-0 small">Manage master data for services, MNOs, aggregators, and expense references</p>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 w-md-auto">
        <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to Dashboard</span><span class="d-sm-none">Back</span>
    </a>
</div>

@if (session('ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('ok') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <div class="fw-bold mb-1">Please fix the validation errors and try again.</div>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $partials = [
        'services' => 'admin.settings.partials.services',
        'service-types' => 'admin.settings.partials.service-types',
        'mnos' => 'admin.settings.partials.mnos',
        'aggregators' => 'admin.settings.partials.aggregators',
        'banks' => 'admin.settings.partials.banks',
        'mandatory-types' => 'admin.settings.partials.mandatory-types',
        'operational-categories' => 'admin.settings.partials.operational-categories',
        'recipients' => 'admin.settings.partials.recipients',
    ];
@endphp

@if(!$activeSection)
    @include('admin.settings.partials.landing')
@else
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.settings.index') }}" class="btn btn-link p-0">
            <i class="bi bi-arrow-left"></i> Back to Settings Overview
        </a>
        <h4 class="mb-0">{{ $sectionTitle ?? ucfirst(str_replace('-', ' ', $activeSection)) }}</h4>
    </div>
    @include($partials[$activeSection])
@endif
@endsection


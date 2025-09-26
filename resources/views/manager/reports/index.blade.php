@extends('layouts.managerdashboardlayout')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="container">
    <h4 class="mb-4">📊 Select a Report</h4>

    <div class="list-group">
        @foreach($reports as $key => $label)
            @if(str_contains(strtolower($label), 'coming soon'))
                <a href="#" class="list-group-item list-group-item-action disabled">
                    {{ $label }}
                </a>
            @else
                <a href="{{ route('manager.reports.show', $key) }}" class="list-group-item list-group-item-action">
                    {{ $label }}
                </a>
            @endif
        @endforeach
    </div>
</div>
@endsection

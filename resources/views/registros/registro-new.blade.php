@extends('components.layout')

@section('styles')
    <!-- Estilos adicionales específicos para esta página pueden ir aquí -->
@endsection

@section('content')
    <x-form-header />

    <div class="form-body">
        <x-alerts />

        <form method="POST" action="{{ route('registros.store') }}">
            @csrf
            <input type="hidden" name="from_public_form" value="1">
            
            <x-voluntario-form :voluntarios="$voluntarios" />
            <x-date-time-fields />
            <x-activity-fields />
            <x-submit-button />
        </form>
    </div>

    <x-form-footer />
@endsection

@push('scripts')
    <x-form-scripts />
@endpush

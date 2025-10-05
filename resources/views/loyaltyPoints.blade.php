@extends('include.app')

@section('content')
    {{-- Loyalty Points Form --}}
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('Loyalty Points') }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('loyaltyPoints.updateLoyaltyPoints') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pointsPerAppointment">{{ __('Points Per Appointment') }}</label>
                            <input type="number" step="1" min="0" name="pointsPerAppointment"
                                id="pointsPerAppointment"
                                class="form-control @error('pointsPerAppointment') is-invalid @enderror"
                                value="{{ old('pointsPerAppointment', $pointsPerAppointment ?? '') }}" required>
                            @error('pointsPerAppointment')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small
                                class="form-text text-muted">{{ __('Set the number of points to award for every appointment') }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="valueOfEachPoint">{{ __('value of each point') }}</label>
                            <input type="number" step="0.01" min="0" name="valueOfEachPoint"
                                id="valueOfEachPoint" class="form-control @error('valueOfEachPoint') is-invalid @enderror"
                                value="{{ old('valueOfEachPoint', $valueOfEachPoint ?? '') }}" required>
                            @error('valueOfEachPoint')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small
                                class="form-text text-muted">{{ __('Set the value of each loyalty point') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group mb-5">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

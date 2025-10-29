@extends('layouts.admin')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">📅 Book New Appointment</h4>
        </div>

        <div class="card-body">
          {{-- Validation Errors --}}
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- Appointment Form --}}
          <form action="{{ route('slot.store') }}" method="POST">
            @csrf

            <!-- 👤 Patient Information -->
            <h5 class="mb-3 text-primary">Patient Information</h5>

            <div class="mb-3">
              <label for="patient_name" class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control @error('patient_name') is-invalid @enderror"
                     id="patient_name"
                     name="patient_name"
                     value="{{ old('patient_name') }}"
                     required>
              @error('patient_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="patient_email" class="form-label">Email Address <span class="text-danger">*</span></label>
              <input type="email"
                     class="form-control @error('patient_email') is-invalid @enderror"
                     id="patient_email"
                     name="patient_email"
                     value="{{ old('patient_email') }}"
                     required>
              @error('patient_email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="patient_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
              <input type="tel"
                     class="form-control @error('patient_phone') is-invalid @enderror"
                     id="patient_phone"
                     name="patient_phone"
                     value="{{ old('patient_phone') }}"
                     required>
              @error('patient_phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <hr class="my-4">

            <!-- 📆 Appointment Details -->
            <h5 class="mb-3 text-primary">Appointment Details</h5>

            <div class="mb-3">
              <label for="pickup_point" class="form-label">Pickup Point <span class="text-danger">*</span></label>
              <select class="form-select @error('pickup_point') is-invalid @enderror"
                      id="pickup_point" name="pickup_point" required>
                <option value="">Select pickup location...</option>
                @foreach ($pickupPoints as $point)
                  <option value="{{ $point }}" {{ old('pickup_point') == $point ? 'selected' : '' }}>
                    {{ $point }}
                  </option>
                @endforeach
              </select>
              @error('pickup_point')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                <input type="date"
                       class="form-control @error('appointment_date') is-invalid @enderror"
                       id="appointment_date"
                       name="appointment_date"
                       value="{{ old('appointment_date') }}"
                       min="{{ date('Y-m-d') }}"
                       required>
                @error('appointment_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="appointment_time" class="form-label">Appointment Time <span class="text-danger">*</span></label>
                <input type="time"
                       class="form-control @error('appointment_time') is-invalid @enderror"
                       id="appointment_time"
                       name="appointment_time"
                       value="{{ old('appointment_time') }}"
                       required>
                @error('appointment_time')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="mb-3">
              <!-- <label for="appointment" class="form-label">Appointment With <span class="text-danger">*</span></label> -->
              <select class="form-select @error('appointment') is-invalid @enderror"
                      id="appointment" name="appointment" required>
                <option value="">Select doctor / staff...</option>
                @foreach ($appointments as $appt)
                  <option value="{{ $appt }}" {{ old('appointment') == $appt ? 'selected' : '' }}>
                    {{ $appt }}
                  </option>
                @endforeach
              </select>
              @error('appointment')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Additional Notes</label>
              <textarea class="form-control @error('notes') is-invalid @enderror"
                        id="notes" name="notes" rows="3"
                        placeholder="Any special requirements or notes...">{{ old('notes') }}</textarea>
              @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-flex justify-content-between mt-4">
              <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
                ← Back to Appointments
              </a>
              <button type="submit" class="btn btn-primary">
                📅 Book Appointment
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Ensure date cannot be in the past
  document.getElementById('appointment_date').min = new Date().toISOString().split('T')[0];
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Add New Slot')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">📅 Add New Appointment Slot</h4>
        </div>

        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('slot.store') }}" method="POST">
            @csrf

            {{-- Date & Time Section --}}
            <h5 class="mb-3 text-primary">📆 Date & Time</h5>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="slot_date" class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                <input type="date" 
                       class="form-control @error('slot_date') is-invalid @enderror" 
                       id="slot_date" 
                       name="slot_date" 
                       value="{{ old('slot_date') }}"
                       min="{{ date('Y-m-d') }}"
                       required>
                @error('slot_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="slot_time" class="form-label fw-bold">Time <span class="text-danger">*</span></label>
                <input type="time" 
                       class="form-control @error('slot_time') is-invalid @enderror" 
                       id="slot_time" 
                       name="slot_time" 
                       value="{{ old('slot_time') }}"
                       required>
                @error('slot_time')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Pickup Point --}}
            <div class="mb-3">
              <label for="pickup_point" class="form-label fw-bold">Pickup Point <span class="text-danger">*</span></label>
              <select class="form-select @error('pickup_point') is-invalid @enderror" 
                      id="pickup_point" 
                      name="pickup_point" 
                      required>
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

            <hr class="my-4">

            {{-- Appointments Section --}}
            <h5 class="mb-3 text-primary">👥 Appointments (Optional)</h5>
            <p class="text-muted small mb-3">You can add multiple appointments to this slot</p>

            <div id="appointments-container">
              <div class="appointment-row mb-3 p-3 border rounded bg-light">
                <div class="row">
                  <div class="col-md-5 mb-2">
                    <label class="form-label fw-bold">Name</label>
                    <input type="text" 
                           class="form-control" 
                           name="appointment_name[]" 
                           placeholder="Patient name">
                  </div>
                  <div class="col-md-5 mb-2">
                    <label class="form-label fw-bold">Phone</label>
                    <input type="tel" 
                           class="form-control" 
                           name="appointment_phone[]" 
                           placeholder="+91 98765 43210">
                  </div>
                  <div class="col-md-2 d-flex align-items-end mb-2">
                    <button type="button" class="btn btn-danger btn-sm remove-appointment" disabled>
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-appointment">
              + Add Another Appointment
            </button>

            <div class="d-flex justify-content-between mt-4">
              <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                ← Back to Dashboard
              </a>
              <button type="submit" class="btn btn-primary">
                📅 Create Slot
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('extra-scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('appointments-container');
    const addBtn = document.getElementById('add-appointment');

    // Add appointment row
    addBtn.addEventListener('click', function() {
      const newRow = document.createElement('div');
      newRow.className = 'appointment-row mb-3 p-3 border rounded bg-light';
      newRow.innerHTML = `
        <div class="row">
          <div class="col-md-5 mb-2">
            <label class="form-label fw-bold">Name</label>
            <input type="text" class="form-control" name="appointment_name[]" placeholder="Patient name">
          </div>
          <div class="col-md-5 mb-2">
            <label class="form-label fw-bold">Phone</label>
            <input type="tel" class="form-control" name="appointment_phone[]" placeholder="+91 98765 43210">
          </div>
          <div class="col-md-2 d-flex align-items-end mb-2">
            <button type="button" class="btn btn-danger btn-sm remove-appointment">Remove</button>
          </div>
        </div>
      `;
      container.appendChild(newRow);
      updateRemoveButtons();
    });

    // Remove appointment row (event delegation)
    container.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-appointment')) {
        e.target.closest('.appointment-row').remove();
        updateRemoveButtons();
      }
    });

    // Update remove button states
    function updateRemoveButtons() {
      const rows = container.querySelectorAll('.appointment-row');
      rows.forEach((row, index) => {
        const removeBtn = row.querySelector('.remove-appointment');
        removeBtn.disabled = rows.length === 1;
      });
    }

    // Set minimum date to today
    document.getElementById('slot_date').min = new Date().toISOString().split('T')[0];
  });
</script>
@endsection
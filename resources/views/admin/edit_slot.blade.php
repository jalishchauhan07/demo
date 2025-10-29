@extends('layouts.admin')

@section('title', 'Edit Slot')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
          <h4 class="mb-0">✏️ Edit Appointment Slot</h4>
        </div>

        <div class="card-body">
          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('slot.update', $slot->id) }}">
            @csrf
            @method('PUT')

            {{-- Date & Time Section --}}
            <h5 class="mb-3 text-warning">📆 Date & Time</h5>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                <input type="date" 
                       name="slot_date" 
                       class="form-control @error('slot_date') is-invalid @enderror" 
                       value="{{ old('slot_date', $slot->slot_date->format('Y-m-d')) }}" 
                       min="{{ date('Y-m-d') }}"
                       required>
                @error('slot_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Time <span class="text-danger">*</span></label>
                <input type="time" 
                       name="slot_time" 
                       class="form-control @error('slot_time') is-invalid @enderror" 
                       value="{{ old('slot_time', $slot->slot_time) }}" 
                       required>
                @error('slot_time')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            {{-- Pickup Point --}}
            <div class="mb-3">
              <label class="form-label fw-bold">Pickup Point <span class="text-danger">*</span></label>
              <select name="pickup_point" 
                      class="form-control @error('pickup_point') is-invalid @enderror" 
                      required>
                <option value="">Select Pickup Point</option>
                @foreach($pickupPoints as $point)
                  <option value="{{ $point }}" {{ old('pickup_point', $slot->pickup_point) == $point ? 'selected' : '' }}>
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
            <h5 class="mb-3 text-warning">👥 Appointments</h5>
            <p class="text-muted small mb-3">Edit or add appointments for this slot</p>

            <div id="appointments-container">
              @if(!empty($slot->appointment) && is_array($slot->appointment))
                @foreach($slot->appointment as $index => $appt)
                  <div class="appointment-row mb-3 p-3 border rounded bg-light">
                    <div class="row">
                      <div class="col-md-5 mb-2">
                        <label class="form-label fw-bold">Name</label>
                        <input type="text" 
                               class="form-control" 
                               name="appointment_name[]" 
                               value="{{ $appt['name'] ?? '' }}" 
                               placeholder="Patient name">
                      </div>
                      <div class="col-md-5 mb-2">
                        <label class="form-label fw-bold">Phone</label>
                        <input type="tel" 
                               class="form-control" 
                               name="appointment_phone[]" 
                               value="{{ $appt['phone'] ?? '' }}" 
                               placeholder="+91 98765 43210">
                      </div>
                      <div class="col-md-2 d-flex align-items-end mb-2">
                        <button type="button" class="btn btn-danger btn-sm remove-appointment">
                          Remove
                        </button>
                      </div>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="appointment-row mb-3 p-3 border rounded bg-light">
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
                      <button type="button" class="btn btn-danger btn-sm remove-appointment" disabled>
                        Remove
                      </button>
                    </div>
                  </div>
                </div>
              @endif
            </div>

            <button type="button" class="btn btn-outline-warning btn-sm mb-3" id="add-appointment">
              + Add Another Appointment
            </button>

            <div class="d-flex justify-content-between mt-4">
              <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-warning">Update Slot</button>
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

    container.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-appointment')) {
        e.target.closest('.appointment-row').remove();
        updateRemoveButtons();
      }
    });

    function updateRemoveButtons() {
      const rows = container.querySelectorAll('.appointment-row');
      rows.forEach((row, index) => {
        const removeBtn = row.querySelector('.remove-appointment');
        removeBtn.disabled = rows.length === 1;
      });
    }

    updateRemoveButtons();
  });
</script>
@endsection
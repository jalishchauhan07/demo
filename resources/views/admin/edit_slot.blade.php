@extends('layouts.admin')

@section('title', 'Edit Slot')

@section('content')
<div class="content-center">
  <div class="card p-4 shadow-sm" style="max-width: 600px; width: 100%;">
    <h4 class="mb-3">Edit Appointment Slot</h4>
    
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

      {{-- Slot Date --}}
      <div class="mb-3">
        <label class="form-label fw-bold">Date</label>
        <input type="date" name="slot_date" class="form-control" required
               value="{{ old('slot_date', $slot->slot_date->format('Y-m-d')) }}">
      </div>

      {{-- Slot Time --}}
      <div class="mb-3">
        <label class="form-label fw-bold">Time</label>
        <input type="time" name="slot_time" class="form-control" required
               value="{{ old('slot_time', $slot->slot_time) }}">
      </div>

      {{-- Pickup Point --}}
      <div class="mb-3">
        <label class="form-label fw-bold">Pickup Point</label>
        <select name="pickup_point" class="form-control" required>
          <option value="">Select Pickup Point</option>
          @foreach($pickupPoints as $point)
            <option value="{{ $point }}" {{ old('pickup_point', $slot->pickup_point) == $point ? 'selected' : '' }}>
              {{ $point }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Appointment --}}
      <div class="mb-3">
        <label class="form-label fw-bold">Appointment With</label>
        <select name="appointment" class="form-control" required>
          <option value="">Select Doctor / Staff</option>
          @foreach($appointments as $appt)
            <option value="{{ $appt }}" {{ old('appointment', $slot->appointment) == $appt ? 'selected' : '' }}>
              {{ $appt }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Update Slot</button>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary flex-fill">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection

@section('extra-styles')
<style>
  .content-center {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 100px);
    padding: 20px;
  }

  .card {
    margin: auto;
  }
</style>
@endsection

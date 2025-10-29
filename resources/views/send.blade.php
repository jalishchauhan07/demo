@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="card shadow-lg border-0 mx-auto" style="max-width: 500px;">
    <div class="card-body p-4">
      <h4 class="text-center mb-4">📤 Send WhatsApp Slot Message</h4>

      {{-- Success / Error --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form action="{{ route('whatsapp.send') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="to" class="form-label">📞 WhatsApp Number (with country code)</label>
          <input type="text" class="form-control" id="to" name="to" placeholder="+911234567890" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Send Available Slots</button>
      </form>

      <p class="text-muted small mt-3">
        ➡️ This will send all upcoming available slots to the entered WhatsApp number using Twilio.
      </p>
    </div>
  </div>
</div>
@endsection

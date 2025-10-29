@extends('layouts.admin')

@section('title', 'Booked Slots')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <h4>📅 Booked Appointment Slots</h4>
  <a href="{{ route('slot.create') }}" class="btn btn-primary btn-sm">+ Add New Slot</a>
</div>

<!-- Filters -->
<div class="card filter-card mb-3 p-3">
  <form method="GET" action="{{ route('dashboard') }}" class="row g-3 align-items-end">
    
    <div class="col-md-3">
      <label class="form-label fw-bold">Sort By</label>
      <select name="sort" class="form-select">
        <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest First)</option>
        <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest First)</option>
        <option value="time_asc" {{ request('sort') == 'time_asc' ? 'selected' : '' }}>Time (Early to Late)</option>
        <option value="time_desc" {{ request('sort') == 'time_desc' ? 'selected' : '' }}>Time (Late to Early)</option>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label fw-bold">From Date</label>
      <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
    </div>

    <div class="col-md-3">
      <label class="form-label fw-bold">Show Per Page</label>
      <select name="per_page" class="form-select">
        @foreach([5, 10, 25, 50, 100] as $num)
          <option value="{{ $num }}" {{ request('per_page', 10) == $num ? 'selected' : '' }}>
            {{ $num }} Slots
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
    </div>
  </form>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<!-- Results Count -->
<div class="mb-3">
  <span class="results-count">
    📊 Showing {{ $slots->count() }} booked slot{{ $slots->count() != 1 ? 's' : '' }}
  </span>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Date</th>
          <th>Time</th>
          <th>Pickup Point</th>
          <th>Appointment</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($slots as $slot)
          <tr>
            <td><strong>{{ ($slots->currentPage() - 1) * $slots->perPage() + $loop->iteration }}</strong></td>
            <td>{{ $slot->formatted_date }}</td>
            <td>{{ $slot->formatted_time }}</td>
            <td>{{ $slot->pickup_point ?? 'N/A' }}</td>
            <td>
              @if($slot->appointment)
                {{ $slot->appointment?? 'Unknown' }} <br>
                <small class="text-muted">{{ $slot->appointment?? '' }}</small>
             
            </td>
            <td>
              <a href="{{ route('slot.edit', $slot->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>
              <a href="{{ route('slot.delete', $slot->id) }}" 
                 class="btn btn-sm btn-danger" 
                 onclick="return confirm('Are you sure you want to delete this slot?')">
                 Delete
              </a>
            </td>
          </tr>
          @endif
        @empty
          <tr>
            <td colspan="6" class="empty-state text-center py-4">
              <div style="font-size: 2rem;">📭</div>
              <p class="text-muted mt-2">No booked appointment slots found.</p>
              <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary mt-2">Clear Filters</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="card-footer d-flex justify-content-center">
    {{ $slots->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection

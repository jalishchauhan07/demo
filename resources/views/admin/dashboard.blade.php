@extends('layouts.admin')

@section('title', 'Dashboard - Booked Slots')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
  <h4>📅 Appointment Slots Dashboard</h4>
  <a href="{{ route('slot.create') }}" class="btn btn-primary">+ Add New Slot</a>
</div>

<!-- Filters -->
<div class="card filter-card mb-3 p-3 shadow-sm">
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

    <div class="col-md-2">
      <label class="form-label fw-bold">Show Per Page</label>
      <select name="per_page" class="form-select">
        @foreach([5, 10, 25, 50, 100] as $num)
          <option value="{{ $num }}" {{ request('per_page', 10) == $num ? 'selected' : '' }}>
            {{ $num }} Slots
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-md-4">
      <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
    </div>
  </form>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    ✅ {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<!-- Results Count -->
<div class="mb-3">
  <span class="badge bg-info text-dark">
    📊 Showing {{ $slots->count() }} of {{ $slots->total() }} slot{{ $slots->total() != 1 ? 's' : '' }}
  </span>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Time</th>
          <th>Pickup Point</th>
          <th>Appointments</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($slots as $slot)
          <tr>
            <td><strong>{{ ($slots->currentPage() - 1) * $slots->perPage() + $loop->iteration }}</strong></td>
            <td>
              <span class="badge bg-secondary">{{ $slot->formatted_date }}</span>
            </td>
            <td>
              <span class="badge bg-primary">{{ $slot->formatted_time }}</span>
            </td>
            <td>
              <i class="bi bi-geo-alt-fill text-danger"></i> {{ $slot->pickup_point ?? 'N/A' }}
            </td>
            <td>
              @if(!empty($slot->appointment) && is_array($slot->appointment))
                <button type="button" 
                        class="btn btn-sm btn-info" 
                        data-bs-toggle="modal" 
                        data-bs-target="#appointmentModal{{ $slot->id }}">
                  👥 {{ count($slot->appointment) }} Appointment{{ count($slot->appointment) != 1 ? 's' : '' }}
                </button>

                <!-- Modal -->
                <div class="modal fade" id="appointmentModal{{ $slot->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Appointments for {{ $slot->formatted_date }} at {{ $slot->formatted_time }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <ul class="list-group">
                          @foreach($slot->appointment as $index => $appt)
                            <li class="list-group-item">
                              <strong>{{ $index + 1 }}. {{ $appt['name'] ?? 'Unknown' }}</strong><br>
                              <small class="text-muted">📞 {{ $appt['phone'] ?? 'N/A' }}</small>
                            </li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              @else
                <span class="text-muted">No appointments</span>
              @endif
            </td>
            <td>
              <div class="btn-group" role="group">
                <a href="{{ route('slot.edit', $slot->id) }}" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('slot.delete', $slot->id) }}" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this slot?')">
                   <i class="bi bi-trash"></i> Delete
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5">
              <div style="font-size: 3rem;">📭</div>
              <p class="text-muted mt-2">No appointment slots found.</p>
              <a href="{{ route('slot.create') }}" class="btn btn-primary mt-2">
                + Create Your First Slot
              </a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  @if($slots->hasPages())
    <div class="card-footer d-flex justify-content-center">
      {{ $slots->links('pagination::bootstrap-5') }}
    </div>
  @endif
</div>
@endsection

@section('extra-styles')
<style>
  .page-header {
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
  }
  
  .filter-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  }
  
  .table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
  }
  
  .badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
  }
  
  .btn-group .btn {
    margin: 0;
  }
</style>
@endsection
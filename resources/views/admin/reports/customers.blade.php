@extends('admin.layouts.app')

@section('title', 'Customers Report')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1><i class="bi bi-people"></i> Customers Report</h1>
        <p class="text-muted">Customer analytics and insights</p>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title">Total Customers</h6>
                <h2 class="card-text">{{ $totalCustomers }}</h2>
                <small>Registered customers</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Active Customers</h6>
                <h2 class="card-text">{{ $activeCustomers }}</h2>
                <small>{{ $activeCustomers > 0 ? round(($activeCustomers/$totalCustomers)*100) : 0 }}% of total</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6 class="card-title">Total Bookings</h6>
                <h2 class="card-text">{{ $totalBookings }}</h2>
                <small>All customer bookings</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title">Avg Bookings/Customer</h6>
                <h2 class="card-text">{{ number_format($avgBookingsPerCustomer, 1) }}</h2>
                <small>Average bookings per customer</small>
            </div>
        </div>
    </div>
</div>

<!-- Registration Trend -->
<div class="card admin-card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i> Customer Registration Trend (Last 6 Months)</h5>
    </div>
    <div class="card-body">
        <div style="height: 300px;">
            <canvas id="registrationChart"></canvas>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="card admin-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-list me-2"></i> Customer List</h5>
    </div>
    <div class="card-body">
        @if($customers->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-people display-1 text-muted mb-3"></i>
                <h3>No Customers Found</h3>
                <p class="mb-0">No customers have registered yet.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Bookings</th>
                            <th>Total Spent</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>
                                <strong>CUST-{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>
                                <strong>{{ $customer->full_name }}</strong><br>
                                <small class="text-muted">{{ $customer->email }}</small>
                            </td>
                            <td>
                                {{ $customer->phone ?? 'Not provided' }}
                            </td>
                            <td>
                                @if($customer->city && $customer->country)
                                    {{ $customer->city }}, {{ $customer->country }}
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $customer->bookings_count }}</span>
                            </td>
                            <td>
                                <strong>${{ number_format($customer->bookings_sum_total_amount ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $customer->status == 'active' ? 'success' : ($customer->status == 'suspended' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $customer->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="viewCustomerDetails({{ $customer->id }})" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($customers->hasPages())
            <div class="mt-4">
                {{ $customers->links() }}
            </div>
            @endif
        @endif
    </div>
</div>

<!-- Customer Segments -->
@if(!$customers->isEmpty())
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-award me-2"></i> Top Customers by Revenue</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($customers->sortByDesc('bookings_sum_total_amount')->take(5) as $topCustomer)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $topCustomer->full_name }}</h6>
                            <strong class="text-success">${{ number_format($topCustomer->bookings_sum_total_amount ?? 0, 2) }}</strong>
                        </div>
                        <p class="mb-1">
                            {{ $topCustomer->bookings_count }} bookings • {{ $topCustomer->email }}
                        </p>
                        <small class="text-muted">Customer since {{ $topCustomer->created_at->format('M Y') }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i> Top Customers by Bookings</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($customers->sortByDesc('bookings_count')->take(5) as $topCustomer)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $topCustomer->full_name }}</h6>
                            <strong class="text-primary">{{ $topCustomer->bookings_count }} bookings</strong>
                        </div>
                        <p class="mb-1">
                            ${{ number_format($topCustomer->bookings_sum_total_amount ?? 0, 2) }} spent • {{ $topCustomer->email }}
                        </p>
                        <small class="text-muted">Customer since {{ $topCustomer->created_at->format('M Y') }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Customer Details Modal -->
<div class="modal fade" id="customerDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="customerDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Registration Trend Chart
        const regCtx = document.getElementById('registrationChart').getContext('2d');
        const regLabels = {!! json_encode(array_keys($registrationTrend)) !!};
        const regData = {!! json_encode(array_values($registrationTrend)) !!};
        
        new Chart(regCtx, {
            type: 'bar',
            data: {
                labels: regLabels,
                datasets: [{
                    label: 'New Registrations',
                    data: regData,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: '#3498db',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
    
    function viewCustomerDetails(customerId) {
        // For now, just show a placeholder
        // In a real app, you would fetch customer details via AJAX
        const modalContent = `
            <div class="text-center py-4">
                <i class="bi bi-person-circle display-1 text-primary mb-3"></i>
                <h4>Customer Details</h4>
                <p class="text-muted">Customer ID: CUST-${String(customerId).padStart(5, '0')}</p>
                <p>Detailed customer information and booking history would be displayed here.</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    In a complete implementation, this would show detailed customer profile, 
                    booking history, preferences, and communication history.
                </div>
            </div>
        `;
        
        document.getElementById('customerDetailsContent').innerHTML = modalContent;
        const modal = new bootstrap.Modal(document.getElementById('customerDetailsModal'));
        modal.show();
    }
</script>
@endsection
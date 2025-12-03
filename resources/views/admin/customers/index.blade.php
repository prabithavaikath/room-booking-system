@extends('admin.layouts.app')

@section('title', 'Customer Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1><i class="bi bi-people"></i> Customer Management</h1>
        <p class="text-muted">Manage all registered customers</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Add Customer
        </a>
        <a href="{{ route('admin.customers.export') }}" class="btn btn-secondary">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Customers</h6>
                <h3 class="fw-bold">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-success">
            <div class="card-body">
                <h6 class="text-muted">Active</h6>
                <h3 class="fw-bold text-success">{{ $stats['active'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-warning">
            <div class="card-body">
                <h6 class="text-muted">Inactive</h6>
                <h3 class="fw-bold text-warning">{{ $stats['inactive'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm text-center border-top border-danger">
            <div class="card-body">
                <h6 class="text-muted">Suspended</h6>
                <h3 class="fw-bold text-danger">{{ $stats['suspended'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="Search by name, email, phone...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sort By</label>
                <select name="order_by" class="form-control">
                    <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                    <option value="first_name" {{ request('order_by') == 'first_name' ? 'selected' : '' }}>First Name</option>
                    <option value="last_name" {{ request('order_by') == 'last_name' ? 'selected' : '' }}>Last Name</option>
                    <option value="email" {{ request('order_by') == 'email' ? 'selected' : '' }}>Email</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Order</label>
                <select name="order_dir" class="form-control">
                    <option value="desc" {{ request('order_dir') == 'desc' ? 'selected' : '' }}>Descending</option>
                    <option value="asc" {{ request('order_dir') == 'asc' ? 'selected' : '' }}>Ascending</option>
                </select>
            </div>
            <div class="col-md-12 d-flex align-items-end">
                <div class="d-grid gap-2 w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($customers->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="mt-3">No Customers Found</h4>
                <p class="text-muted">Try adjusting your filters or add a new customer.</p>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Add New Customer
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>
                                <strong>{{ $customer->full_name }}</strong><br>
                                <small class="text-muted">{{ $customer->email }}</small>
                            </td>
                            <td>
                                <p class="mb-1">{{ $customer->phone }}</p>
                                <small>{{ $customer->email }}</small>
                            </td>
                            <td>
                                @if($customer->city || $customer->country)
                                <p class="mb-1">{{ $customer->city }}</p>
                                <small>{{ $customer->country }}</small>
                                @else
                                <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary p-2">
                                    {{ $customer->bookings()->count() }} bookings
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $customer->status == 'active' ? 'success' : ($customer->status == 'inactive' ? 'warning' : 'danger') }} p-2">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $customer->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $customers->links() }}
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i>
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
            </div>
        @endif
    </div>
</div>
@endsection
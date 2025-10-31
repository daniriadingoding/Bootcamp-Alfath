@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @if(Auth::user()->isMerchant())
                        Daftar Semua Pesanan Customer
                    @else
                        Daftar Pesanan Saya
                    @endif
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    @if(Auth::user()->isMerchant())
                                        <th scope="col">Nama Customer</th>
                                    @endif
                                    <th scope="col">Detail Pesanan</th>
                                    <th scope="col">Total Harga</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Tanggal Pesan</th>
                                    @if(Auth::user()->isMerchant())
                                        <th scope="col">Ubah Status</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                <tr>
                                    <th scope="row">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-link fw-bold p-0">
                                            #{{ $order->id }}
                                        </a>
                                    </th>
                                    @if(Auth::user()->isMerchant())
                                        <td>{{ $order->user->name }}</td>
                                    @endif
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($order->items as $item)
                                                <li>{{ $item->foodMenu->name ?? 'Menu Dihapus' }} (x{{ $item->quantity }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            if ($order->status == 'pending') $badgeClass = 'bg-warning text-dark';
                                            if ($order->status == 'onprogress') $badgeClass = 'bg-info text-dark';
                                            if ($order->status == 'done') $badgeClass = 'bg-success';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    
                                    @if(Auth::user()->isMerchant())
                                    <td>
                                        <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" @if($order->status == 'pending') selected disabled @endif>Pending</option>
                                                <option value="onprogress" @if($order->status == 'onprogress') selected @endif>On Progress</option>
                                                <option value="done" @if($order->status == 'done') selected @endif>Done</option>
                                            </select>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isMerchant() ? '7' : '5' }}" class="text-center">Tidak ada pesanan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
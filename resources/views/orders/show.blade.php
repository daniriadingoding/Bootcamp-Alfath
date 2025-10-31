@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Detail Pesanan #{{ $order->id }}
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Pemesan:</strong> {{ $order->user->name }}</p>
                            <p><strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                            <p><strong>Alamat Pengiriman:</strong> {{ $order->address }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0"><strong>Status:</strong>
                                @php
                                    $badgeClass = 'bg-secondary';
                                    if ($order->status == 'pending') $badgeClass = 'bg-warning text-dark';
                                    if ($order->status == 'onprogress') $badgeClass = 'bg-info text-dark';
                                    if ($order->status == 'done') $badgeClass = 'bg-success';
                                @endphp
                                <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($order->status) }}</span>
                            </p>
                            <h4 class="mt-2"><strong>Total Harga: Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong></h4>
                        </div>
                    </div>

                    <hr>
                    
                    <h5 class="mt-4">Item Pesanan:</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama Makanan</th>
                                    <th scope="col">Harga Satuan</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $index => $item)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ $item->foodMenu->name ?? 'Menu Dihapus' }}</td>
                                        <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total Item:</td>
                                    <td class="fw-bold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-primary">Kembali ke Daftar Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
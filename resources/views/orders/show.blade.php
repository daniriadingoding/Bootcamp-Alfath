@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Detail Pesanan #{{ $order->id }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Pemesan:</strong> {{ $order->user->name }}</p>
                            <p><strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                            <p><strong>Alamat Pengiriman:</strong> {{ $order->address }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1"><strong>Status:</strong>
                                @if ($order->status == 'pending')
                                <span class="badge bg-warning text-dark fs-6">Menunggu Konfirmasi</span>
                                @elseif ($order->status == 'onprogress')
                                <span class="badge bg-info text-dark fs-6">Sedang Diproses</span>
                                @elseif ($order->status == 'done')
                                <span class="badge bg-success fs-6">Selesai</span>
                                @elseif ($order->status == 'cancelled')
                                <span class="badge bg-danger fs-6">Dibatalkan</span>
                                @else
                                <span class="badge bg-secondary fs-6">{{ ucfirst($order->status) }}</span>
                                @endif
                            </p>
                            <h4 class="mt-3"><strong>Total Harga:</strong> Rp{{ number_format($order->total_price, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <hr>
                    <h4 class="mb-3">Item Pesanan:</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Makanan</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $index => $item) <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->foodMenu->name ?? 'Menu Dihapus' }}</td>
                                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Item:</th>
                                    <th>Rp{{ number_format($order->total_price, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="text-end mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Kembali ke Daftar Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
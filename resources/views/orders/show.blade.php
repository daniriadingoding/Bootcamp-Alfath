@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 bg-indigo-600 border-b border-indigo-600">
                <h3 class="text-lg font-semibold text-white">Detail Pesanan #{{ $order->id }}</h3>
            </div>
            
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="mb-2"><strong class="font-medium text-gray-700 dark:text-gray-300">Pemesan:</strong> {{ $order->user->name }}</p>
                        <p class="mb-2"><strong class="font-medium text-gray-700 dark:text-gray-300">Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                        <p class="mb-2"><strong class="font-medium text-gray-700 dark:text-gray-300">Alamat Pengiriman:</strong> {{ $order->address }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <div class="mb-4">
                            <strong class="font-medium text-gray-700 dark:text-gray-300 block mb-1">Status:</strong>
                            @if ($order->status == 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Menunggu Konfirmasi</span>
                            @elseif ($order->status == 'onprogress')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Sedang Diproses</span>
                            @elseif ($order->status == 'done')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Selesai</span>
                            @elseif ($order->status == 'cancelled')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Dibatalkan</span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">Total Harga: Rp{{ number_format($order->total_price, 0, ',', '.') }}</h4>
                    </div>
                </div>

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                <h4 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Item Pesanan:</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Makanan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($order->items as $index => $item) 
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->foodMenu && $item->foodMenu->image)
                                        <img src="{{ Storage::url($item->foodMenu->image) }}" class="w-16 h-12 object-cover rounded shadow-sm" alt="{{ $item->foodMenu->name }}">
                                    @else
                                        <span class="text-gray-400 text-xs italic">No Image</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->foodMenu->name ?? 'Menu Dihapus' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">Total Item:</th>
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-white">Rp{{ number_format($order->total_price, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-6 text-right">
                    <a href="{{ route('orders.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Kembali ke Daftar Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
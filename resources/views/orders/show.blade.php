@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-4 bg-indigo-600 border-b border-indigo-600 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white">Detail Pesanan #{{ $order->id }}</h3>
                <span class="text-indigo-100 text-sm">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="font-bold text-gray-800 dark:text-gray-200 mb-2">Informasi Pesanan</h4>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span class="font-semibold">Toko (Merchant):</span> 
                            <span class="text-indigo-600 font-bold">{{ $order->items->first()->foodMenu->user->name ?? 'Unknown' }}</span>
                        </p>

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span class="font-semibold">Pemesan:</span> {{ $order->user->name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span class="font-semibold">Email:</span> {{ $order->user->email }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">Alamat Pengiriman:</span> {{ $order->address }}
                        </p>
                    </div>
                    
                    <div class="text-left md:text-right border-t md:border-t-0 pt-4 md:pt-0 border-gray-200 dark:border-gray-700">
                        <div class="mb-4 space-y-2">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Status Order:</span>
                                @if ($order->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu Konfirmasi</span>
                                @elseif ($order->status == 'onprogress')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sedang Diproses</span>
                                @elseif ($order->status == 'done')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Selesai</span>
                                @elseif ($order->status == 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($order->status) }}</span>
                                @endif
                            </div>

                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">Pembayaran:</span>
                                @if($order->payment_status == 'paid') 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Lunas
                                    </span>
                                @elseif($order->payment_status == 'failed' || $order->payment_status == 'expired') 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Gagal</span>
                                @elseif($order->payment_status == 'cancelled') 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Dibatalkan</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Belum Dibayar</span>
                                @endif
                            </div>
                        </div>
                        
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                            Rp{{ number_format($order->total_price, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>

                @if(Auth::user()->isCustomer() && $order->payment_status == 'pending' && $order->status != 'cancelled')
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
                        <div class="flex justify-between items-center flex-wrap gap-4">
                            <div>
                                <h5 class="text-sm font-bold text-yellow-800 dark:text-yellow-200">Selesaikan Pembayaran</h5>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">Pesanan akan diproses setelah pembayaran berhasil dikonfirmasi.</p>
                            </div>
                            <a href="{{ route('payment.create', $order) }}" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                💳 Bayar Sekarang
                            </a>
                        </div>
                    </div>
                @endif

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                <h4 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Rincian Menu Pesanan</h4>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Menu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Satuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($order->items as $index => $item) 
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item->foodMenu && $item->foodMenu->image)
                                            <img class="h-10 w-10 rounded-md object-cover mr-3" src="{{ Storage::url($item->foodMenu->image) }}" alt="">
                                        @endif
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->foodMenu->name ?? 'Menu Dihapus' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    Rp{{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium text-right">
                                    Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700 font-bold text-gray-900 dark:text-white">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm">Total Pembayaran:</td>
                                <td class="px-6 py-4 text-right text-sm">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('orders.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Kembali ke Daftar Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
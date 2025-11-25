@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold mb-4">
                    @if(Auth::user()->isMerchant())
                        Daftar Semua Pesanan Customer
                    @else
                        Daftar Pesanan Saya
                    @endif
                </h2>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order ID</th>
                                @if(Auth::user()->isMerchant())
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Customer</th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Detail Pesanan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Harga</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Pesan</th>
                                @if(Auth::user()->isMerchant())
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ubah Status</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 font-bold">
                                        #{{ $order->id }}
                                    </a>
                                </td>
                                @if(Auth::user()->isMerchant())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $order->user->name }}</td>
                                @endif
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <ul class="space-y-1">
                                        @foreach($order->items as $item)
                                            <li class="flex items-center space-x-2">
                                                @if($item->foodMenu && $item->foodMenu->image)
                                                    <img src="{{ Storage::url($item->foodMenu->image) }}" alt="img" class="w-8 h-8 object-cover rounded shadow-sm">
                                                @else
                                                    <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center text-xs">No</div>
                                                @endif
                                                <span>
                                                    {{ $item->foodMenu->name ?? 'Menu Dihapus' }} <span class="text-gray-400">(x{{ $item->quantity }})</span>
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badgeClass = 'bg-gray-100 text-gray-800';
                                        if ($order->status == 'pending') $badgeClass = 'bg-yellow-100 text-yellow-800';
                                        if ($order->status == 'onprogress') $badgeClass = 'bg-blue-100 text-blue-800';
                                        if ($order->status == 'done') $badgeClass = 'bg-green-100 text-green-800';
                                        if ($order->status == 'cancelled') $badgeClass = 'bg-red-100 text-red-800'; // Warna Merah untuk Cancelled
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                
                                @if(Auth::user()->isMerchant())
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-gray-300" onchange="this.form.submit()">
                                            <option value="pending" @if($order->status == 'pending') selected disabled @endif>Pending</option>
                                            <option value="onprogress" @if($order->status == 'onprogress') selected @endif>On Progress</option>
                                            <option value="done" @if($order->status == 'done') selected @endif>Done</option>
                                            <option value="cancelled" @if($order->status == 'cancelled') selected @endif>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->isMerchant() ? '7' : '5' }}" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                    Tidak ada pesanan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
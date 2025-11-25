@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Edit Menu Makanan: {{ $foodMenu->name }}</h2>

                <form action="{{ route('foodmenu.update', $foodMenu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    {{-- FITUR KHUSUS ADMIN: PILIH MERCHANT --}}
                    @if(Auth::user()->isAdmin())
                        <div>
                            <label for="merchant_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pemilik Menu (Merchant)</label>
                            <select id="merchant_id" name="merchant_id" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                @foreach($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $foodMenu->user_id) == $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->name }} ({{ $merchant->address ?? 'Lokasi Belum Ada' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('merchant_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Makanan</label>
                        <input id="name" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" type="text" name="name" value="{{ old('name', $foodMenu->name) }}" required autofocus />
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('description', $foodMenu->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Harga</label>
                        <input id="price" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" type="number" step="0.01" name="price" value="{{ old('price', $foodMenu->price) }}" required />
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Gambar (biarkan kosong jika tidak ingin mengubah)</label>
                        <input id="image" type="file" name="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 mt-1" />
                        
                        @if ($foodMenu->image)
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Gambar saat ini:</p>
                                <img src="{{ Storage::url($foodMenu->image) }}" alt="{{ $foodMenu->name }}" class="w-32 h-32 object-cover rounded-md border border-gray-300 dark:border-gray-600">
                            </div>
                        @endif
                        
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Menu
                        </button>
                        <a href="{{ route('foodmenu.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
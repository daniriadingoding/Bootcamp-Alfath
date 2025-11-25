@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 dark:text-gray-200 mb-8">Pilih Menu Makanan Anda</h1>
        
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($foodItems as $foodItem)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-full">
                    <img src="{{ $foodItem->image ? Storage::url($foodItem->image) : 'https://via.placeholder.com/300x200' }}" 
                         class="w-full h-48 object-cover" 
                         alt="{{ $foodItem->name }}">
                    
                    <div class="p-6 flex flex-col flex-grow">
                        <h5 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $foodItem->name }}</h5>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 flex-grow">{{ $foodItem->description }}</p>
                        
                        <div class="mt-auto">
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">
                                Rp{{ number_format($foodItem->price, 0, ',', '.') }}
                            </p>
                            
                            <div class="flex items-center">
                                <span class="text-gray-700 dark:text-gray-300 mr-3 text-sm">Jumlah</span>
                                <input type="number" 
                                       class="item-quantity w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" 
                                       value="0" min="0" 
                                       data-id="{{ $foodItem->id }}"
                                       data-price="{{ $foodItem->price }}"
                                       data-name="{{ $foodItem->name }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <hr class="my-8 border-gray-300 dark:border-gray-700">

            <div id="items-container"></div>
            <input type="hidden" name="total_price" id="hiddenTotalPrice" value="0">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6">
                    <div class="mb-6">
                        <label for="address" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Alamat Pengiriman:</label>
                        <textarea name="address" id="address" 
                                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" 
                                  rows="3" required>{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center">
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0">
                            Total Harga: Rp<span id="totalPrice">0</span>
                        </h4>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 text-lg">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderForm = document.getElementById('orderForm');
    const itemQuantities = document.querySelectorAll('.item-quantity');
    const totalPriceSpan = document.getElementById('totalPrice');
    const hiddenTotalPriceInput = document.getElementById('hiddenTotalPrice');
    const itemsContainer = document.getElementById('items-container');
    let cart = {}; 

    function calculateTotalPrice() {
        let total = 0;
        itemsContainer.innerHTML = ''; 
        
        itemQuantities.forEach(input => {
            const quantity = parseInt(input.value);
            const price = parseFloat(input.dataset.price);
            const id = input.dataset.id;
            
            if (!isNaN(quantity) && quantity > 0) {
                total += quantity * price;
                cart[id] = { quantity, price };
                
                itemsContainer.innerHTML += `
                    <input type="hidden" name="items[${id}][food_menu_id]" value="${id}">
                    <input type="hidden" name="items[${id}][quantity]" value="${quantity}">
                    <input type="hidden" name="items[${id}][price]" value="${price}">
                `;
            } else {
                delete cart[id];
            }
        });
        
        totalPriceSpan.textContent = total.toLocaleString('id-ID');
        hiddenTotalPriceInput.value = total;
    }

    itemQuantities.forEach(input => {
        input.addEventListener('change', calculateTotalPrice);
        input.addEventListener('keyup', calculateTotalPrice);
    });

    orderForm.addEventListener('submit', function(event) {
        if (Object.keys(cart).length === 0 || hiddenTotalPriceInput.value == 0) {
            alert('Anda harus memilih setidaknya satu item makanan.');
            event.preventDefault();
        }
    });

    calculateTotalPrice(); 
});
</script>
@endsection
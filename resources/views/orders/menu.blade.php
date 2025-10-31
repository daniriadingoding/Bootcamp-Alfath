@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">Pilih Menu Makanan Anda</h1>
    
    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf
        <div class="row">
            @foreach($foodItems as $foodItem)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $foodItem->image ? Storage::url($foodItem->image) : 'https://via.placeholder.com/300x200' }}" class="card-img-top" alt="{{ $foodItem->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $foodItem->name }}</h5>
                        <p class="card-text text-muted">{{ $foodItem->description }}</p>
                        <p class="card-text mt-auto"><b>Harga: Rp{{ number_format($foodItem->price, 0, ',', '.') }}</b></p>
                        
                        <div class="input-group mt-2">
                            <span class="input-group-text">Jumlah</span>
                            <input type="number" class="form-control item-quantity" value="0" min="0" 
                                   data-id="{{ $foodItem->id }}"
                                   data-price="{{ $foodItem->price }}"
                                   data-name="{{ $foodItem->name }}">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <hr>

        <div id="items-container"></div>
        <input type="hidden" name="total_price" id="hiddenTotalPrice" value="0">

        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="address" class="form-label fw-bold">Alamat Pengiriman:</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold">Total Harga: Rp<span id="totalPrice">0</span></h4>
                    <button type="submit" class="btn btn-success btn-lg">Pesan Sekarang</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderForm = document.getElementById('orderForm');
    const itemQuantities = document.querySelectorAll('.item-quantity');
    const totalPriceSpan = document.getElementById('totalPrice');
    const hiddenTotalPriceInput = document.getElementById('hiddenTotalPrice');
    const itemsContainer = document.getElementById('items-container');
    let cart = {}; // { food_menu_id: { quantity: 1, price: 10000 } }

    function calculateTotalPrice() {
        let total = 0;
        itemsContainer.innerHTML = ''; // Kosongkan container
        
        itemQuantities.forEach(input => {
            const quantity = parseInt(input.value);
            const price = parseFloat(input.dataset.price);
            const id = input.dataset.id;
            
            if (!isNaN(quantity) && quantity > 0) {
                total += quantity * price;
                cart[id] = { quantity, price };
                
                // Tambahkan hidden input untuk form
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

    calculateTotalPrice(); // Hitung total awal saat load
});
</script>
@endsection
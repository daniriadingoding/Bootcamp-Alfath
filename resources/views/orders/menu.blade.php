@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Pilih Menu Makanan Anda</h2>
    
    <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="row">
            @foreach($foodItems as $foodItem)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $foodItem->image ? Storage::url($foodItem->image) : 'https://via.placeholder.com/300x200' }}" class="card-img-top" alt="{{ $foodItem->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $foodItem->name }}</h5>
                        <p class="card-text">{{ $foodItem->description }}</p>
                        <p class="card-text fw-bold">Harga: Rp{{ number_format($foodItem->price, 0, ',', '.') }}</p>
                        
                        <div class="mt-auto">
                            <label for="quantity-{{ $foodItem->id }}" class="form-label">Jumlah</label>
                            <input type="number" class="form-control quantity-input" id="quantity-{{ $foodItem->id }}" 
                                   data-id="{{ $foodItem->id }}" 
                                   data-price="{{ $foodItem->price }}" 
                                   data-name="{{ $foodItem->name }}"
                                   min="0" value="0">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div id="items-container"></div>
        <input type="hidden" name="total_price" id="total_price_input" value="0">

        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="address" class="form-label fw-bold">Alamat Pengiriman:</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <h3 id="total-price-display" class="fw-bold">Total Harga: Rp0</h3>
                
                <button type="submit" class="btn btn-primary w-100 mt-3">Pesan Sekarang</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.quantity-input');
    const itemsContainer = document.getElementById('items-container');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const totalPriceInput = document.getElementById('total_price_input');
    const orderForm = document.getElementById('orderForm');
    
    let cart = {}; // { food_menu_id: { quantity: 1, price: 10000, name: 'Mie' } }

    function updateCart() {
        let total = 0;
        itemsContainer.innerHTML = ''; // Kosongkan container input
        
        for (const id in cart) {
            const item = cart[id];
            if (item.quantity > 0) {
                total += item.quantity * item.price;
                
                // Tambahkan hidden input untuk form
                itemsContainer.innerHTML += `
                    <input type="hidden" name="items[${id}][food_menu_id]" value="${id}">
                    <input type="hidden" name="items[${id}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="items[${id}][price]" value="${item.price}">
                `;
            } else {
                delete cart[id]; // Hapus item jika quantity 0
            }
        }
        
        // Update tampilan total harga
        const formattedTotal = new Intl.NumberFormat('id-ID').format(total);
        totalPriceDisplay.textContent = `Total Harga: Rp${formattedTotal}`;
        totalPriceInput.value = total;
    }

    inputs.forEach(input => {
        input.addEventListener('change', function (e) {
            const id = e.target.dataset.id;
            const price = parseFloat(e.target.dataset.price);
            const name = e.target.dataset.name;
            const quantity = parseInt(e.target.value, 10);
            
            cart[id] = { quantity, price, name };
            updateCart();
        });
    });

    orderForm.addEventListener('submit', function(e) {
        if (Object.keys(cart).length === 0 || totalPriceInput.value == 0) {
            e.preventDefault();
            alert('Keranjang Anda masih kosong. Silakan pilih minimal 1 item.');
        }
    });
});
</script>
@endsection
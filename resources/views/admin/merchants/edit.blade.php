@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Atur Lokasi: {{ $merchant->name }}</h2>
                    <a href="{{ route('admin.merchants.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">Kembali</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <form action="{{ route('admin.merchants.update', $merchant) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Latitude</label>
                                <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $merchant->latitude) }}" 
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600" readonly required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Longitude</label>
                                <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $merchant->longitude) }}" 
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600" readonly required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Alamat Lengkap</label>
                                <textarea name="address" id="address" rows="4" 
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600" required>{{ old('address', $merchant->address) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">*Klik pada peta untuk mengisi koordinat otomatis.</p>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition">
                                Simpan Lokasi
                            </button>
                        </form>
                    </div>

                    <div class="md:col-span-2">
                        <div id="map" style="height: 500px; width: 100%; border-radius: 0.5rem; z-index: 1;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Tentukan Lokasi Awal (Default: Jakarta atau Lokasi Merchant Saat Ini)
    const initialLat = {{ $merchant->latitude ?? -6.2088 }};
    const initialLng = {{ $merchant->longitude ?? 106.8456 }};
    const initialZoom = {{ $merchant->latitude ? 16 : 12 }};

    // 2. Inisialisasi Peta
    const map = L.map('map').setView([initialLat, initialLng], initialZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // 3. Setup Marker
    let marker;

    // Jika merchant sudah punya lokasi, tampilkan marker
    if ({{ $merchant->latitude ? 'true' : 'false' }}) {
        marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);
        marker.bindPopup("Lokasi Toko Saat Ini").openPopup();
        
        // Event saat marker digeser (drag)
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });
    }

    // 4. Klik Peta
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Pindahkan atau buat marker baru
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, {draggable: true}).addTo(map);
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
            });
        }

        updateInputs(lat, lng);
    });

    // 5. Fungsi Update Input Form
    function updateInputs(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(7);
        document.getElementById('longitude').value = lng.toFixed(7);
    }
</script>
@endsection
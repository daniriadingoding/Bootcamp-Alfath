@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    #map { height: calc(100vh - 65px); z-index: 0; }
    .floating-panel { z-index: 1000; }
    .merchant-list::-webkit-scrollbar { width: 6px; }
    .merchant-list::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
</style>

<div class="relative w-full h-screen bg-gray-100 overflow-hidden">
    
    <div id="map" class="w-full absolute inset-0 bg-gray-200"></div>

    <div class="floating-panel absolute top-4 left-4 w-full max-w-md h-[85vh] flex flex-col gap-4 p-2 pointer-events-none">
        
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-xl pointer-events-auto border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                <span>📍</span> Jelajahi Kuliner Sekitar
            </h2>
            
            <div class="flex gap-2 mb-2">
                <select id="radius" class="form-select block w-1/3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500">
                    <option value="2">2 km</option>
                    <option value="5">5 km</option>
                    <option value="10" selected>10 km</option>
                    <option value="25">25 km</option>
                </select>
                
                <button onclick="getLocation()" class="w-2/3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow-md flex justify-center items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Cari Lokasi Saya
                </button>
            </div>
            <div id="status" class="text-xs text-gray-500 dark:text-gray-400 text-center min-h-[20px]"></div>
        </div>

        <div id="results-container" class="hidden flex-1 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-xl shadow-xl overflow-hidden flex flex-col pointer-events-auto border border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out">
            <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10 flex justify-between items-center">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-sm">Ditemukan: <span id="count" class="font-bold text-indigo-600">0</span> Merchant</h3>
            </div>
            
            <div id="merchant-list" class="merchant-list overflow-y-auto p-3 space-y-3 flex-1">
                </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. INISIALISASI PETA
    // Default view Jakarta sebelum lokasi ditemukan
    const map = L.map('map', { zoomControl: false }).setView([-6.200000, 106.816666], 12); 
    
    // Pindahkan zoom control ke kanan bawah agar tidak tertutup panel
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    let userMarker, radiusCircle;
    let markers = []; // Array untuk menyimpan marker merchant

    // Custom Icons
    const userIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    const merchantIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    // 2. FUNGSI GEOLOCATION
    function getLocation() {
        const status = document.getElementById('status');
        status.innerHTML = '<span class="animate-pulse">Mencari titik lokasi...</span>';
        
        if (!navigator.geolocation) {
            status.textContent = "Browser tidak mendukung Geolocation.";
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const radius = document.getElementById('radius').value;

                status.innerHTML = '<span class="text-green-600">Lokasi ditemukan! Memuat data...</span>';
                
                updateMapUserLocation(lat, lng, radius);
                fetchNearbyMerchants(lat, lng, radius);
            }, 
            (error) => {
                console.error(error);
                status.innerHTML = '<span class="text-red-500">Gagal mendeteksi lokasi. Pastikan GPS aktif.</span>';
            }
        );
    }

    // 3. UPDATE VISUAL PETA (USER)
    function updateMapUserLocation(lat, lng, radiusKm) {
        // Hapus marker lama jika ada
        if (userMarker) map.removeLayer(userMarker);
        if (radiusCircle) map.removeLayer(radiusCircle);

        // Fokus peta ke user
        map.setView([lat, lng], 14);

        // Marker User
        userMarker = L.marker([lat, lng], {icon: userIcon}).addTo(map)
            .bindPopup("<b>Posisi Anda</b>").openPopup();

        // Lingkaran Radius
        radiusCircle = L.circle([lat, lng], {
            color: '#4f46e5', // Indigo-600
            fillColor: '#6366f1',
            fillOpacity: 0.1,
            weight: 1,
            radius: radiusKm * 1000 // Konversi km ke meter
        }).addTo(map);
    }

    // 4. FETCH DATA DARI SERVER
    function fetchNearbyMerchants(lat, lng, radius) {
        const url = `{{ route('nearby.search') }}?lat=${lat}&lng=${lng}&radius=${radius}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const listContainer = document.getElementById('merchant-list');
                const resultsContainer = document.getElementById('results-container');
                const countSpan = document.getElementById('count');
                
                // Reset tampilan
                listContainer.innerHTML = '';
                markers.forEach(m => map.removeLayer(m));
                markers = [];

                if(data.merchants && data.merchants.length > 0) {
                    resultsContainer.classList.remove('hidden');
                    countSpan.innerText = data.merchants.length;
                    document.getElementById('status').textContent = `Menampilkan ${data.merchants.length} merchant dalam radius ${radius}km.`;

                    data.merchants.forEach(merchant => {
                        // A. Tambah Marker di Peta
                        const marker = L.marker([merchant.latitude, merchant.longitude], {icon: merchantIcon})
                            .addTo(map)
                            .bindPopup(`
                                <div class="text-center">
                                    <h3 class="font-bold text-sm">${merchant.name}</h3>
                                    <p class="text-xs mb-1">${parseFloat(merchant.distance).toFixed(2)} km</p>
                                    
                                    <a href="/orders/create?merchant_id=${merchant.id}" class="block bg-indigo-600 text-white text-xs px-2 py-1 rounded mt-1 hover:bg-indigo-700">
                                        Lihat Menu
                                    </a>
                                </div>
                            `);
                        markers.push(marker);

                        // B. Tambah Kartu di List Kiri
                        const distanceKm = parseFloat(merchant.distance).toFixed(2);
                        
                        // URL Gambar (Fallback ke Avatar jika null)
                        const imageUrl = merchant.image 
                            ? `/storage/${merchant.image}` 
                            : `https://ui-avatars.com/api/?name=${encodeURIComponent(merchant.name)}&background=random`;

                        const cardHtml = `
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-600 group cursor-pointer relative overflow-hidden" 
                                 onclick="focusOnMerchant(${merchant.latitude}, ${merchant.longitude})">
                                
                                <div class="flex gap-3 items-start">
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-md flex-shrink-0 overflow-hidden">
                                        <img src="${imageUrl}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                             onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <h4 class="font-bold text-gray-900 dark:text-white truncate pr-2 text-sm">${merchant.name}</h4>
                                            <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                ${distanceKm} km
                                            </span>
                                        </div>
                                        
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1">
                                            ${merchant.address ?? 'Alamat belum diatur'}
                                        </p>
                                        
                                        <div class="mt-3 flex justify-end">
                                            <a href="/orders/create?merchant_id=${merchant.id}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 flex items-center gap-1 transition-colors">
                                                Pesan Sekarang &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        listContainer.insertAdjacentHTML('beforeend', cardHtml);
                    });
                } else {
                    resultsContainer.classList.remove('hidden');
                    listContainer.innerHTML = `
                        <div class="text-center p-6 text-gray-500 flex flex-col items-center justify-center h-full">
                            <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm">Tidak ada merchant ditemukan di sekitar lokasi ini.</p>
                            <p class="text-xs mt-1">Coba perbesar radius pencarian.</p>
                        </div>
                    `;
                    document.getElementById('status').textContent = "Hasil nihil.";
                    countSpan.innerText = "0";
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('status').textContent = "Terjadi kesalahan server.";
            });
    }

    function focusOnMerchant(lat, lng) {
        map.flyTo([lat, lng], 16, {
            animate: true,
            duration: 1.5
        });
    }
</script>
@endsection
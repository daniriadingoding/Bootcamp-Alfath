@extends('layouts.app')

@section('content')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>

<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
            
            <div class="bg-indigo-600 px-6 py-4 border-b border-indigo-500">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <span>💳</span> Pembayaran Pesanan #{{ $order->id }}
                </h2>
            </div>

            <div class="p-8 text-center">
                
                {{-- Info Total --}}
                <div class="mb-8">
                    <p class="text-gray-500 dark:text-gray-400 mb-1">Total yang harus dibayar:</p>
                    <h3 class="text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </h3>
                </div>

                {{-- Instruksi --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-8 text-left border border-blue-100 dark:border-blue-800">
                    <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Petunjuk:</h4>
                    <ul class="list-disc list-inside text-sm text-blue-700 dark:text-blue-300 space-y-1">
                        <li>Klik tombol <strong>"Pilih Metode Pembayaran"</strong> di bawah.</li>
                        <li>Jendela popup Midtrans akan muncul.</li>
                        <li>Pilih metode (GoPay, ShopeePay, Transfer Bank, dll).</li>
                        <li>Selesaikan pembayaran sesuai instruksi di popup.</li>
                    </ul>
                </div>

                {{-- Tombol Bayar --}}
                <button id="pay-button" class="w-full sm:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-lg shadow-md transition transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                    Pilih Metode Pembayaran
                </button>

                <div class="mt-6">
                    <a href="{{ route('orders.show', $order) }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline">
                        Batalkan & Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    
    payButton.addEventListener('click', function () {
        // Memanggil Snap Popup dengan Token yang dikirim dari Controller
        window.snap.pay('{{ $snapToken }}', {
            // Callback Sukses (Redirect ke halaman sukses)
            onSuccess: function(result){
                console.log(result);
                window.location.href = "{{ route('payment.success', $order) }}";
            },
            // Callback Pending (Biasanya dianggap sukses menunggu konfirmasi)
            onPending: function(result){
                console.log(result);
                window.location.href = "{{ route('payment.success', $order) }}";
            },
            // Callback Error (Redirect ke halaman error/kembali)
            onError: function(result){
                console.log(result);
                alert("Pembayaran Gagal!");
                window.location.href = "{{ route('orders.show', $order) }}";
            },
            // Callback Close (Jika user menutup popup tanpa bayar)
            onClose: function(){
                console.log('customer closed the popup without finishing the payment');
                alert('Anda menutup halaman pembayaran sebelum menyelesaikannya.');
            }
        });
    });
</script>
@endsection
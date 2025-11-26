<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized', true);
        Config::$is3ds = config('services.midtrans.is_3ds', true);
    }

    public function createPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan sudah dibayar.');
        }

        try {
            $itemDetails = [];
            foreach ($order->items as $item) {
                $itemDetails[] = [
                    'id' => $item->foodMenu->id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => substr($item->foodMenu->name, 0, 50),
                ];
            }

            $transactionDetails = [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => (int) $order->total_price,
            ];

            $customerDetails = [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);

            $order->update([
                'payment_token' => $snapToken,
                'payment_status' => 'pending',
                'transaction_id' => $transactionDetails['order_id'],
            ]);

            return view('orders.payment', [
                'order' => $order,
                'snapToken' => $snapToken,
                'clientKey' => config('services.midtrans.client_key'),
            ]);

        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function success(Order $order)
    {
        if ($order->transaction_id) {
            try {
                $status = Transaction::status($order->transaction_id);
                $this->updateOrderFromTransactionStatus($order, $status);
            } catch (\Exception $e) {
                Log::error('Check status failed: ' . $e->getMessage());
            }
        }
        
        if ($order->payment_status == 'paid') {
             return redirect()->route('orders.show', $order)->with('success', 'Pembayaran Berhasil! Terima kasih.');
        }

        return redirect()->route('orders.show', $order)->with('info', 'Pembayaran sedang diproses.');
    }

    private function updateOrderFromTransactionStatus($order, $status)
    {
        $transactionStatus = $status->transaction_status;
        $fraudStatus = $status->fraud_status;

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->update(['payment_status' => 'pending']);
            } else {
                $order->update(['payment_status' => 'paid', 'status' => 'pending']);
            }
        } elseif ($transactionStatus == 'settlement') {
            $order->update(['payment_status' => 'paid', 'status' => 'pending']);
        } elseif ($transactionStatus == 'pending') {
            $order->update(['payment_status' => 'pending']);
        } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $order->update(['payment_status' => 'failed']);
        }
    }
}
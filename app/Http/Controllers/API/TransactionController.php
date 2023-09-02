<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PopularProduct;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client as TwillioSdk;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 12);
        $status = $request->input('status');

        if($id)
        {
            $transaction = Transaction::with(['items.product'])->find($id);

            if($transaction)
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            else
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
        }

        $transaction = Transaction::with(['items.product'])->orderBy('created_at', 'desc')->where('users_id', Auth::user()->id);

        if($status)
            $transaction->where('status', $status);

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaksi berhasil diambil'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'exists:products,id',
            'total_price' => 'required',
            'shipping_price' => 'required',
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED',
        ]);

        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'address' => $request->address,
            'total_price' => $request->total_price,
            'shipping_price' => $request->shipping_price,
            'status' => $request->status
        ]);

        if ($request->address != $request->user()->address) {
            User::where('id', $request->user()->id)->update([
                'address' => $request->address
            ]);
        }

        foreach ($request->items as $product) {
            TransactionItem::create([
                'users_id' => Auth::user()->id,
                'products_id' => $product['id'],
                'transactions_id' => $transaction->id,
                'quantity' => $product['quantity']
            ]);

            $popular = PopularProduct::where('products_id', $product['id'])->first();
            if ($popular) {
                $popular->update([
                    'volume' => DB::raw('volume+1')
                ]);
            } else {
                PopularProduct::create([
                    'products_id' => $product['id'],
                    'volume' => 1
                ]);
            }

        }

        $orderUrl = "http://kopicombi.my.id/dashboard/transaction/{$transaction->id}";
        // Note: twilio body tidak bisa menggunakan pesan dengan url localhost, pesan tidak akan terkirim
        $startFormText = '======= Form Pesanan =======';
        $endFormText = '============================';
        $total = number_format($transaction->total_price);
        $body = "Halo Mimin, Ada orderan baru nih!!\n\n$startFormText\n\nNama : {$request->user()->name}\nAlamat : {$transaction->address}\nInvoice : *{$transaction->invoice_number}*\nIDR *Rp.{$total}*\n\n$endFormText\n\nInfo detail : $orderUrl";
        $this->whatsappNotification($body);
        return ResponseFormatter::success($transaction->load('items.product'), 'Transaksi berhasil');
    }

    private function whatsappNotification(string $body)
    {
        $user    = config('services.twilio.sid');
        $pass  = config('services.twilio.token');
        $wa_from= config('services.twilio.whatsapp_from'); //from twilio account

        $recipient = config('services.twilio.whatsapp_recipient'); //nomor admin yg dituju
        $twilioHost = "https://api.twilio.com/2010-04-01/Accounts/{$user}/Messages.json";

        $data = array(
            "From" => "whatsapp:$wa_from",
            "To" => "whatsapp:$recipient",
            "Body" => $body
          );

        $response = Http::asForm()->withBasicAuth($user, $pass)->post($twilioHost, $data);
        return $response;
    }

    // cheat sheet command line
    // \" – double quote
    // \\ – single backslash
    // \a – bell/alert
    // \b – backspace
    // \r – carriage return
    // \n – newline
    // \s – space
    // \t – tab

    // private function whatsappNotification(string $body)
    // {
    //     $sid    = config('services.twilio.sid');
    //     $token  = config('services.twilio.token');
    //     $wa_from= config('services.twilio.whatsapp_from');
    //     $twilio = new TwillioSdk($sid, $token);
    //     $recipient = '+6282244215577'; //nomor admin yg dituju
    //     return $twilio->messages->create("whatsapp:$recipient",["from" => "whatsapp:$wa_from", "body" => $body]);
    // }
}

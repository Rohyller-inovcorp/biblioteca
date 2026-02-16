<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
class CheckAbandonedOrders extends Command
{
    protected $signature = 'orders:check-abandoned {--test}';
    protected $description = 'Envia email para encomendas pendentes há mais de 1h';

    public function handle()
    {
        $minutes = $this->option('test') ? 2 : 60;

        $limit = Carbon::now()->subMinutes($minutes);

        $orders = Order::where('status', 'pending')
            ->where('notified', false)
            ->where('updated_at', '<=', $limit)
            ->with('user')
            ->get();

        foreach ($orders as $order) {
            Mail::to($order->user->email)->send(new \App\Mail\AbandonedOrderMail($order));

            $order->update(['notified' => true]);
        }

        $this->info("Emails enviados: " . $orders->count());
    }
}

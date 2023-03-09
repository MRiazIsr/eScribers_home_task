<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GenerateDailyReport extends Command
{
    protected $signature = 'report:daily';
    protected $description = 'Generate and email the daily purchase report';

    public function handle()
    {
        $today = date('Y-m-d');

        $orders = DB::table('orders')
            ->whereDate('created_at', $today)
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('products', 'products.id', '=', 'order.product_id')
            ->select(
                'orders.created_at as date',
                'products.name as model',
                'order_product.quantity as amount',
                'users.name as customer',
                DB::raw('SUM(order_product.quantity * products.price) as total_price')
            )
            ->groupBy('orders.created_at', 'products.name', 'order_product.quantity', 'users.name')
            ->orderByDesc('total_price')
            ->get();

        $csv = \League\Csv\Writer::createFromString('');
        $csv->insert(['Date', 'Model', 'Amount', 'Customer', 'Total Price']);
        foreach ($orders as $order) {
            $csv->insert([$order->date, $order->model, $order->amount, $order->customer, $order->total_price]);
        }

        $filename = 'daily_report_' . $today . '.csv';
        $filepath = storage_path('app/' . $filename);
        file_put_contents($filepath, $csv->getContent());

        Mail::send([], [], function ($message) use ($filepath) {
            $message->to('website-owners@example.com')
                    ->subject('Daily Purchase Report')
                    ->attach($filepath);
        });

        $this->info('Daily purchase report generated and emailed successfully');
    }
}

<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = (int) ($argv[1] ?? 0);
if ($id <= 0) {
    echo "Usage: php inspect_order.php ID\n";
    exit(1);
}

$order = App\Models\Order::find($id);
if (! $order) {
    echo "Order not found\n";
    exit(1);
}

echo json_encode($order->only([
    'id', 'payment_method', 'payment_status', 'fizetve', 'barion_payment_id', 'total_price', 'created_at',
]), JSON_PRETTY_PRINT).PHP_EOL;

echo 'payments_count='.App\Models\Payment::where('order_id', $id)->count().PHP_EOL;

$cols = Illuminate\Support\Facades\Schema::getColumnListing('orders');
echo 'has_fizetve='.(in_array('fizetve', $cols, true) ? 'yes' : 'no').PHP_EOL;

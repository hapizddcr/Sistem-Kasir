<?php

return [
    'name' => env('APP_NAME', 'Sistem Kasir'),

    'tax_percent' => (float) env('POS_TAX_PERCENT', 11),

    'points_per_amount' => (int) env('POS_POINTS_PER_AMOUNT', 1000),

    'point_value' => (int) env('POS_POINT_VALUE', 100),

    'low_stock_threshold' => (int) env('POS_LOW_STOCK_THRESHOLD', 10),

    'payment_methods' => [
        'cash' => 'Tunai',
        'qris' => 'QRIS',
        'transfer' => 'Transfer Bank',
        'card' => 'Kartu Debit/Kredit',
    ],
];

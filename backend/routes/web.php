<?php

use App\Mail\IncomingOrderDeadlineMail;
use App\Mail\OrderCompletedMail;
use App\Mail\OrderRequestCreatedMail;
use App\Models\Order;
use App\Models\OrderRequest;
use Illuminate\Support\Facades\Route;

// Route::get('/api/mail-preview/{template}', function (string $template) {
//     $order = Order::latest()->firstOrFail();
//     $orderRequest = OrderRequest::latest()->firstOrFail();

//     $mailable = match ($template) {
//         'order-completed' => new OrderCompletedMail($order),
//         'incoming-deadline' => new IncomingOrderDeadlineMail($order),
//         'order-request-created' => new OrderRequestCreatedMail($orderRequest),
//         default => abort(404),
//     };

//     return $mailable;
// });

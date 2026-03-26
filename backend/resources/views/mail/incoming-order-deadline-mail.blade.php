<x-mail::message>
# {{ __('notifications.orderIncomingDeadlineTitle') }}

{{ __('notifications.orderIncomingDeadlineMessage', ['orderId' => $order->id, 'date' => $date]) }}

*{{ config('app.name') }}*
</x-mail::message>

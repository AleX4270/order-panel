<x-mail::message>
# {{ __('notifications.orderCompletedTitle') }}

{{ __('notifications.orderCompletedMessage', ['orderId' => $order->id]) }}

*{{ config('app.name') }}*
</x-mail::message>

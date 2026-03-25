<?php
declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyIncomingOrderDeadlines implements ShouldQueue {
    use Queueable;

    public function __construct() {}

    public function handle(): void {
        
    }
}

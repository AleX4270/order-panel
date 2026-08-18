<?php
declare(strict_types=1);

use App\Console\Commands\SendIncomingDeadlineNotifications;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendIncomingDeadlineNotifications::class)->dailyAt('5:00');

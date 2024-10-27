<?php

use App\Jobs;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new Jobs\Boc\DownloadRobots)->dailyAt('00:00');
Schedule::job(new Jobs\Boc\DownloadArchive)->dailyAt('00:05');
Schedule::job(new Jobs\TakeSnapshot)->dailyAt('06:00');
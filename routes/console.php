<?php

use App\Jobs\Boc;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new Boc\DownloadRobots)->dailyAt('00:00');
Schedule::job(new Boc\DownloadArchive)->dailyAt('00:05');

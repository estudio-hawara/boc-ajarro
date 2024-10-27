<?php

use App\Jobs;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new Jobs\Boc\DownloadRobots)->dailyAt('00:00');
Schedule::job(new Jobs\Boc\DownloadArchive)->dailyAt('00:05');
Schedule::job(new Jobs\Boc\FollowLinksFoundInArchive)->dailyAt('00:10');
Schedule::job(new Jobs\Boc\FollowLinksFoundInYearIndex)->hourly();
Schedule::job(new Jobs\Boc\FollowLinksFoundInBulletinIndex)->everyFiveMinutes();
Schedule::job(new Jobs\TakeSnapshot)->dailyAt('06:00');
Schedule::command('horizon:snapshot')->everyFiveMinutes();
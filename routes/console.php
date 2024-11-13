<?php

use App\Jobs;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new Jobs\Boc\DownloadRobots)->dailyAt('00:00');
Schedule::job(new Jobs\Boc\DownloadArchive)->dailyAt('00:05');
Schedule::job(new Jobs\Boc\DownloadYearIndex(App\Models\Link::find(45)))->dailyAt('00:10');
Schedule::job(new Jobs\Boc\ExtractLinksFromYearIndex(App\Models\Page::find(176348), recreate: true))->everyMinute();
Schedule::job(new Jobs\Boc\FollowLinksFoundInArchive)->hourly();
Schedule::job(new Jobs\Boc\FollowLinksFoundInYearIndex)->hourly();
Schedule::job(new Jobs\Boc\FollowLinksFoundInBulletinIndex)->hourly();
Schedule::job(new Jobs\TakeSnapshot)->everyFiveMinutes();
Schedule::command('horizon:snapshot')->everyFiveMinutes();

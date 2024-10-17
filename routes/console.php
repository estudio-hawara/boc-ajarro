<?php

use App\Jobs\Boc\DownloadArchives;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DownloadArchives)->daily();

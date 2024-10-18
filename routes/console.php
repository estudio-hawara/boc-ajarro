<?php

use App\Jobs\Boc\DownloadArchive;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DownloadArchive)->daily();

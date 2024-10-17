<?php

use App\Jobs\DownloadPage;
use Illuminate\Support\Facades\Schedule;
 
Schedule::job(
    new DownloadPage('https://www.gobiernodecanarias.org/boc/archivo/')
)->daily();
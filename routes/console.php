<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Scedule::command('remove:vend-data-more-than-seven-days')->daily();

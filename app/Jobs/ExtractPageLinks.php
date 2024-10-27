<?php

namespace App\Jobs;

use App\Jobs\Traits\ExtractsLinks;
use App\Jobs\Traits\FiltersLinks;
use App\Models\Page;

class ExtractPageLinks extends AbstractJob
{
    use ExtractsLinks;
    use FiltersLinks;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Page $page,
        protected string $root,
        protected bool $recreate = false,
    ) {
        //
    }
}

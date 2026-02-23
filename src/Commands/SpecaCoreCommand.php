<?php

namespace Speca\SpecaCore\Commands;

use Illuminate\Console\Command;

class SpecaCoreCommand extends Command
{
    public $signature = 'skeleton';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

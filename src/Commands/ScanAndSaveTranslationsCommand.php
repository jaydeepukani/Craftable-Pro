<?php

namespace Brackets\CraftablePro\Commands;

use Brackets\CraftablePro\Translations\TranslationsProcessor;
use Illuminate\Console\Command;

class ScanAndSaveTranslationsCommand extends Command
{
    public $signature = 'craftable-pro:scan-translations';

    public $description = 'Scan translations';

    public function __construct(private TranslationsProcessor $processor)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->processor->scanTranslations();
    }
}

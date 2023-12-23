<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:download:sde')]
class DownloadSDE extends Command
{
    protected $signature = 'app:download:sde';

    protected $description = 'Downloads the latest SDE and extracts it to the application directory';

    public function handle(): int
    {
        ini_set('memory_limit', '-1');
        $path = Storage::disk('database')->path('sde.sqlite.bz2');

        //TODO: Check if the SDE is already downloaded and the E-Tag matches
        if (!Storage::disk('database')->exists('sde.sqlite')) {
            Http::send('GET', 'https://www.fuzzwork.co.uk/dump/sqlite-latest.sqlite.bz2', ['sink' => $path]);

            $sde = bzopen($path, 'r');

            $sqlite = '';

            while (!feof($sde)) {
                $sqlite .= bzread($sde, 4096);
            }

            Storage::disk('database')->put('sde.sqlite', $sqlite);
        }

        DB::connection('sde')->select('SELECT 1');

        return Command::SUCCESS;
    }
}

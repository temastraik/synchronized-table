<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;

class SyncGoogleSheets extends Command
{
    protected $signature = 'google-sheet:sync';
    protected $description = 'Sync database with Google Sheets';

    public function handle(GoogleSheetService $sheetService)
    {
        $this->info('Starting Google Sheets synchronization...');
        
        if ($sheetService->syncData()) {
            $this->info('Synchronization completed successfully.');
        } else {
            $this->error('Synchronization failed.');
        }
        
        return 0;
    }
}

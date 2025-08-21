<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetService;
use Illuminate\Support\Facades\Log;

class FetchGoogleSheetComments extends Command
{
    protected $signature = 'google-sheet:fetch-comments {--count= : Limit number of rows}';
    protected $description = 'Fetch comments from Google Sheet with progress bar';

    public function handle(GoogleSheetService $sheetService)
    {
        $count = $this->option('count') ? (int)$this->option('count') : null;
        
        try {
            $commentsData = $sheetService->getCommentsWithProgress($count);
            
            if (empty($commentsData)) {
                $this->info('No data found in Google Sheet.');
                return 0;
            }
            
            $this->info('Fetching comments from Google Sheet...');
            
            $progressBar = $this->output->createProgressBar(count($commentsData));
            $progressBar->start();
            
            $results = [];
            foreach ($commentsData as $data) {
                $results[] = [
                    'ID' => $data['id'],
                    'Comment' => $data['comment']
                ];
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            $this->table(['ID', 'Comment'], $results);
            
            $this->info('Successfully fetched ' . count($results) . ' comments.');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error fetching data: ' . $e->getMessage());
            Log::error('Command error: ' . $e->getMessage());
            return 1;
        }
    }
}
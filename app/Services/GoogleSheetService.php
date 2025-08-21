<?php

namespace App\Services;

use App\Models\TextItem;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleSheetService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;
    protected $sheetName = 'Sheet1';

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Laravel Google Sheets');
        $this->client->setScopes([Sheets::SPREADSHEETS]);
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->setAccessType('offline');

        $this->service = new Sheets($this->client);
        $this->spreadsheetId = config('services.google.sheet_id');
    }

    public function setSpreadsheetId($spreadsheetId)
    {
        $this->spreadsheetId = $spreadsheetId;
    }

    public function extractSheetIdFromUrl($url)
    {
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    public function syncData()
    {
        if (!$this->spreadsheetId) {
            Log::warning('Google Sheet ID not configured');
            return false;
        }

        try {
            // Получаем данные из БД
            $items = TextItem::allowed()->get();
            
            // Получаем текущие данные из Google Sheets
            $currentData = $this->getSheetData();
            $comments = $this->extractComments($currentData);
            
            // Подготавливаем данные для отправки
            $data = [];
            $headers = ['ID', 'Title', 'Content', 'Status', 'Created At', 'Updated At'];
            $data[] = $headers;
            
            foreach ($items as $item) {
                $comment = $comments[$item->id] ?? '';
                $data[] = [
                    $item->id,
                    $item->title,
                    $item->content,
                    $item->status,
                    $item->created_at->toDateTimeString(),
                    $item->updated_at->toDateTimeString(),
                    $comment, // Комментарий в дополнительном столбце
                ];
            }
            
            // Обновляем данные в таблице
            $range = $this->sheetName . '!A1:G' . (count($data) + 1);
            $valueRange = new ValueRange();
            $valueRange->setValues($data);
            $valueRange->setMajorDimension('ROWS');
            
            $params = ['valueInputOption' => 'USER_ENTERED'];
            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $valueRange,
                $params
            );
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Google Sheets sync error: ' . $e->getMessage());
            return false;
        }
    }

    public function getSheetData()
    {
        try {
            $range = $this->sheetName . '!A1:G';
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
            return $response->getValues() ?? [];
        } catch (\Exception $e) {
            Log::error('Error fetching Google Sheets data: ' . $e->getMessage());
            return [];
        }
    }

    protected function extractComments($sheetData)
    {
        $comments = [];
        
        if (count($sheetData) > 1) {
            foreach (array_slice($sheetData, 1) as $row) {
                if (count($row) >= 7) { // 6 столбцов данных + комментарий
                    $id = $row[0] ?? null;
                    $comment = $row[6] ?? '';
                    if ($id) {
                        $comments[$id] = $comment;
                    }
                }
            }
        }
        
        return $comments;
    }

    public function getCommentsWithProgress($limit = null)
    {
        $data = $this->getSheetData();
        $comments = [];
        
        if (count($data) > 1) {
            $rows = array_slice($data, 1);
            if ($limit) {
                $rows = array_slice($rows, 0, $limit);
            }
            
            $total = count($rows);
            $current = 0;
            
            foreach ($rows as $row) {
                $current++;
                if (count($row) >= 7) {
                    $id = $row[0] ?? 'N/A';
                    $comment = $row[6] ?? 'No comment';
                    $comments[] = [
                        'id' => $id,
                        'comment' => $comment,
                        'progress' => ($current / $total) * 100
                    ];
                }
            }
        }
        
        return $comments;
    }
}
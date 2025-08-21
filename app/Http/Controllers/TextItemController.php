<?php
// app/Http/Controllers/TextItemController.php

namespace App\Http\Controllers;

use App\Models\TextItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TextItemController extends Controller
{
    public function index()
    {
        $items = TextItem::latest()->paginate(20);
        $googleSheetUrl = config('services.google.sheet_url');
        
        return view('text-items.index', compact('items', 'googleSheetUrl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:Allowed,Prohibited',
        ]);

        TextItem::create($request->all());

        return redirect()->route('text-items.index')
            ->with('success', 'Item created successfully.');
    }

    public function update(Request $request, TextItem $textItem)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:Allowed,Prohibited',
        ]);

        $textItem->update($request->all());

        return redirect()->route('text-items.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(TextItem $textItem)
    {
        $textItem->delete();

        return redirect()->route('text-items.index')
            ->with('success', 'Item deleted successfully.');
    }

    public function generateRandom()
    {
        $statuses = ['Allowed', 'Prohibited'];
        
        for ($i = 0; $i < 1000; $i++) {
            TextItem::create([
                'title' => 'Random Item ' . ($i + 1),
                'content' => 'This is randomly generated content for item ' . ($i + 1),
                'status' => $statuses[array_rand($statuses)],
            ]);
        }

        return redirect()->route('text-items.index')
            ->with('success', '1000 random items generated successfully.');
    }

    public function clearAll()
    {
        TextItem::truncate();

        return redirect()->route('text-items.index')
            ->with('success', 'All items cleared successfully.');
    }

    public function updateGoogleSheetUrl(Request $request)
    {
        $request->validate([
            'google_sheet_url' => 'required|url',
        ]);

        config(['services.google.sheet_url' => $request->google_sheet_url]);

        return redirect()->route('text-items.index')
            ->with('success', 'Google Sheet URL updated successfully.');
    }

    public function fetchData($count = null)
    {
        $exitCode = Artisan::call('google-sheet:fetch-comments', [
            '--count' => $count,
        ]);

        $output = Artisan::output();

        return response($output, 200)
            ->header('Content-Type', 'text/plain');
    }
}
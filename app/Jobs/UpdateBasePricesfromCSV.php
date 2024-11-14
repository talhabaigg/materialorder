<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use App\Models\ItemBase;
use Illuminate\Support\Str;
use App\Models\MaterialItem;
use App\Models\ItemBasePrice;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;

class UpdateBasePricesfromCSV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting UpdateBasePricesfromCSV job with file: {$this->fileName}");
        set_time_limit(0);
        
        $path = storage_path("app/public/{$this->fileName}");

        if (!file_exists($path)) {
            Log::warning("CSV file does not exist at the specified path: {$path}");
            return;
        }

        $csvData = array_map('str_getcsv', file($path));
        $currentDate = now()->startOfDay();
        $basePriceList = ItemBase::where('effective_from', '<=', $currentDate)
            ->where(function ($query) use ($currentDate) {
                $query->where('effective_to', '>=', $currentDate)
                      ->orWhereNull('effective_to');
            })
            ->first();

        if (!$basePriceList) {
            Log::warning('No active base price list found for the current date.');
            return;
        }

        $baseId = $basePriceList->id;
        $insertData = [];

        foreach ($csvData as $index => $row) {
            if ($index === 0 || count($row) < 3) {
                continue;
            }

            $itemCode = Str::trim($row[0]);
            $priceFromCsv = (float) $row[2];

            $materialItem = MaterialItem::where('code', $itemCode)->first();

            if ($materialItem && $priceFromCsv > 0) {
                $insertData[] = [
                    'item_base_id' => $baseId,
                    'material_item_id' => $materialItem->id,
                    'price' => $priceFromCsv,
                ];
            } else {
                Log::warning("Material item not found or invalid price for item code: {$itemCode}");
            }
        }

        if (!empty($insertData)) {
            foreach ($insertData as $data) {
                ItemBasePrice::updateOrCreate(
                    [
                        'item_base_id' => $data['item_base_id'],
                        'material_item_id' => $data['material_item_id'],
                    ],
                    [
                        'price' => $data['price'],
                    ]
                );
            }
            Log::info('Base prices updated successfully.');
        } else {
            Log::warning('No valid base prices to update.');
        }
    }
}

<?php

namespace App\Imports;

use App\Models\Feature;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log; 

class FeaturesImport implements ToModel, WithHeadingRow
{

    protected $cat_id;
    protected $brand_id;

    public function __construct($cat_id, $brand_id)
    {
        $this->cat_id = $cat_id;
        $this->brand_id = $brand_id;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Known columns
            $knownColumns = ['make', 'brand', 'model', 'modle', 'vessel_name', 'label_name', 'dog_breed_group'];
    
            // Filter out known columns to get the dynamic/extra fields
            $otherFeatures = array_diff_key($row, array_flip($knownColumns));
       
            return new Feature([
                'brand_id' => $this->brand_id,
                'cat_id' => $this->cat_id,
                'make' => $row['make'] ?? $row['brand'] ?? $row['vessel_name'] ?? null,
                'model' => $row['modle'] ?? $row['model'] ?? null,
                'label_name' => $row['label_name'] ?? null,
                'dog_breed_group' => $row['dog_breed_group'] ?? null,
                'other_features' => json_encode($otherFeatures), // Store extra fields as JSON
            ]);
    
        } catch (\Exception $e) {
            // Log the error message and the row that caused the error
            Log::error('Import error: ' . $e->getMessage(), [
                'row' => $row, // Log the row data that caused the exception
                'exception' => $e // Log the full exception for more details
            ]);
        }
    
        return null;
    }
}

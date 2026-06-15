<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblCustomField;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use Illuminate\Support\Facades\Log; // 👈 IMPORT ZAROORI HAI

class CustomFieldController extends Controller
{
    public function getCustomFields(Request $request, $id)
    {
        // DEBUG START: Request Data Print karein
        Log::info("🔥 API HIT: getCustomFields for Category ID: " . $id);
        Log::info("📥 Request Params: ", $request->all());

        $category_id = $id;
        $cfld = TblCustomField::where('cat_id', $category_id)->first();

        if (!$cfld || $cfld->field_count == 0) {
            return response()->json(['success' => true, 'data' => ['select' => [], 'text' => [], 'textarea' => [], 'checkbox' => [], 'radio' => [], 'number' => []]]);
        }

        $fields = TblFieldsDetail::where('cat_id', $category_id)
            ->where('active', '1')
            ->get();

        $response = [
            'select' => [], 'text' => [], 'textarea' => [], 'checkbox' => [], 'radio' => [], 'number' => []
        ];

        foreach ($fields as $field) {
            // Make ko Brand show karna
            $displayLabel = ($field->name === 'Make') ? 'Brand' : $field->name;

            $fieldData = [
                'label' => $displayLabel,
                'name' => $field->id . '_' . $field->form_field_name,
                'passing_label' => $field->form_field_name,
                'required' => (string)$field->required,
                'options' => []
            ];

            // ---------------------------------------------------------
            // CASE A: BRAND FIELD
            // ---------------------------------------------------------
            if ($field->form_field_name === 'brandwithmodel' || $field->form_field_name === 'make') {
                
                $options = TblFieldsOption::where('cat_id', $category_id)
                    ->where('form_field_name', $field->form_field_name)
                    ->where('active', '1')
                    ->orderBy('key', 'asc')
                    ->get();

                foreach ($options as $opt) {
                    $fieldData['options'][] = [
                        'label' => $opt->key,
                        'value' => $opt->id,
                        'key'   => $opt->key,
                        'icon'  => $opt->image ?? '' 
                    ];
                }
                $response['select'][] = $fieldData;

                // ---------------------------------------------------------
                // CASE B: MODEL FIELD INJECTION (DEBUGGING ADDED)
                // ---------------------------------------------------------
                $modelFieldData = [
                    'label' => 'Model',
                    'name' => $field->id . '_brandswithmodels',
                    'passing_label' => 'models',
                    'required' => (string)$field->required,
                    'type' => 'select',
                    'options' => []
                ];

                // 1. Expected Key banayein
                $expectedKey = $field->id . '_' . $field->form_field_name;
                
                Log::info("🧐 Looking for Param Key: " . $expectedKey);

                // 2. Check karein Request me ye Key hai ya nahi
                if ($request->has($expectedKey)) {
                    $brandId = $request->input($expectedKey);
                    Log::info("✅ Brand ID Found in Request: " . $brandId);

                    // 3. Database se Brand Option nikalein
                    $brandOption = TblFieldsOption::find($brandId);

                    if ($brandOption) {
                        Log::info("📦 Brand Found in DB: " . $brandOption->key);
                        Log::info("📄 Raw Models Value: '" . $brandOption->value . "'");

                        if (!empty($brandOption->value)) {
                            $rawModels = $brandOption->value;
                            $modelsList = [];

                            // Parsing Logic
                            if (substr($rawModels, 0, 1) === '[' && substr($rawModels, -1) === ']') {
                                $modelsArray = json_decode($rawModels, true);
                                Log::info("🔹 Parsed as JSON");
                            } else {
                                $modelsArray = array_map('trim', explode(',', $rawModels));
                                Log::info("🔹 Parsed as Comma Separated");
                            }

                            if (is_array($modelsArray)) {
                                foreach ($modelsArray as $modelName) {
                                    if (!empty($modelName)) {
                                        $modelsList[] = [
                                            'label' => $modelName,
                                            'value' => $modelName,
                                            'key'   => $modelName
                                        ];
                                    }
                                }
                            }
                            $modelFieldData['options'] = $modelsList;
                            Log::info("✅ Total Models Added: " . count($modelsList));
                        } else {
                            Log::info("⚠️ Brand Value (Models) is EMPTY in DB.");
                        }
                    } else {
                        Log::info("❌ Brand ID ($brandId) not found in tbl_fields_options table.");
                    }
                } else {
                    Log::info("❌ Request does not contain key: " . $expectedKey);
                    Log::info("⚠️ Available Keys in Request: ", $request->keys());
                }

                $response['select'][] = $modelFieldData;
            }
            // Normal Fields
            elseif (in_array($field->type, ['select', 'radio-group', 'checkbox-group'])) {
                $options = TblFieldsOption::where('cat_id', $category_id)
                    ->where('form_field_name', $field->form_field_name)
                    ->where('active', '1')
                    ->get();

                foreach ($options as $opt) {
                    $fieldData['options'][] = [
                        'label' => $opt->key,
                        'value' => $opt->value ?: $opt->key,
                        'key'   => $opt->key
                    ];
                }
                if ($field->type == 'select') $response['select'][] = $fieldData;
                if ($field->type == 'radio-group') $response['radio'][] = $fieldData;
                if ($field->type == 'checkbox-group') $response['checkbox'][] = $fieldData;
            } else {
                if ($field->type == 'text') $response['text'][] = $fieldData;
                if ($field->type == 'number') $response['number'][] = $fieldData;
                if ($field->type == 'textarea') $response['textarea'][] = $fieldData;
            }
        }

        return response()->json(['success' => true, 'data' => $response]);
    }
}
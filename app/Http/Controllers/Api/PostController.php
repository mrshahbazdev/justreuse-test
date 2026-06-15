<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;

// Models
use App\Models\TblCategory;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\Package;
use App\Models\TblCountry;
use App\Models\TblState;
use App\Models\TblCity;
use App\Models\TblPaymentsMethod;
use App\Models\TblCurrency;
use App\Models\TblPostedAdPackageInfo;
use App\Models\Setting;
use App\Models\TblFieldsDetail;

class PostController extends Controller
{
    // ==================================================
    // 1. GET INITIAL DATA (Categories, Packages, etc.)
    // ==================================================
    public function getCreateData()
    {
        $parentCategories = TblCategory::whereNull('parent_id')->orderBy('list_order', 'asc')->get();
        $packages = Package::get_active_packages();
        $currencies = TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
        $paymentMethods = TblPaymentsMethod::where('active', '1')->get();
        
        // Settings se default currency nikalna
        $settings = Setting::get_logos();
        $defaultCurrency = $settings['default_currency'] ?? 1;

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $parentCategories,
                'packages' => $packages,
                'currencies' => $currencies,
                'payment_methods' => $paymentMethods,
                'default_currency' => $defaultCurrency
            ]
        ]);
    }

    // ==================================================
    // 2. GET SUB CATEGORIES
    // ==================================================
    public function getSubCategories($id)
    {
        $childCategories = TblCategory::where('parent_id', $id)
            ->orderBy('list_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $childCategories
        ]);
    }

    // ==================================================
    // 3. UPLOAD IMAGE (Call this one by one for images)
    // ==================================================
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:10240', // 10MB Max
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $image = $request->file('image');
            $watermarkPath = public_path('storage/watermark.png');
            $watermarkExists = file_exists($watermarkPath);

            $img = Image::make($image->getRealPath());

            // --- Watermark Logic (Same as Livewire) ---
            if ($watermarkExists) {
                $watermark = Image::make($watermarkPath);
                $watermarkWidth = intval($img->width() * 0.15);
                if ($watermarkWidth < 80) $watermarkWidth = 80;

                $watermark->widen($watermarkWidth, function ($constraint) {
                    $constraint->upsize();
                });
                $watermark->opacity(70);
                $img->insert($watermark, 'top-right', 20, 20);
            }

            $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
            $fullPath = 'adpost/predefined/' . $filename;
            
            // Save to Public Disk
            Storage::disk('public')->put($fullPath, (string) $img->encode());

            return response()->json([
                'success' => true,
                'path' => $fullPath, // Frontend is path ko array me save karega
                'url' => asset('storage/' . $fullPath)
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Image upload failed: ' . $e->getMessage()], 500);
        }
    }

    // ==================================================
    // 4. STORE POST (Final Submission)
    // ==================================================
    public function store(Request $request)
    {
        // Determine user: authenticated user OR user_id from request
        $userId = Auth::id();
        if (!$userId && $request->has('user_id')) {
            $userId = $request->user_id;
        }
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'user_id is required when not authenticated.'], 422);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'category_id' => 'required|exists:tbl_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
            'package_id' => 'required|exists:packages,id',
            'currency_id' => 'required',
            'country_short' => 'required',
            'country_long' => 'required',
            'state_short' => 'required',
            'state_long' => 'required',
            'city_name' => 'required',
            'main_city_name' => 'required',
            'uploaded_images' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // Location Logic
            $locationData = $this->processLocationData($request);

            // Package Logic
            $package = Package::find($request->package_id);
            $active_status = ($package && $package->short_name == 'free') ? 1 : 0;
            
            $slug = Str::slug($request->title, "-") . '-' . (TblPost::count() + 1);
            $imagesString = implode(',', $request->uploaded_images);

            // Create Post
            $post = TblPost::create([
                'user_id' => $userId,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'slug' => $slug,
                'city' => $locationData['city_id'],
                'locality' => $locationData['cityNames'],
                'images' => $imagesString,
                'currency_id' => $request->currency_id,
                'active' => $active_status,
                'product_condition' => $request->product_condition ?? 1,
                'exchange_to_buy' => $request->exchange_to_buy ?? 0,
                'fixed_price' => $request->fixed_price ?? 0,
                'instant_buy' => $request->instant_buy ?? 0,
                'video_url' => $request->video_url ?? '',
                'shipping_rate' => $request->shipping_rate ?? 0
            ]);

            // 4.5 Save Custom Fields
            // Frontend 'custom_fields' key me JSON object bhejega: { "12_brandwithmodel": "UUID", "15_color": "Red" }
            if ($request->has('custom_fields')) {
                $this->saveCustomFields($post->id, $request->input('custom_fields'));
            }

            // 4.6 Handle Package Info (Free)
            if ($active_status == 1) {
                $this->handlePackage($post->id, $package, $userId);
                return response()->json([
                    'success' => true, 
                    'message' => 'Post created successfully!',
                    'type' => 'success'
                ]);
            }

            // 4.7 Handle Payment (Paid)
            if ($active_status == 0) {
                // Return data needed for Payment Gateway Screen
                return response()->json([
                    'success' => true,
                    'type' => 'payment',
                    'post_id' => $post->id,
                    'amount' => $package->price,
                    'package_id' => $package->id,
                    'message' => 'Post created. Payment required.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================== HELPER FUNCTIONS ====================

    private function processLocationData($request)
    {
        // Country
        $country = TblCountry::firstOrCreate(
            ['code' => $request->country_short],
            ['name' => $request->country_long]
        );

        // State
        $state = TblState::firstOrCreate(
            ['country_id' => $country->id, 'code' => $request->state_short],
            ['name' => $request->state_long]
        );

        // City
        $city = TblCity::firstOrCreate(
            [
                'country_id' => $country->id, 
                'state_id' => $state->id, 
                'name' => $request->main_city_name,
                'locality' => $request->city_name
            ],
            [
                'latitude' => $request->latitude ?? '',
                'logitude' => $request->longitude ?? '' // Typo in original DB (logitude)
            ]
        );

        return [
            'city_id' => $city->id,
            'cityNames' => $request->city_name . "," . $request->main_city_name
        ];
    }

    private function saveCustomFields($post_id, $customFieldsData)
    {
        $brandModelData = [];

        // Note: Mobile app se data array/object format me aana chahiye
        foreach ($customFieldsData as $key => $value) {
            
            // Key format expected: "12_fieldname"
            $parts = explode('_', $key, 2);
            if(count($parts) < 2) continue;

            $field_id = $parts[0];
            $field_slug = $parts[1];

            // A. Brand Handle
            if ($field_slug === 'brandwithmodel') {
                $brandModelData[$field_id]['brand'] = $value;
                continue;
            }
            // B. Model Handle
            if ($field_slug === 'brandswithmodels') {
                // Hamein parent brand ID chahiye taake uske sath merge karein
                // Usually field_id same hota hai ya app logic handle karti hai.
                // Assuming field_id is same as Brand field id from Frontend logic
                $brandModelData[$field_id]['model'] = $value;
                continue;
            }

            // C. Normal Fields
            TblPostValue::create([
                'post_id' => $post_id,
                'field_id' => $field_id,
                'value' => is_array($value) ? implode(',', $value) : ($value ?? '')
            ]);
        }

        // D. Save Combined Brand,Model
        foreach($brandModelData as $f_id => $data) {
            $brand = $data['brand'] ?? '';
            $model = $data['model'] ?? '';
            
            if($brand) {
                TblPostValue::create([
                    'post_id' => $post_id,
                    'field_id' => $f_id,
                    'value' => $brand . ',' . $model
                ]);
            }
        }
    }

    private function handlePackage($post_id, $package, $userId = null)
    {
        $curr_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime($curr_date . "+" . $package->duration . " days"));

        TblPostedAdPackageInfo::create([
            'user_id' => $userId ?? Auth::id(),
            'post_id' => $post_id,
            'ad_type' => 'free',
            'start_date' => $curr_date,
            'end_date' => $end_date,
            'active' => '1'
        ]);
    }
}
<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\TblCategory;
use App\Models\TblCustomField;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblCurrency;
use App\Models\TblPaymentsMethod;
use App\Models\Package;
use App\Models\Setting;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\TblCountry;
use App\Models\TblState;
use App\Models\TblCity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

class PostComponentEdit extends Component
{
    use WithFileUploads;

    public TblPost $post;

    public $currentStep = 1;
    public $selectedParentCategory;
    public $selectedChildCategory;
    public $childCategories = [];
    
    public $title;
    public $price;
    public $currency_id;
    public $description;
    public $video_url;
    public $product_condition;
    public $exchangeToBuy;
    public $FixedPrice;
    public $InstantBuy;
    public $shipping_fee;
    
    public $location;
    public $text_city_sst;
    public $text_country_sst;
    
    public $customFields = [];
    public $customFieldsData = [];
    public $dynamicModels = [];

    public $images = []; // For new uploads
    public $existingImages = []; // For displaying existing images
    public $imagesToDelete = []; // To track removed existing images

    public function mount($postId)
    {
        $this->post = TblPost::findOrFail($postId);

        if ($this->post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->title = $this->post->title;
        $this->price = $this->post->price;
        $this->currency_id = $this->post->currency_id;
        $this->description = $this->post->description;
        $this->video_url = $this->post->video_url;
        $this->product_condition = $this->post->product_condition;
        $this->exchangeToBuy = $this->post->exchange_to_buy;
        $this->FixedPrice = $this->post->fixed_price;
        $this->InstantBuy = $this->post->instant_buy;
        $this->shipping_fee = $this->post->shipping_rate;
        
        $this->location = $this->post->locality;
        $city = TblCity::find($this->post->city);
        if ($city) {
            $this->text_city_sst = $city->name;
            $country = TblCountry::find($city->country_id);
            if ($country) {
                $this->text_country_sst = $country->name;
            }
        }

        $childCategory = TblCategory::find($this->post->category_id);
        if ($childCategory) {
            $this->selectedChildCategory = $childCategory->id;
            $this->selectedParentCategory = $childCategory->parent_id;
            if($this->selectedParentCategory) {
                $this->childCategories = TblCategory::where('parent_id', $this->selectedParentCategory)->get();
            }
        }

        if ($this->post->images) {
            $this->existingImages = explode(',', $this->post->images);
        }

        $postValues = TblPostValue::where('post_id', $this->post->id)->get();
        foreach ($postValues as $value) {
            $fieldDetail = TblFieldsDetail::find($value->field_id);
            if ($fieldDetail) {
                $key = $value->field_id . '_' . $fieldDetail->form_field_name;
                $this->customFieldsData[$key] = $value->value;

                if ($fieldDetail->form_field_name === 'brandwithmodel') {
                    $brandModelParts = explode(',', $value->value);
                    $this->customFieldsData[$key] = $brandModelParts[0];
                    $this->updatedCustomFieldsData($brandModelParts[0], $key); 
                    $modelKey = $value->field_id . '_brandswithmodels';
                    $this->customFieldsData[$modelKey] = $brandModelParts[1] ?? null;
                }
            }
        }
    }
    
    public function render()
    {
        if ($this->selectedChildCategory) {
            $this->customFields = TblFieldsDetail::where('cat_id', $this->selectedChildCategory)
                ->where('active', '1')->get();
        } else {
            $this->customFields = collect();
        }
        
        $parentCategories = TblCategory::whereNull('parent_id')->orderBy('list_order', 'asc')->get();
        $currencies = TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
        
        $parentCategory = null;
        if ($this->selectedParentCategory) {
            $parentCategory = TblCategory::find($this->selectedParentCategory);
        }

        return view('livewire.post.edit', compact(
            'parentCategories', 
            'currencies',
            'parentCategory'
        ));
    }

    public function updatedSelectedParentCategory($value)
    {
        $this->childCategories = TblCategory::where('parent_id', $value)->orderBy('list_order', 'asc')->get();
        $this->selectedChildCategory = null;
        $this->customFields = collect();
        $this->customFieldsData = [];
    }

    public function selectChildCategory($id)
    {
        $this->selectedChildCategory = $id;
        $this->customFieldsData = [];
    }

    public function nextStep()
    {
        $this->validate([
            'selectedParentCategory' => 'required',
            'selectedChildCategory' => 'required',
            'title' => 'required|max:255',
        ]);
        $this->currentStep = 2;
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function updatedImages($images)
    {
        $this->validate(['images.*' => 'image|max:10240']);
        foreach ($images as $image) {
            $this->images[] = $image;
        }
    }

    public function removeNewImage($index)
    {
        if (isset($this->images[$index])) {
            array_splice($this->images, $index, 1);
        }
    }
    
    public function removeExistingImage($index)
    {
        if (isset($this->existingImages[$index])) {
            $this->imagesToDelete[] = $this->existingImages[$index];
            array_splice($this->existingImages, $index, 1);
        }
    }

    public function updatePost()
    {
        $this->validate([
            'title' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
            'location' => 'required',
        ]);
        
        foreach ($this->imagesToDelete as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $newImagePaths = $this->processNewImages();

        $finalImagePaths = array_merge($this->existingImages, $newImagePaths);
        
        $this->post->images = implode(',', $finalImagePaths);
        $this->post->title = $this->title;
        $this->post->price = $this->price;
        $this->post->description = $this->description;
        $this->post->currency_id = $this->currency_id;
        $this->post->video_url = $this->video_url;
        $this->post->product_condition = $this->product_condition;
        $this->post->exchange_to_buy = $this->exchangeToBuy;
        $this->post->fixed_price = $this->FixedPrice;
        $this->post->instant_buy = $this->InstantBuy;
        $this->post->shipping_rate = $this->shipping_fee;
        
        $this->post->category_id = $this->selectedChildCategory;
        
        $this->post->save();

        TblPostValue::where('post_id', $this->post->id)->delete();
        $this->saveCustomFields($this->post->id, $this->customFieldsData);

        session()->flash('message', 'Post updated successfully!');
        return redirect()->to('/post');
    }

    private function processNewImages()
    {
        $imagenamesArr = [];
        $watermarkPath = public_path('storage/watermark.png');
        $watermarkExists = file_exists($watermarkPath);

        foreach ($this->images as $image) {
            try {
                $img = Image::make($image->getRealPath());
                if ($watermarkExists) {
                    $watermark = Image::make($watermarkPath)->widen(150)->opacity(60);
                    $img->insert($watermark, 'bottom-right', 10, 10);
                }
                $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                $fullPath = 'adpost/predefined/' . $filename;
                Storage::disk('public')->put($fullPath, (string) $img->encode());
                $imagenamesArr[] = $fullPath;
            } catch (\Exception $e) {
                // Log error if needed
            }
        }
        return $imagenamesArr;
    }
    
    private function saveCustomFields($post_id, $formData)
{
    // ... (Your existing $skipfields array)
    $skipfields = [
        'post-id', 'text-title-sst', 'number-price-sst', 'textarea-desc-sst', 
        'selected_category', 'text-video-sst', 'product_condition', 'exchangeToBuy', 
        'FixedPrice', 'InstantBuy', 'text-shipping-fee', 'package_type', 
        'payment_type', 'currency_id', 'text-city-sst', 'text-country-sst', 
        'city_name', 'main_city_name', 'city_lat', 'city_lag', 'country_long', 
        'country_short', 'state_long', 'state_short', 'images_indhu', 
        'selected-img-index'
    ];

    $brandModelData = [];

    foreach ($formData as $key => $value) {
        // 1. Agar key standard field hai, to usay ignore kar dein
        if (in_array($key, $skipfields)) {
            continue;
        }

        $field_id = explode('_', $key)[0];
        $field_name_slug = explode('_', $key, 2)[1];

        // 2. Agar Brandwithmodel field hai, to uski value ko jama kar len
        if ($field_name_slug === 'brandwithmodel') {
            $brandModelData[$field_id]['brand'] = $value ?? '';
            continue; // Isko abhi skip karen, Model ke saath save hoga
        }

        // 3. Agar Brandswithmodels field hai, to uski value ko jama kar len
        if ($field_name_slug === 'brandswithmodels') {
            $brandModelData[$field_id]['model'] = $value ?? '';
            continue; // Isko bhi abhi skip karen
        }
        
        // 4. Baaki normal custom fields ko save karein
        TblPostValue::create([
            'post_id' => $post_id,
            'field_id' => $field_id,
            'value' => is_array($value) ? implode(',', $value) : ($value ?? '')
        ]);
    }
    
    // 5. Brand aur Model ki combined value ko save karein
    foreach($brandModelData as $field_id => $data) {
        $brand_value = $data['brand'] ?? '';
        $model_value = $data['model'] ?? '';
        
        // Brand ID aur Model Slug ko comma se milakar save karna
        $combined_value = $brand_value . ',' . $model_value;

        TblPostValue::create([
            'post_id' => $post_id,
            'field_id' => $field_id,
            'value' => $combined_value
        ]);
    }
}

    public function updatedCustomFieldsData($value, $key)
    {
        $fieldId = explode('_', $key)[0];
        $fieldDetail = TblFieldsDetail::find($fieldId);

        if ($fieldDetail && $fieldDetail->form_field_name === 'brandwithmodel') {
            $brandOption = TblFieldsOption::find($value);
            if ($brandOption && !empty($brandOption->value)) {
                $modelsData = $brandOption->value;
                if (substr($modelsData, 0, 1) === '[' && substr($modelsData, -1) === ']') {
                    $this->dynamicModels[$fieldId] = json_decode($modelsData, true);
                } else {
                    $this->dynamicModels[$fieldId] = array_map('trim', explode(',', $modelsData));
                }
            } else {
                $this->dynamicModels[$fieldId] = [];
            }
        }
    }
}


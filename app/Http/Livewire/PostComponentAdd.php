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
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblCountry;
use App\Models\TblState;
use App\Models\TblCity;
use App\Models\TblPayment;
use App\Models\User;
use App\Models\TblFollowers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PostComponentAdd extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $selectedParentCategory;
    public $selectedParentCategoryTitle;
    public $childCategories = [];
    public $selectedChildCategory;
    public $selectedChildCategoryTitle;
    public $title;
    public $price;
    public $currency_id;
    public $description;
    public $video_url = '';
    public $product_condition = 1;
    public $exchangeToBuy = 0;
    public $FixedPrice = 0;
    public $InstantBuy = 0;
    public $shipping_fee = 0;
    public $package_type;
    public $payment_type = '';
    public $text_city_sst;
    public $text_country_sst;
    public $location;
    public $city_name;
    public $main_city_name;
    public $city_lat;
    public $city_lag;
    public $country_long;
    public $country_short;
    public $state_long;
    public $state_short;
    public $images = [];
    public $uploadedImages = [];
    public $selected_img_index = [];
    public $customFieldsHtml = '';
    public $productConditionHtml = '';
    public $showPaymentMethods = false;
    public $showShippingFee = false;
    public $customFieldsData = [];
	public $dynamicModels = [];
  	public $customFields = [];
    protected $rules = [
        'title' => 'required|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'required',
        'selectedChildCategory' => 'required',
        'text_city_sst' => 'required',
        'text_country_sst' => 'required',
        'location' => 'required',
        'package_type' => 'required',
    ];

    protected $messages = [
        'title.required' => 'Product title is required',
        'price.required' => 'Price is required',
        'description.required' => 'Description is required',
        'selectedChildCategory.required' => 'Please select a sub-category',
        'text_city_sst.required' => 'City is required',
        'text_country_sst.required' => 'Country is required',
        'location.required' => 'Location is required',
        'package_type.required' => 'Please select a package',
    ];

    public function mount()
    {
        $settings = Setting::get_logos();
        $this->currency_id = $settings['default_currency'];
        
        $freePackage = Package::where('name', 'Free')->first();
        if ($freePackage) {
            $this->package_type = $freePackage->id;
        }
    }

    public function getTitlePlaceholderProperty()
    {
        $examples = [
            'Automobiles' => ['Cars' => 'e.g. Toyota Corolla 2020 GLI Automatic', 'Motorcycles' => 'e.g. Honda CB150R 2023 Red', 'Trucks' => 'e.g. Hino Truck 2019 10-Wheeler', 'Buses' => 'e.g. Toyota Coaster 2018 30-Seater', 'Boats' => 'e.g. Yamaha Speedboat 2021 150HP'],
            'Electronics' => ['Mobile Phones' => 'e.g. iPhone 14 Pro Max 256GB Deep Purple', 'Laptops' => 'e.g. MacBook Pro M2 16GB 512GB 2023', 'Tablets' => 'e.g. iPad Air 5th Gen 64GB WiFi', 'Cameras' => 'e.g. Canon EOS R6 Mark II Body Only', 'TVs' => 'e.g. Samsung 55" OLED 4K Smart TV 2024'],
            'Property' => ['Houses' => 'e.g. 3 Bedroom House in DHA Phase 5', 'Apartments' => 'e.g. 2 Bed Apartment Bahria Town Islamabad', 'Plots' => 'e.g. 10 Marla Plot DHA Phase 8', 'Commercial' => 'e.g. Shop for Sale Gulberg III Lahore', 'Rooms' => 'e.g. Furnished Room for Rent Near UOL'],
            'Fashion' => ['Men' => 'e.g. Branded Leather Jacket Size L Black', 'Women' => 'e.g. Designer Lawn Suit Unstitched 3-Piece', 'Kids' => 'e.g. Kids Winter Jacket Age 5-6 Blue', 'Shoes' => 'e.g. Nike Air Max 270 Size 42 White', 'Accessories' => 'e.g. Ray-Ban Aviator Sunglasses Original'],
            'Furniture' => ['Sofa' => 'e.g. 7-Seater L-Shape Sofa Set Brown', 'Beds' => 'e.g. King Size Wooden Bed with Side Tables', 'Tables' => 'e.g. Office Desk with Drawers Wooden', 'Chairs' => 'e.g. Gaming Chair Ergonomic with Lumbar Support'],
            'Jobs' => ['Full Time' => 'e.g. Senior Software Developer - Laravel/PHP', 'Part Time' => 'e.g. Part Time Data Entry Operator', 'Freelance' => 'e.g. Graphic Designer for Social Media'],
            'Services' => ['Home Services' => 'e.g. Professional House Painting Service', 'Repair' => 'e.g. AC Repair and Installation Service'],
        ];

        $parent = $this->selectedParentCategoryTitle;
        $child = $this->selectedChildCategoryTitle;

        if ($parent && $child && isset($examples[$parent][$child])) {
            return $examples[$parent][$child];
        }

        if ($parent && isset($examples[$parent])) {
            $first = reset($examples[$parent]);
            return $first;
        }

        if ($child) {
            return 'e.g. ' . $child . ' - Brand, Model, Condition';
        }

        if ($parent) {
            return 'e.g. Your ' . $parent . ' product title here';
        }

        return 'e.g. iPhone 14 Pro Max 256GB';
    }

    public function render()
    {
      	//dd($this->getWatermarkPath());
        // Agar category select ho to uske custom fields load karein
        if ($this->selectedChildCategory) {
            $cfld = TblCustomField::where('cat_id', $this->selectedChildCategory)->first();
            if ($cfld && $cfld->field_count > 0) {
                $this->customFields = TblFieldsDetail::where('cat_id', $this->selectedChildCategory)
                    ->where('active', '1')
                    ->get();
            } else {
                $this->customFields = [];
            }

            $this->productConditionHtml = $this->generateProductConditionHtml($this->selectedChildCategory);
        } else {
            $this->customFields = [];
        }
		
        $parentCategories = TblCategory::whereNull('parent_id')->orderBy('list_order', 'asc')->get();
        $payment_methods = TblPaymentsMethod::where('active', '1')->get()->toArray();
        $packagesList = Package::get_active_packages();
        $currencies = TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();

        return view('livewire.post.add', compact(
            'parentCategories', 
            'payment_methods', 
            'packagesList',
            'currencies'
        ));
    }

    public function updatedSelectedParentCategory($value)
    {
        if (empty($value)) {
            $this->childCategories = [];
            $this->selectedParentCategoryTitle = null;
            $this->selectedChildCategory = null;
            $this->customFieldsHtml = '';
            $this->productConditionHtml = '';
            return;
        }

        $selectedCategory = TblCategory::find($value);
        if ($selectedCategory) {
            $this->selectedParentCategoryTitle = $selectedCategory->title;
        } else {
            $this->selectedParentCategoryTitle = null;
        }

        $this->childCategories = TblCategory::where('parent_id', $value)
            ->orderBy('list_order', 'asc')
            ->get();
            
        $this->selectedChildCategory = null;
        $this->selectedChildCategoryTitle = null;
        $this->customFieldsHtml = '';
        $this->productConditionHtml = '';
    }

    public function selectChildCategory($id)
  {
      $this->selectedChildCategory = $id;
      $childCat = TblCategory::find($id);
      $this->selectedChildCategoryTitle = $childCat ? $childCat->title : null;
      $this->customFieldsData = []; // Custom fields data ko reset karein
      $this->dynamicModels = [];    // Dynamic models ko reset karein

      // HTML generate karne wala code yahan se hata diya gaya hai
      $this->dispatchBrowserEvent('categorySelected', ['categoryId' => $id]);
  }

   

    public function nextStep()
{
    if ($this->currentStep == 1) {
        // Step 1 ke liye base validation rules
        $rules = [
            'selectedParentCategory' => 'required',
            'selectedChildCategory' => 'required',
            'title' => 'required|max:255',
        ];

        $messages = [
            'selectedParentCategory.required' => 'Please select a main category.',
            'selectedChildCategory.required' => 'Please select a sub-category.',
            'title.required' => 'Product title is required.',
        ];

        // Required custom fields ke liye dynamic rules add karein
        if ($this->selectedChildCategory) {
            $requiredFields = TblFieldsDetail::where('cat_id', $this->selectedChildCategory)
                ->where('active', '1')
                ->where('required', '!=', '0') // Sirf required fields hasil karein
                ->get();

            foreach ($requiredFields as $field) {
                $fieldName = 'customFieldsData.' . $field->id . '_' . $field->form_field_name;
                $rules[$fieldName] = 'required';
                $messages[$fieldName . '.required'] = 'The ' . $field->name . ' field is required.';
            }
        }
        
        // Validation ko run karein
        $this->validate($rules, $messages);

        // Agar validation pass ho jaye, to agle step par jayein
        $this->currentStep = 2;
    }
}

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    

    public function updatedInstantBuy($value)
    {
        $this->showShippingFee = (bool)$value;
        if ($value) {
            $this->validate(['shipping_fee' => 'required|numeric|min:0']);
        }
    }

    public function updatedPackageType($value)
{
    $package = Package::find($value);
    if ($package && $package->short_name != 'free') {
        $this->showPaymentMethods = true;
    } else {
        $this->showPaymentMethods = false;
        $this->payment_type = '';
    }
}

    public function updatedImages($images)
    {
        $this->validate([
            'images.*' => 'image|max:10240',
        ]);
        
        foreach ($images as $image) {
            $this->uploadedImages[] = $image;
        }
        
        $this->selected_img_index = array_keys($this->uploadedImages);
        $this->images = [];
    }
    public function updateImageOrder($newOrder)
{
    // Reorder the images based on the new order
    $reorderedImages = [];
    foreach ($newOrder as $index) {
        if (isset($this->uploadedImages[$index])) {
            $reorderedImages[] = $this->uploadedImages[$index];
        }
    }
    $this->uploadedImages = $reorderedImages;
}
    public function removeImage($index)
    {
        if (isset($this->uploadedImages[$index])) {
            unset($this->uploadedImages[$index]);
            $this->uploadedImages = array_values($this->uploadedImages);
            $this->selected_img_index = array_keys($this->uploadedImages);
        }
    }

    public function submit()
{
    $this->validate();

    if (empty($this->uploadedImages)) {
        $this->addError('images', 'Please upload at least one image.');
        return;
    }

    $package = Package::find($this->package_type);
    
    // Yahan 'short_name' se check hoga
    if ($package && $package->short_name != 'free' && empty($this->payment_type)) {
        $this->addError('payment_type', 'Please select a payment method for paid package.');
        return;
    }

    try {
        $formData = [
            'post-id' => 0,
            'text-title-sst' => $this->title,
            'number-price-sst' => $this->price,
            'textarea-desc-sst' => $this->description,
            'selected_category' => $this->selectedChildCategory,
            'text-video-sst' => $this->video_url,
            'product_condition' => $this->product_condition,
            'exchangeToBuy' => $this->exchangeToBuy,
            'FixedPrice' => $this->FixedPrice,
            'InstantBuy' => $this->InstantBuy,
            'text-shipping-fee' => $this->shipping_fee,
            'package_type' => $this->package_type,
            'payment_type' => $this->payment_type,
            'currency_id' => $this->currency_id,
            'text-city-sst' => $this->text_city_sst,
            'text-country-sst' => $this->text_country_sst,
            'city_name' => $this->city_name,
            'main_city_name' => $this->main_city_name,
            'city_lat' => $this->city_lat,
            'city_lag' => $this->city_lag,
            'country_long' => $this->country_long,
            'country_short' => $this->country_short,
            'state_long' => $this->state_long,
            'state_short' => $this->state_short,
            'images_indhu' => $this->uploadedImages,
            'selected-img-index' => implode(',', $this->selected_img_index),
        ];

        $formData = array_merge($formData, $this->customFieldsData);

        $result = $this->savePostToDatabase($formData);
	
        if ($result === 'success') {
            session()->flash('message', 'Post added successfully!');
            return redirect()->to('/post');
        } elseif (is_array($result) && isset($result['type']) && $result['type'] === 'payment') {
            return $this->handlePaymentRedirect($result);
        } else if ($result === 'invalid_image') {
            $this->addError('images', 'Unsupported image type. Please upload valid images.');
        } else {
            $this->addError('submit', 'Failed to save post. Please try again.');
        }

    } catch (\Exception $e) {
        $this->addError('submit', 'Error: ' . $e->getMessage());
    }
}

    private function savePostToDatabase($formData)
{
    $user_id = Auth::id();
    $title = $formData['text-title-sst'];
    $price = $formData['number-price-sst'];
    $desc = $formData['textarea-desc-sst'];
    $cat_id = $formData['selected_category'];
    $currency_id = $formData['currency_id'];
    $package_type = $formData['package_type'];

    $predefined_imgs = $this->processImages($formData);

    if ($predefined_imgs === 'invalid_image') {
        return 'invalid_image';
    }

    $locationData = $this->processLocationData($formData);
    
    $slug = Str::slug($title, "-") . '-' . $this->getTotalPostCount();

    $package = Package::find($package_type);
    
    // Yahan 'short_name' se check hoga
    $active_status = 0;
    if ($package && $package->short_name == 'free') {
        $active_status = 1;
    }

    $postData = [
        'user_id' => $user_id,
        'category_id' => $cat_id,
        'title' => $title,
        'description' => $desc,
        'price' => $price,
        'slug' => $slug,
        'city' => $locationData['city_id'],
        'locality' => $locationData['cityNames'],
        'images' => $predefined_imgs,
        'currency_id' => $currency_id,
        'active' => $active_status, // active status yahan se set hoga
        'product_condition' => $formData['product_condition'],
        'exchange_to_buy' => $formData['exchangeToBuy'],
        'fixed_price' => $formData['FixedPrice'],
        'instant_buy' => $formData['InstantBuy'],
        'video_url' => $formData['text-video-sst'],
        'shipping_rate' => $formData['text-shipping-fee']
    ];

    $post = TblPost::create($postData);

    $this->saveCustomFields($post->id, $formData);
    
    // Yahan 'short_name' se check hoga
    if ($package && $package->short_name == 'free') {
        $this->handlePackage($post->id, $formData);
    }

    // Yahan 'short_name' se check hoga
    if ($package && $package->short_name != 'free') {
        return [
            'type' => 'payment',
            'post_id' => $post->id
        ];
    }

    return 'success';
}

   private function processImages($formData)
  {
      \Log::info('========== PROCESS IMAGES STARTED ==========');

      $imagenamesArr = []; 

      if (empty($formData['images_indhu'])) {
          \Log::warning('No images found in formData');
          return '';
      }

      \Log::info('Total images received: ' . count($formData['images_indhu']));
      \Log::info('Selected image indices: ' . ($formData['selected-img-index'] ?? 'NONE'));

      // Watermark path fetch karein
      $watermarkPath = getWatermarkPath();

      \Log::info('Watermark Path Retrieved: ' . ($watermarkPath ?? 'NULL'));
      \Log::info('Watermark File Exists: ' . ($watermarkPath && file_exists($watermarkPath) ? 'YES ✅' : 'NO ❌'));

      if ($watermarkPath && file_exists($watermarkPath)) {
          \Log::info('Watermark File Size: ' . filesize($watermarkPath) . ' bytes');
          \Log::info('Watermark MIME Type: ' . mime_content_type($watermarkPath));
      }

      $watermarkExists = $watermarkPath && file_exists($watermarkPath);

      foreach ($formData['images_indhu'] as $key => $image) {
          \Log::info("Processing image key: {$key}");
          \Log::info("Image name: " . $image->getClientOriginalName());

          $selectedIndices = explode(',', $formData['selected-img-index'] ?? '');
          \Log::info("Is selected? " . (in_array($key, $selectedIndices) ? 'YES' : 'NO'));

          if (in_array($key, $selectedIndices)) {
              try {
                  \Log::info("--- Starting image processing for: " . $image->getClientOriginalName());

                  $img = Image::make($image->getRealPath()); 
                  \Log::info('Image loaded successfully');
                  \Log::info('Image dimensions: ' . $img->width() . 'x' . $img->height());
                  \Log::info('Image format: ' . $img->mime());

                  if ($watermarkExists) {
                      \Log::info('🔥 APPLYING WATERMARK - START');

                      try {
                          $watermark = Image::make($watermarkPath);
                          \Log::info('Watermark image loaded: ' . $watermark->width() . 'x' . $watermark->height());

                          // Watermark ka size calculate karein
                          $watermarkWidth = intval($img->width() * 0.15);
                          if ($watermarkWidth < 80) $watermarkWidth = 80;

                          \Log::info('Watermark will be resized to width: ' . $watermarkWidth);

                          $watermark->widen($watermarkWidth, function ($constraint) {
                              $constraint->upsize();
                          });
                          \Log::info('Watermark resized to: ' . $watermark->width() . 'x' . $watermark->height());

                          $watermark->opacity(70);
                          \Log::info('Watermark opacity set to 70');

                          $img->insert($watermark, 'top-right', 20, 20);
                          \Log::info('✅ WATERMARK INSERTED SUCCESSFULLY');

                      } catch (\Exception $wmError) {
                          \Log::error('❌ Watermark application failed: ' . $wmError->getMessage());
                          \Log::error('Watermark stack trace: ' . $wmError->getTraceAsString());
                      }

                  } else {
                      \Log::warning('⚠️ Watermark does not exist - skipping watermark');
                  }

                  $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                  $fullPath = 'adpost/predefined/' . $filename;

                  \Log::info('Saving image as: ' . $fullPath);
                  Storage::disk('public')->put($fullPath, (string) $img->encode()); 
                  \Log::info('✅ Image saved successfully');

                  $imagenamesArr[] = $fullPath; 

              } catch (\Exception $e) {
                  \Log::error('❌❌❌ IMAGE PROCESSING ERROR ❌❌❌');
                  \Log::error('Error message: ' . $e->getMessage());
                  \Log::error('Error file: ' . $e->getFile() . ' line: ' . $e->getLine());
                  \Log::error('Stack trace: ' . $e->getTraceAsString());
                  return 'invalid_image'; 
              }
          }
      }

      \Log::info('========== PROCESS IMAGES COMPLETED ==========');
      \Log::info('Total images processed: ' . count($imagenamesArr));

      return implode(',', $imagenamesArr);
  }

  /**
   * Database se watermark path fetch kare
   */
  private function getWatermarkPath()
  {
      try {
          $settings = DB::table('settings')->where('key', 'app')->first();

          if (!$settings) {
              \Log::warning('Settings not found in database');
              return null;
          }

          $settingsValue = json_decode($settings->value, true);

          if (empty($settingsValue['app_watermark'])) {
              \Log::warning('app_watermark not found in settings');
              return null;
          }

          // Full path banayein
          $watermarkPath = public_path($settingsValue['app_watermark']);

          return $watermarkPath;

      } catch (\Exception $e) {
          \Log::error('Error fetching watermark from database: ' . $e->getMessage());
          return null;
      }
  }
  

    private function processLocationData($formData)
    {
        $country_id = $this->getOrCreateCountry($formData);
        $state_id = $this->getOrCreateState($country_id, $formData);
        $city_id = $this->getOrCreateCity($country_id, $state_id, $formData);

        $cityNames = $formData['city_name'] . "," . $formData['main_city_name'];

        return [
            'city_id' => $city_id,
            'cityNames' => $cityNames
        ];
    }

    private function getOrCreateCountry($formData)
    {
        $country = TblCountry::where('code', $formData['country_short'])
                            ->where('name', $formData['country_long'])
                            ->first();

        if (!$country) {
            $country = TblCountry::create([
                'code' => $formData['country_short'],
                'name' => $formData['country_long']
            ]);
        }

        return $country->id;
    }

    private function getOrCreateState($country_id, $formData)
    {
        $state = TblState::where('country_id', $country_id)
                        ->where('code', $formData['state_short'])
                        ->where('name', $formData['state_long'])
                        ->first();

        if (!$state) {
            $state = TblState::create([
                'country_id' => $country_id,
                'code' => $formData['state_short'],
                'name' => $formData['state_long']
            ]);
        }

        return $state->id;
    }

    private function getOrCreateCity($country_id, $state_id, $formData)
    {
        $city = TblCity::where('country_id', $country_id)
                      ->where('state_id', $state_id)
                      ->where('name', $formData['main_city_name'])
                      ->where('locality', $formData['city_name'])
                      ->first();

        if (!$city) {
            $city = TblCity::create([
                'country_id' => $country_id,
                'state_id' => $state_id,
                'locality' => $formData['city_name'],
                'name' => $formData['main_city_name'],
                'latitude' => $formData['city_lat'],
                'logitude' => $formData['city_lag']
            ]);
        }

        return $city->id;
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

    private function handlePackage($post_id, $formData)
{
    $package = Package::find($formData['package_type']);
    $curr_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime($curr_date . "+" . $package->duration . " days"));

    TblPostedAdPackageInfo::create([
        'user_id' => Auth::id(),
        'post_id' => $post_id,
        // Yahan 'short_name' se check hoga
        'ad_type' => $package->short_name == 'free' ? 'free' : 'paid',
        'start_date' => $curr_date,
        'end_date' => $end_date,
        'active' => '1'
    ]);
}

    private function handlePaymentRedirect($result)
    {
        $package = Package::find($this->package_type);
        $currency_symbol = Setting::get_admin_default_currency();
        $currency_id = $currency_symbol['id'];

        if ($this->payment_type == "paypal") {
            return redirect('/paypal-payment-process?pack_amt=' . $package->price . '&cid=' . $currency_id . '&post_id=' . $result['post_id'] . '&live_days=' . $package->duration . '&package_id=' . $package->id . '&payment_type=paypal&coupon_id=');
        } else if ($this->payment_type == "stripe") {
            return redirect('/stripe-payment?pack_amt=' . $package->price . '&cid=' . $currency_id . '&post_id=' . $result['post_id'] . '&live_days=' . $package->duration . '&package_id=' . $package->id . '&payment_type=stripe&coupon_id=&uid=' . Auth::id() . '&paid_for=package');
        }
    }

    private function getTotalPostCount()
    {
        return TblPost::count() + 1;
    }
	public function updatedCustomFieldsData($value, $key)
  {
      // Key se Field ID nikalein (UUID)
      $fieldId = explode('_', $key)[0];

      // Check karein ke yeh brand wala field hi hai
      $fieldDetail = TblFieldsDetail::find($fieldId);

      if ($fieldDetail && $fieldDetail->form_field_name === 'brandwithmodel') {
          // Selected brand ki ID se uske models ka data hasil karein
          $brandOption = TblFieldsOption::find($value);

          if ($brandOption && !empty($brandOption->value)) {
              $modelsData = $brandOption->value;
              // Data ko array mein convert karein (chahe woh JSON ho ya comma-separated)
              if (substr($modelsData, 0, 1) === '[' && substr($modelsData, -1) === ']') {
                  $this->dynamicModels[$fieldId] = json_decode($modelsData, true);
              } else {
                  $this->dynamicModels[$fieldId] = array_map('trim', explode(',', $modelsData));
              }
          } else {
              // Agar koi brand select na ho to models ko khali kar dein
              $this->dynamicModels[$fieldId] = [];
          }
      }
  }
    private function generateCustomFieldsHtml($catid)
{
    $htmltag = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';

    $cfld = TblCustomField::where('cat_id', $catid)->first();
    
    if ($cfld && $cfld->field_count > 0) {
        $arrayData = TblFieldsDetail::where('cat_id', $catid)->where('active', '1')->get();

        foreach ($arrayData as $r) {
            $field_id = $r["id"];
            $type = $r["type"];
            $name = $field_id . '_' . $r["form_field_name"];
            $label = $r['name'];
            $required = ($r['required'] == "0") ? "" : "required";
            $requiredLbl = ($r['required'] == "0") ? "" : '&nbsp;<span class="text-red-800">*</span>';
            
            $htmltag .= '<div class="mb-4">';
            $htmltag .= '<label class="block text-sm font-medium text-gray-700 mb-2">' . $label . $requiredLbl . '</label>';

            if (in_array($type, ["text", "number", "date"])) {
                $htmltag .= '<input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" type="' . $type . '" wire:model="customFieldsData.' . $name . '" ' . $required . ' />';
            } elseif ($r['form_field_name'] === 'brandwithmodel') {
                $brandOptions = TblFieldsOption::where('cat_id', $catid)
                    ->where('form_field_name', $r['form_field_name'])
                    ->where('active', '1')
                    ->orderBy('key', 'asc')
                    ->get();

                // Brand Dropdown (yeh pehle jaisa hi hai)
                $htmltag .= '<select wire:model="customFieldsData.' . $name . '" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" data-field-id="' . $field_id . '" ' . $required . '>';
                $htmltag .= '<option value="">Select Brand</option>';
                foreach ($brandOptions as $option) {
                    $htmltag .= '<option value="' . $option->id . '" data-models="' . htmlspecialchars($option->value) . '">' . $option->key . '</option>';
                }
                $htmltag .= '</select>';
                $htmltag .= '</div>';

                // Models Dropdown (YAHAN TABDEELI HAI)
                $htmltag .= '<div class="mb-4">';
                $htmltag .= '<label class="block text-sm font-medium text-gray-700 mb-2">Models ' . $requiredLbl . '</label>';
                $htmltag .= '<select wire:model="customFieldsData.' . $field_id . '_brandswithmodels" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" ' . $required . '>';

                if (isset($this->dynamicModels[$field_id]) && !empty($this->dynamicModels[$field_id])) {
                    $htmltag .= '<option value="">Select Model</option>';
                    foreach ($this->dynamicModels[$field_id] as $model) {
                        $modelValue = strtolower(str_replace(' ', '-', $model));
                        $htmltag .= '<option value="' . $modelValue . '">' . $model . '</option>';
                    }
                } else {
                    $htmltag .= '<option value="">Select brand first</option>';
                }

                $htmltag .= '</select>';
          } elseif ($type == "textarea") {
                $htmltag .= '<textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" wire:model="customFieldsData.' . $name . '" ' . $required . ' rows="3"></textarea>';
            
            // YAHAN TABDEELI KI GAYI HAI
            } elseif (in_array($type, ["select", "autocomplete", "radio-group"])) { // "radio-group" yahan add kar diya gaya hai
                $options = TblFieldsOption::where('cat_id', $catid)
                    ->where('form_field_name', $r["form_field_name"])
                    ->where('active', '1')
                    ->get();
                $htmltag .= '<select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" wire:model="customFieldsData.' . $name . '" ' . $required . '>';
                $htmltag .= '<option value="">Select ' . $label . '</option>';
                foreach ($options as $k) {
                    $htmltag .= '<option value="' . $k["value"] . '">' . $k["key"] . '</option>';
                }
                $htmltag .= '</select>';
            } elseif ($type == "checkbox-group") {
                $options = TblFieldsOption::where('cat_id', $catid)
                    ->where('form_field_name', $r["form_field_name"])
                    ->where('active', '1')
                    ->get();
                $htmltag .= '<div class="chips flex flex-wrap gap-2" data-group="' . $name . '">';
                foreach ($options as $k) {
                    $htmltag .= '<button class="chip px-3 py-2 border border-gray-300 rounded-full text-sm hover:bg-gray-50" type="button" data-value="' . $k["value"] . '" wire:click="$set(\'customFieldsData.' . $name . '\', \'' . $k["value"] . '\')">' . $k["key"] . '</button>';
                }
                $htmltag .= '</div>';
            }
            // Purana "radio-group" wala block yahan se hata diya gaya hai
            
            $htmltag .= '</div>';
        }
    }
    
    $htmltag .= '</div>';

    return $htmltag;
}

    private function generateProductConditionHtml($id)
    {
        $check = TblCategory::where("id", $id)->pluck('product_condition')->first();
        $pchtmlTag = "";
        if ($check == 1) {
            $pchtmlTag .= '<div class="mb-4">';
            $pchtmlTag .= '<label class="block text-base text-black font-semibold mb-2">Product Condition <span class="text-red-800">*</span></label>';
            $pchtmlTag .= '<select wire:model="product_condition" required class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 focus:outline-none placeholder-gray-400 border border-gray-300">';
            $pchtmlTag .= '<option value="">Select Condition</option>';
            $pchtmlTag .= '<option value="1">Like New</option>';
            $pchtmlTag .= '<option value="2">Lightly used</option>';
            $pchtmlTag .= '<option value="3">Heavily used</option>';
            $pchtmlTag .= '</select>';
            $pchtmlTag .= '</div>';
        }
        return $pchtmlTag;
    }
}
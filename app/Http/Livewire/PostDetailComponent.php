<?php

namespace App\Http\Livewire;

use App\Models\TblCategory;
use App\Models\TblCity;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\User;
use App\Models\TblPostInsight;
use App\Models\TblSavedPosts;
use App\Models\ReportType;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class PostDetailComponent extends Component
{
    public TblPost $post;
    public $is_favorited = false;

    // Modal States
    public $showReportModal = false;
    public $showOfferModal = false;
    public $showVideoModal = false;

    // Form Properties
    public $reportType;
    public $reportComment;
    public $offerPrice;
    public $offerMessage;

    // Data for View
    public $allFeatures = [];
    public $jsImages = [];

    public function mount($slug)
    {
        $this->post = TblPost::where('slug', $slug)->where('active', 1)->firstOrFail();
        $user = $this->post->user;

        if (!$user || $user->deleted_at !== null || $user->is_blocked) {
            abort(404);
        }
        
        $this->trackPostView();
        $this->checkIfFavorited();
        $this->prepareFeatures();
        $this->prepareImages();
    }

    public function checkIfFavorited()
    {
        if (Auth::check()) {
            $this->is_favorited = TblSavedPosts::where('user_id', Auth::id())
                                              ->where('post_id', $this->post->id)
                                              ->exists();
        }
    }
	public function submitReport()
    {
        if (!Auth::check()) { return redirect()->route('login'); }

        $this->validate([
            'reportType' => 'required',
            'reportComment' => 'required|string|max:500',
        ]);
        
        // Yahan par report save karne ki logic aayegi
        
        $this->showReportModal = false;
        $this->reset(['reportType', 'reportComment']);
        $this->dispatchBrowserEvent('show-toast', ['message' => 'Report submitted successfully.']);
    }
    public function toggleFavorite()
    {
        if (!Auth::check()) { return redirect()->route('login'); }

        $favorite = TblSavedPosts::where('user_id', Auth::id())->where('post_id', $this->post->id)->first();
        if ($favorite) {
            $favorite->delete();
            $this->is_favorited = false;
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Removed from favorites!']);
        } else {
            TblSavedPosts::create(['user_id' => Auth::id(), 'post_id' => $this->post->id]);
            $this->is_favorited = true;
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Added to favorites!']);
        }
    }

    private function prepareFeatures()
    {
        $post_values = TblPostValue::where('post_id', $this->post->id)->get();
        $features = [];

        foreach ($post_values as $value) {
            $field = TblFieldsDetail::find($value->field_id);
            if (!$field) continue;

            $label = $field->name;
            $post_value = $value->value;
            $processed_value = $post_value;

            if (in_array($field->type, ['select', 'autocomplete', 'radio-group'])) {
                 $option = TblFieldsOption::where('field_id', $field->id)
                                          ->where('value', $post_value)
                                          ->first();
                 $processed_value = $option ? $option->key : $post_value;
            } elseif ($field->form_field_name === 'brandwithmodel') {
                $values = explode(',', $post_value);
                $brand_id = $values[0] ?? null;
                $model_slug = $values[1] ?? null;
                
                $brand_name = TblFieldsOption::where('id', $brand_id)->value('key');
                $model_name = Str::title(str_replace('-', ' ', $model_slug));
                
                $processed_value = trim("$brand_name, $model_name", ', ');
            } elseif ($field->type === 'checkbox-group') {
                 $checked_values = explode(',', $post_value);
                 $option_keys = TblFieldsOption::where('field_id', $field->id)
                                              ->whereIn('value', $checked_values)
                                              ->pluck('key');
                 $processed_value = $option_keys->implode(', ');
            }
            
            $features[] = ['k' => $label, 'v' => $processed_value];
        }
        
        if ($this->post->exchange_to_buy == 1) {
            $features[] = ['k' => 'Exchange to Buy', 'v' => 'Yes, willing to exchange'];
        }

        $this->allFeatures = $features;
    }


    private function prepareImages()
    {
        $images = explode(',', $this->post->images);
        foreach ($images as $r) {
            if(empty($r)) continue;
            $this->jsImages[] = \Illuminate\Support\Facades\Storage::url($r);
        }
    }


     private function trackPostView()
    {
        TblPost::where('id', $this->post->id)->increment('views_count');
        if(Auth::check() && Auth::id() != $this->post->user_id) {
            TblPostInsight::updateOrCreate(
                ['user_id' => Auth::id(), 'post_id' => $this->post->id, 'visited_date' => today()],
                ['ip_address' => request()->ip(), 'views' => \DB::raw('views + 1')]
            );
        }
    }
    public function render()
    {
        $info_location = TblCity::find($this->post->city);
        $category = TblCategory::find($this->post->category_id);
        $related_products = TblPost::get_related_products($this->post->category_id, $this->post->id);
        $report_types = ReportType::where('type', 'post')->get();
        $info_user = User::find($this->post->user_id);
        $settings = Setting::get_logos();

        return view('livewire.post-detail-component', [
            'info_location' => $info_location,
            'category_name' => $category->title ?? 'N/A',
            'category_slug' => $category->slug ?? '',
            'info_user' => $info_user,
            'related_products' => $related_products,
            'report_types' => $report_types,
            'settings' => $settings,
        ])->layout('layouts.packagebuy');
    }
}


<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Package;
use App\Models\User;
use Livewire\WithPagination;

class Packages extends Component
{

    use WithPagination;

    public $package_id, $name, $short_name, $single_pack_limit, $ribbon, $has_badge, $price, $currency_code, $promo_duration, $duration, $pictures_limit, $facebook_ads_duration, $google_ads_duration, $twitter_ads_duration, $linkedin_ads_duration, $description, $lft, $recommended, $active, $bulk_ads, $bulk_limit, $ad_type, $bulk_type;
    public $ad_typeArr = array("top_ad" => "Top Ad", "feature_ad" => "Feature Ad");
    public $badge_val = 1;
    public $active_val = 1;
    public $bulk_ad_val = 1;
    public $recommend_val = 1;
    public $insertMode = false;
    public $updateMode = false;
    public $cnfopen = 0;

    protected $rules = [
        'name' => 'required',
        'short_name' => 'required',
        'price' => 'required',
        'ad_type' => 'required',
    ];

    public function render()
    {
        return view('livewire.admin.package.compo', [
            'packages' => Package::orderBy('created_at', 'asc')->paginate(10),
        ]);
    }

    // for back button redirect page
    public function back()
    {
        return redirect()->route('admin/package');
    }

    public function create()
    {
        $this->insertMode = true;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();
    //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error');
            return redirect()->route('admin/package');
        }
    //end check demo user

        if (($this->bulk_ads == 1 && $this->bulk_limit == null) || ($this->bulk_ads == 1 && $this->bulk_type == null)) {
            $this->validate([
                'bulk_limit' => 'required',
                'bulk_type' => 'required',
            ], [
                'bulk_limit.required' => 'if bulk add clicked, minimum 1 Ad required.',
                'bulk_type.required' => 'if bulk add clicked, need to choose validate type for bulk ads.',
            ]);
            $this->bulk_ads = false;
        }

        $bulk_limit_v = ($this->bulk_ads == 1) ? $this->bulk_limit : 0;
        $bulk_type_v = ($this->bulk_ads == 1) ? $this->bulk_type : 0;
        Package::create([
            'name' => $this->name,
            'short_name' => $this->short_name,
            'ribbon' => $this->ribbon,
            'has_badge' => $this->has_badge ?? '0',
            'price' => $this->price,
            'currency_code' => "-",
            'bulk_ads' => $this->bulk_ads ?? '0',
            'bulk_limit' => $bulk_limit_v,
            'bulk_type' => $bulk_type_v,
            'promo_duration' => $this->promo_duration ?? '30',
            'duration' => $this->duration ?? '30',
            'pictures_limit' => $this->pictures_limit ?? '5',
            'facebook_ads_duration' => $this->facebook_ads_duration ?? '0',
            'google_ads_duration' => $this->google_ads_duration ?? '0',
            'twitter_ads_duration' => $this->twitter_ads_duration ?? '0',
            'linkedin_ads_duration' => $this->linkedin_ads_duration ?? '0',
            'description' => $this->description,
            'lft' => $this->lft,
            'recommended' => $this->recommended ?? '0',
            'active' => $this->active ?? '0',
            'ad_type' => $this->ad_type,
        ]);

        $this->insertMode = false;
        $this->updateMode = false;
        session()->flash('message', 'Package Created Successfully.');
        return redirect()->route('admin/package');
    }

    public function update()
    {
        $this->validate();
    //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error');
            return redirect()->route('admin/package');
        }
    //end check demo user

        $packages = Package::find($this->package_id);

        if ($this->bulk_ads == "1" && $this->bulk_limit == 0) {
            $this->validate([
                'bulk_limit' => 'required',
            ], [
                'bulk_limit.required' => 'if bulk add clicked, minimum 1 Ad required.',
            ]);
        } else if ($this->bulk_ads == 1 && $this->bulk_type == 0) {
            $this->validate([
                'bulk_type' => 'required',
            ], [
                'bulk_type.required' => 'if bulk add clicked, need to choose validate type for bulk ads.',
            ]);
        } else if ($this->lft == 1 && $this->single_pack_limit == 0) {
            $this->validate([
                'single_pack_limit' => 'required',
            ], [
                'single_pack_limit.required' => 'Need to set min 1 ad as an limit.',
            ]);
        } else {
            $bulk_limit_v = ($this->bulk_ads == 1) ? $this->bulk_limit : 0;
            $bulk_type_v = ($this->bulk_ads == 1) ? $this->bulk_type : 0;

            $packages->update([
                'name' => $this->name,
                'short_name' => $this->short_name,
                'ribbon' => $this->ribbon,
                'has_badge' => $this->has_badge ?? '0',
                'price' => $this->price,
                'bulk_ads' => $this->bulk_ads,
                'bulk_limit' => $bulk_limit_v,
                'bulk_type' => $bulk_type_v,
                'ad_type' => $this->ad_type,
                'currency_code' => "-",
                'promo_duration' => $this->promo_duration ?? '30',
                'duration' => $this->duration ?? '30',
                'pictures_limit' => $this->pictures_limit ?? '5',
                'facebook_ads_duration' => $this->facebook_ads_duration ?? '0',
                'google_ads_duration' => $this->google_ads_duration ?? '0',
                'twitter_ads_duration' => $this->twitter_ads_duration ?? '0',
                'linkedin_ads_duration' => $this->linkedin_ads_duration ?? '0',
                'description' => $this->description,
                'single_pack_limit' => !empty($this->single_pack_limit) ? $this->single_pack_limit : "",
                'lft' => $this->lft,
                'recommended' => $this->recommended ?? '0',
                'active' => $this->active ?? '0',
            ]);

            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', 'Package Update Successfully.');
            return redirect()->route('admin/package');
        }
    }

    public function edit($id)
    {
        $packages = Package::find($id);
        $this->package_id = $id;
        $this->name = $packages->name;
        $this->short_name = $packages->short_name;
        $this->ribbon = $packages->ribbon;
        $this->has_badge = $packages->has_badge;
        $this->price = $packages->price;
        $this->bulk_ads = $packages->bulk_ads;
        $this->bulk_limit = $packages->bulk_limit;
        $this->bulk_type = $packages->bulk_type;
        $this->ad_type = $packages->ad_type;
        $this->currency_code = $packages->currency_code;
        $this->promo_duration = $packages->promo_duration;
        $this->duration = $packages->duration;
        $this->pictures_limit = $packages->pictures_limit;
        $this->facebook_ads_duration = $packages->facebook_ads_duration;
        $this->google_ads_duration = $packages->google_ads_duration;
        $this->twitter_ads_duration = $packages->twitter_ads_duration;
        $this->linkedin_ads_duration = $packages->linkedin_ads_duration;
        $this->description = $packages->description;
        $this->lft = $packages->lft;
        $this->single_pack_limit = $packages->single_pack_limit;
        $this->recommended = $packages->recommended;
        $this->active = $packages->active;

        $this->updateMode = true;
        $this->insertMode = false;
    }




    public function deleteReq($id)
    {
        $this->cnfopen = $id;
    }

    public function deleteCan()
    {
        $this->cnfopen = 0;
    }

    public function delete($id)
    {
        $this->cnfopen = 0;

        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            session()->flash('message', $isDemoUser["message"]);
            return;
        }

        Package::find($id)->delete();
        session()->flash('message', 'Package Deleted Successfully.');
    }
}

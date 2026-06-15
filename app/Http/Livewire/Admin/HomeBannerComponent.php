<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\TblBanners;
use App\Models\Setting;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use DB;
use Image;
Use Storage;
use Illuminate\Http\Request;

class HomeBannerComponent extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $insertMode = false;
    public $updateMode=false;
    public $cnfopen = 0;
    protected $listeners = ['ckeditorUpdated'];
    public $images, $stored_images, $banner_id, $banner_url,$description;

    public function updatingSearch()
    {
        $this->resetPage();
    }
   

    public function ckeditorUpdated($data)
    {
      
        $this->description = $data;
    }

    public function render()
    {

        $banners = TblBanners::whereNull('deleted_at')->orderBy('id','asc')->limit(6)->get();

           return view('livewire.admin.home-banner.compo', compact('banners'));

    }


    // for back button redirect page
    public function back()
    {   
        return redirect()->route('admin/home-banner');
    }


    public function create()
    {           
        $this->insertMode=true;
        $this->updateMode = false;
    }


   

    public function store()
    {
       
     
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error'); 
            return redirect()->route('admin/home-banner');
        }
     //end check demo user

        $this->validate([
            'images' => 'required',
			'banner_url' => 'required',
            'description' => 'required'
        ]);

        $imagename = "";

        if ($this->images != null) {
            //$imagename = $this->images->store('banners', 'public');
			
			
		/* Get watermark image */
            $settings = Setting::get_logos();
			$getImage = $this->images;
			
		$imagename = $getImage->hashName('banners');	
		$path_web_list = $getImage->hashName('public/banners');
		/*$web_list = Image::make($getImage)->resize(null, 350 , function ($constraint) {
			$constraint->aspectRatio();
		}); */
		$web_list = Image::make($getImage);
		$web_list->insert(public_path('storage/'.$settings['watermark']), 'bottom-right', 10, 10);
		Storage::put($path_web_list, (string) $web_list->encode());

			

            TblBanners::create([
                'images' => $imagename,
				'banner_url' => $this->banner_url,
               'content' => $this->description
            ]);
            
        }


        session()->flash('message', 'New Banner added successfully.');
        Session()->flash('class', 'success'); 
        return redirect()->route('admin/home-banner');


    }


    public function edit($id)
    {

        $get_image = TblBanners::find($id);
        $this->stored_images = $get_image->images;
		$this->banner_url = $get_image->banner_url;
        $this->banner_id = $id;
        $this->description = $get_image->content;

        $this->updateMode = true;
        $this->insertMode=false;

    }


public function update()
{

    //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error'); 
            return redirect()->route('admin/home-banner');
        }
    //end check demo user

    $this->validate([
		'banner_url' => 'required',
       
    ]);
	
	if(empty($this->stored_images))
	{
		$this->validate([
        'images' => 'required',
		]);
	}

    $update_img = TblBanners::find($this->banner_id);

            $imagename = "";

            if ($this->images != null) {
                //$imagename = $this->images->store('banners', 'public');
				
				/* Get watermark image */
				$settings = Setting::get_logos();
				$getImage = $this->images;

				//-------
				$imagename = $getImage->hashName('banners');	
				$path_web_list = $getImage->hashName('public/banners');
				/* $web_list = Image::make($getImage)->resize(null, 350 , function ($constraint) {
					$constraint->aspectRatio();
				}); */
				$web_list = Image::make($getImage);
				$web_list->insert(public_path('storage/'.$settings['watermark']), 'bottom-right', 10, 10);
				Storage::put($path_web_list, (string) $web_list->encode());
				//---
				
                
                $update_img->update([
                    'images' => $imagename,
                ]);
            }
			
			    $update_img->update([
					'banner_url' => $this->banner_url,
                    'content' => $this->description
                ]);

            session()->flash('message', 'New Banner Updated successfully.');
            Session()->flash('class', 'success'); 
            return redirect()->route('admin/home-banner');

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

        TblBanners::find($id)->delete();
        session()->flash('message', 'Deleted successfully.');
        Session()->flash('class', 'success'); 
        
    }



}
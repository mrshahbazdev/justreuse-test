<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ReportType;
use App\Models\TblPostMethod;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class PostMethodsComponent extends Component
{

    public $method_list;

    public function render()
    {
        $this->update_post_settings();
        $this->method_list = TblPostMethod::get();
        return view('livewire.admin.post_methods.show');
    }

    public function update_post_settings()
    {
        //begin - read all folders
        $folders_list = scandir(base_path() . '/extra/postplugins');
        $folders = [];
        $only_folders = [];
        foreach ($folders_list as $f) {
            if ($f == "." || $f == "..") {
                continue;
            }
            if ($f == "bannerads") {
                $desc = 'Promoting your brands under a banner section to reach more traffic among users';
            } else {
                $desc = 'User can enable the ' . $f . ' option for their post to sold fast.';
            }
            $types =                     [
                'name' => $f,
                'display_name' => ucfirst($f),
                'description' => $desc,
                'active' => '0'
            ];

            $folders[] = $types;
            $only_folders[] = $f;
        }
        //end - read all folders

        //begin - inserting/removing list of payments detail
        foreach ($folders as $j) {
            $path = base_path() . '/extra/postplugins/' . $j['name'];
            if (file_exists($path)) {
                $isExist = TblPostMethod::where('name', $j['name']);
                if ($isExist->count() == 0) {
                    TblPostMethod::create([
                        'name' => $j['name'],
                        'display_name' => $j['display_name'],
                        'description' => $j['description'],
                        'active' => $j['active'],
                    ]);
                }
            } else {
                $isExist = TblPostMethod::where('name', $j['name'])->delete();
            }
        }
        //end - inserting/removing list of payments detail
        $tkt = TblPostMethod::whereNotIn('name', $only_folders);
        $tkt->delete();

        //begin - remove from table, unnecessary folder names
        //end - remove from table, unnecessary folder names
    }


    public function enable_post_method()
    {
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['message' => $isDemoUser["message"]]);
        }

        $id = request()->id;
        $val = request()->active;
        $node = TblPostMethod::find($id);
        $node->update([
            'active' => $val,
        ]);
        return response()->json(['message' => 'updated successfully']);
    }
}

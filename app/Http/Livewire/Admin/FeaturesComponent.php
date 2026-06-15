<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Exports\FeaturesExport;
use Illuminate\Http\Request;
use App\Models\Feature;
use App\Models\User;
use App\Models\TblCategory;
use App\Models\TblCustomField;
use App\Models\FeaturesMappingGroup;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Imports\FeaturesImport;

class FeaturesComponent extends Component
{
    public function render()
    {
        $categorylist = TblCategory::orderBy('list_order', "asc")->with('ancestors')->get()->toTree();
        return view('livewire.admin.features.show', compact('categorylist'));
    }
    public function get_brand(Request $request)
    {
        $catid = $request->cat_id;
        $brandData = [];
        $cfld  = TblCustomField::where('cat_id', $catid)->get();
        if (!empty($cfld[0])) {
            if ($cfld[0]->field_count > 0) {
                $arrayData = TblFieldsDetail::where('cat_id', $catid)->where('active', '1')->get();

                if ($arrayData->count() > 0) {
                    foreach ($arrayData as $r) {


                        if ($r['form_field_name'] == "brandwithmodel") {
                            $field_id = $r["id"];
                            $arrayData = TblFieldsOption::where('cat_id', $catid)->where('form_field_name', $r["form_field_name"])->where('active', '1')->get();
                            foreach ($arrayData as $brands) {

                                $brandData[] = array(
                                    'id' => $brands->id,
                                    'field_id' => $brands->field_id,
                                    'key' => $brands->key,
                                    'form_field_name' => $brands->form_field_name,
                                );
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['data' => $brandData]);
    }
    public function store(Request $request)
    {
        $brand_id = "";
        $request->validate([
            'file' => 'required|mimes:xlsx',
            'cat_id' => 'required'
        ]);
        $cat_id = $request->cat_id;
        $brand_id = $request->brand_id;
        try {
            Excel::import(new FeaturesImport($cat_id, $brand_id), $request->file('file'));
            // return redirect()->route('brand-features')->with('success', 'Features imported successfully.');
            return redirect()->back()->with('message', 'Features imported successfully.');
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('message', 'Duplicate Entry or Something went wrong!!');
        }
    }

    public function export()
    {
        return Excel::download(new FeaturesExport, 'features.xlsx');
    }

    public function features_map()
    {
        $categorylist = TblCategory::orderBy('list_order', "asc")->with('ancestors')->get()->toTree();
        return view('livewire.admin.features.featuremap', compact('categorylist'));
    }
    public function features_map_show()
    {
        $featuremaplist = FeaturesMappingGroup::where('cat_id', '64')->orderBy('list_order', "asc")->get();

        return view('livewire.admin.features.featuremap_show', compact('featuremaplist'));
    }

    public function features_map_store(Request $request)
    {
        $request->validate([
            'feature_title.*' => 'required|string',  // Validate each feature title
            'cat_id' => ['required', 'in:64'],
            'feature_items.*' => 'required|string'  // Validate each feature item
        ], [
            'cat_id.in' => 'The category  must be exactly Car.',
        ]);

        $titles = $request->feature_title;
        $items = $request->feature_items;
        $cat_id = $request->cat_id;

        // Loop through each title and item and store them
        foreach ($titles as $index => $title) {
            FeaturesMappingGroup::create([
                'cat_id' => $cat_id,
                'features_title' => $title,
                'features_items' => $items[$index]
            ]);
        }
        // Redirect with success message
        session()->flash('message', 'Features Mapped successfully.');
        Session()->flash('class', 'success');
        // Redirect with success message
        return redirect()->route('features-map-show')->with('success', 'Features Mapped successfully.');
    }

    public function features_map_edit($id)
    {
        $featuredata = FeaturesMappingGroup::find($id);
        $categorylist = TblCategory::orderBy('list_order', "asc")->with('ancestors')->get()->toTree();
        return view('livewire.admin.features.featuremap_edit')->with(['featuredata' => $featuredata, 'categorylist' => $categorylist]);
    }
    public function features_map_update(Request $request)
    {
        $request->validate([
            'feature_title.*' => 'required|string',  // Validate each feature title
            'cat_id' => ['required', 'in:64'],
            'feature_items.*' => 'required|string'  // Validate each feature item
        ], [
            'cat_id.in' => 'The category  must be exactly Car.',
        ]);

        $titles = $request->feature_title;
        $items = $request->feature_items;
        $cat_id = $request->cat_id;
        $id = $request->id;
        $features = FeaturesMappingGroup::find($id);
        $features->update([
            'features_title' => $titles,
            'features_items' => $items,
            'cat_id' => $cat_id
        ]);
        // Redirect with success message
        session()->flash('message', 'FeaturesMapping Updated successfully.');
        Session()->flash('class', 'success');
        return redirect()->route('features-map-show')->with('success', 'FeaturesMapping Updated successfully.');
    }
    public function features_map_delete($id)
    {
        $featuredata = FeaturesMappingGroup::find($id);
        $featuredata->delete();
        session()->flash('message', 'Deleted successfully.');
        Session()->flash('class', 'success');
        return redirect()->route('features-map-show')->with('success', 'FeaturesMapping Deleted successfully.');
    }

    public function featureOrder()
    {
        $list = FeaturesMappingGroup::orderBy('list_order','asc')->get();
        return view('livewire.admin.features.fea_orderlist', compact('list'));
    }

    public function update_features_order()
    {
         //start check demo user
         $isDemoUser = User::isDemoUser();
         if($isDemoUser["result"]==true)
         {
             session()->flash('message', $isDemoUser["message"]);
             Session()->flash('result', '0');
             return response()->json(['result'=>"failed"]);
         }
     //end check demo user

     if($_SERVER["REQUEST_METHOD"]=="POST"){

         $formdata = request()->all();
        
         if(!empty($formdata)){
         $data_arr = ($formdata["data_array"]==null)?"":$formdata["data_array"];
             foreach($data_arr as $j)
             {
                 $rowid = $j["row_id"];
                 $list_order = $j["list_order"];
                 $node = FeaturesMappingGroup::where('id',$rowid);
                 $node->update(['list_order'=>$list_order]);
             }
             session()->flash('result','1');
             session()->flash('message','Reorder done successfully');
 
             return response()->json(['result'=>"success"]);
         }
         else{
            session()->flash('result','0');
            session()->flash('message','Nothing to update');
 
             return response()->json(['result'=>"failed"]);
         }

     }

    }

}

<?php
namespace App\Http\Livewire\Admin;
use Livewire\Component;
use App\Models\TblLanguage;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Session;
use Illuminate\Support\Str;

class LanguageComponentEdit extends Component
{


    public function render()
    {
        $id  = request()->id;

        $locales = config('locales');
        $languages = config('languages');
        $direction = ([
            '0'      => 'ltr',
            '1'     => 'rtl',]);
        $data = TblLanguage::find($id);
        return view('livewire.admin.languages.edit', compact('data','locales','languages','direction')); 

    }

// for back button redirect page
public function back()
{   
    return redirect()->route('admin/language');
}


public function update($formdata)
{

    //start check demo user
    $isDemoUser = User::isDemoUser();
    if($isDemoUser["result"]==true)
    {
        session()->flash('message', $isDemoUser["message"]);
        Session()->flash('class', 'error');
       return redirect()->route('admin/language');
    }
//end check demo user

    $chk_abbr = config('languages');
    $id = $formdata['id'];

    $name = $formdata['language'];
    $native = $formdata['native'];
    $locales = $formdata['locales'];
    $direction = $formdata['direction'];
    $abbr = array_search($name,$chk_abbr,true);

    $exist = TblLanguage::where('id','!=',$id)->where('name',$name)->where('abbr',$abbr)->where('locale',$locales)->get();
    if(count($exist)==0)
    {
        $data = TblLanguage::find($id);

        $data->update([
                'name' => $name,
                'abbr' => $abbr,
                'native' => $native,
                'locale' => $locales,
                'direction' => $direction,
        ]);
        session()->flash('message', 'Updated Successfully');
    }
    else{
        session()->flash('message', 'Same language setup already exist!');
    }

    return redirect()->route('admin/language');
    // dd($id, $name, $native, $locales, $direction, $abbr);
}



}
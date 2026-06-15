<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblLanguage;
use App\Models\Languages;
use App\Models\Addlanguage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\File;



class LanguageComponent extends Component
{

    use WithPagination;


    public $name, $native, $abbr, $direction;
    public $updateMode = false;
    public $insertMode = false;
    public $cnfopen = 0;
    public $locales;
    public $languages;



    // for back button redirect page
    public function back()
    {
        return redirect()->route('admin/language');
    }


    public function render()
    {

        $this->locales = config('locales');
        $this->languages = config('languages');

        $language = TblLanguage::paginate(10);
        return view('livewire.admin.languages.compo', compact('language'));
    }


    public function create()
    {

        $this->insertMode = true;
        $this->updateMode = false;
    }

    public function store($formdata)
    {

        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error');
            return redirect()->route('admin/language');
        }
        //end check demo user

        $chk_abbr = config('languages');

        $name = $formdata['language'];
        $native = $formdata['native'];
        $locales = $formdata['locales'];
        $direction = $formdata['direction'];
        $abbr = array_search($name, $chk_abbr, true);

        // dd($name, $native, $locales, $direction, $abbr);


        //  dd($this->name,$this->native,$this->abbr,$this->direction);
        $db_chk = TblLanguage::where('name', $name)->where('native', $native)->count();
        // dd($db_chk);

        if ($db_chk > 0) {
            session()->flash('message', 'This language already Exist..');
            return redirect()->route('admin/language');
        } else {

            TblLanguage::create([
                'name' => $name,
                'abbr' => $abbr,
                'native' => $native,
                'locale' => $locales,
                'direction' => $direction,
            ]);
        }
        $this->insertMode = false;
        $this->updateMode = false;
        return redirect()->route('admin/language');
    }


    public function active()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user

        $id = request()->id;
        $active_val = request()->active_val;

        $active = TblLanguage::find($id);

        $active->update([
            'active' => $active_val,
        ]);

        return response()->json(['message' => 'actived']);
    }


    public function default()
    {
        //start check demo user
       // $isDemoUser = User::isDemoUser();
       // if ($isDemoUser["result"] == true) {
           // return response()->json(['message' => $isDemoUser["message"]]);
      //  }
        //end check demo user

       /* $id = request()->id;
        $default_val = request()->default_val;

        $default1 = TblLanguage::where('default', '1');

        $default1->update([
            'default' => '0'
        ]);

        $default = TblLanguage::find($id);
        $default->update([
            'default' => $default_val,
        ]);

        return response()->json(['message' => 'default']);*/
      

    // Start check demo user
    $isDemoUser = User::isDemoUser();
    if ($isDemoUser["result"] == true) {
        return response()->json(['message' => $isDemoUser["message"]]);
    }
    // End check demo user

    $id = request()->id;
    $default_val = request()->default_val;

    if ($default_val == '1') {
        // If setting a new default, update all records to not be default
        TblLanguage::where('default', '1')->update(['default' => '0']);
    }

    // Update the selected record
    $default = TblLanguage::find($id);
    $default->update(['default' => $default_val]);

    // Ensure there's always one record with default = 1
    if ($default_val == '0') {
        $defaultExists = TblLanguage::where('default', '1')->exists();
        if (!$defaultExists) {
            // Set the last updated record as default if no record is default
            $default->update(['default' => '1']);
        }
    }

    return response()->json(['message' => 'default updated']);


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
        if ($isDemoUser["result"] == true) {
            session()->flash('message', $isDemoUser["message"]);
        } else {

            TblLanguage::find($id)->delete();
            session()->flash('message', 'Language Deleted Successfully.');
            return redirect()->route('admin/language');
        }
    }

    public function sublanguage()
    {

        $locale = request()->locale;
        //dd($locale);
        $addlanguages = Languages::where('lang_code', $locale)->get();
        $addlanguage = TblLanguage::all();
        //   dd($addlanguage);
        return view('addlang.index', compact('addlanguage', 'locale', 'addlanguages'));
        //return view('admin.translatelanguage');

    }

    public function active_sub_languages()
    {

        //dd(request()->search);
        $locale = request()->locale;
        //dd($locale);
        $search = request()->search;
        //dd($search);
        if ($search == null) {

            $texts = DB::table('languages')
                ->where('lang_code', $locale)
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            //dd('xxx');
            $texts = DB::table('languages')
                ->where('lang_code', $locale)
                ->where('lang_org_text', 'like', '%' . $search . '%')
                ->orderBy('created_at', 'asc')
                ->get();
        }
        return response()->json($texts);
    }


    public function edit_sub_languages()
    {
        //dd('ccc');
        $id = request()->id;
        $lang_code = request()->lang_code;
        $lang_org_text = request()->lang_org_text;
        $lang_text = request()->lang_text;

        $language_code = request()->language_code;
        $translation_key = request()->language_key;

        $translation_value = request()->old_text;

        $new_translation = $lang_text;

		
        $file_update = $this->generateLanguageMapping($translation_key, $translation_value, $language_code, $new_translation);

        // dd($file_update,$translation_key, $translation_value, $language_code, $new_translation);

        if ($lang_code) {
            $model = DB::table('languages')->where('id', $id)->update(['lang_code' => $lang_code]);
        }
        if ($lang_org_text) {
            $model = DB::table('languages')->where('id', $id)->update(['lang_org_text' => $lang_org_text]);
        }
        if ($lang_org_text) {
            $model = DB::table('languages')->where('id', $id)->update(['lang_org_text' => $lang_org_text]);
        }
        if ($lang_text) {
            $model = DB::table('languages')->where('id', $id)->update(['lang_text' => $lang_text]);
        }
        return response()->json(["data" =>  $model, "message" => 'updated']);
    }


    public function delete_sub_languages()
    {
        $id = request()->id;

        $del = Languages::find($id)->delete($id);

        return response()->json(["data" => 'deleted Successfully', "message" => 'Deleted Successfully']);
    }


    public function add_sublang()
    {

        $locale = request()->locale;

        $addlanguage = TblLanguage::all();


        return view('addlang.create', compact('addlanguage', 'locale'));
    }


    public function add_sublang_store()
    {
        //dd('cccw');

        $Language_code = request()->Language_code;
        $Original_text = request()->Original_text;
        $Translate_text = request()->Translate_text;
        //dd($Language_code,$Original_text,$Translate_text);

        DB::table('languages')->insert([
            'lang_code'=>$Language_code,
            'lang_org_text'=>$Original_text,
            'lang_text'=>$Translate_text,
            'type'=>'mobile'
        ]);
        //dd('sucess');
        $commonFilePath = resource_path("lang/{$Language_code}/common.php");

        // Check if the "common.php" file exists
        if (File::exists($commonFilePath)) {
            // If it exists, load its data
            $commonData = require($commonFilePath);
        } else {
            // If it doesn't exist, initialize an empty array
            $commonData = [];
        }

        // Update or create the translation entry
        $commonData[$Original_text] = $Translate_text;

        // Generate the content for the "common.php" file
        $commonFileContent = '<?php' . PHP_EOL . 'return ' . var_export($commonData, true) . ';';

        // Write the content to the "common.php" file
        File::put($commonFilePath, $commonFileContent);

        return redirect('/admin/sublanguage-show?locale=' . $Language_code);
        //return redirect()->back()->withErrors(['msg' => 'added successfully']);

    }

   public function generateLanguageMapping($translation_key, $translation_value, $lang_code, $new_translation)
{
    
    $languageDirectory = resource_path('lang');
    $languageToFileMapping = [];
    $updatedFiles = [];
    $createdFiles = [];

    // Scan the language directory to find language folders
    $languageFolders = array_diff(scandir($languageDirectory), ['.', '..']);

    foreach ($languageFolders as $languageFolder) {
        $folderPath = $languageDirectory . '/' . $languageFolder;

        if (is_dir($folderPath)) {
            // Scan the language folder to find language files
            $languageFiles = array_diff(scandir($folderPath), ['.', '..']);

            $languageToFileMapping[$languageFolder] = [
                'folder' => $languageFolder,
                'files' => $languageFiles,
            ];
        }
    }

     
    // Check if the language code exists in the mapping
    if (isset($languageToFileMapping[$lang_code])) {
     
        $mapping = $languageToFileMapping[$lang_code];
		
        foreach ($mapping['files'] as $file_name) {
            $languageFilePath = $languageDirectory . '/' . $mapping['folder'] . '/' . $file_name;
				
            if (File::exists($languageFilePath)) {
             
                $languageData = require($languageFilePath);
					
                if (is_array($languageData)) {
                  
                    if (array_key_exists($translation_key, $languageData)) {
                      
                        $languageData[$translation_key] = $new_translation;
                        $updatedFiles[] = $file_name;
                    } else {
                     
                        $languageData[$translation_key] = $new_translation;
                        $createdFiles[] = $file_name;
                    }

                    $fileContent = '<?php' . PHP_EOL . 'return ' . var_export($languageData, true) . ';';
                    File::put($languageFilePath, $fileContent);
                 
                }
            }
        }
    }

    return !empty($updatedFiles) || !empty($createdFiles);
}

}

<?php
namespace App\Http\Livewire\Admin;
use App\Models\TblCategory;
use Livewire\Component;
use App\Models\TblCustomField;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Session;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;
class CustomFieldsComponentEdit extends Component
{
	public $ancestors;
	public function render()
	{
		$uuid  = request()->id;
		$id =  TblCategory::where('uuid', $uuid)->get();
		$id = $id[0]->id;
		$checkData =  TblCustomField::where('cat_id', $id)->get();
		$catInfo = TblCategory::find($id);
		$this->ancestors = TblCategory::ancestorsAndSelf($id);
		$data = array("cat_id" => $catInfo->id, "cat_title" => $catInfo->title, "html" => "", "field_count" => "0");
		if ($checkData->count() > 0) {
			$data = array("cat_id" => $catInfo->id, "cat_title" => $catInfo->title, "html" => $checkData[0]['html'], "field_count" => $checkData[0]['field_count']);
		}
		// dd($data);
		return view('livewire.admin.customfields.edit', compact('data'));
	}
	public function update($formdata)
	{
		//  dd($formdata);
		$jsd1 = json_decode($formdata['cathtml'], true);
		// Assigning logo value for missed logo field with image value. --start--
		if ($jsd1 != null) {
			foreach ($jsd1 as &$r) {
				if (array_key_exists('values', $r)) {
					$options = $r['values'];
					// dd($options);
					foreach ($options as &$values) {
						// $logo_option = isset($values['logo']) ? $values['logo'] : $values['image'] ; got issue in $values['logo'] fixed temp so upload image may cause issue;
						$logo_option = isset($values['logo']) ? $values['logo'] : 'noimage50.png';
						$values['logo'] = $logo_option;
						// // Remove all special characters and convert to hyphens
						// $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', $values['value']);
						// // Remove hyphens from the beginning and end of the slug
						// $slug = trim($slug, '-');
						// // Convert the slug to lowercase
						// $slug = strtolower($slug);
						// $values['value'] =Str::slug($values['value'], "-");
						// Process other data if needed and add it to $processedOptions
					}
					$r['values'] = $options; // Update the modified options back to the main array
				}
			}
			// Reconstruct the modified data into $formdata['cathtml']
			$formdata['cathtml'] = json_encode($jsd1);
		}
		// Now $formdata['cathtml'] contains the modified JSON data with logo values assigned
		// -------------------- end -------------------------
		$parentid = TblCategory::where('id', $formdata['catid'])->get()[0]['parent_id'];
		$redirect_url = URL::to('/admin/category/');
		if ($parentid != null && $parentid != "") {
			$uuid = TblCategory::where('id', $parentid)->get()[0]['uuid'];
			$redirect_url = $redirect_url . '/' . $uuid . '/subcategories';
		}
		//start check demo user
		$isDemoUser = User::isDemoUser();
		if ($isDemoUser["result"] == true) {
			session()->flash('message', $isDemoUser["message"]);
			Session()->flash('result', '0');
			return redirect($redirect_url);
		}
		//end check demo user
		$catid = $formdata['catid'];
		$html = $formdata['cathtml'];
		$fieldcount = $formdata['catfieldcount'];
		$isExist =  TblCustomField::where('cat_id', $catid)->count();
		$insertedId = 0;
		if ($isExist == 0) {
			$query = TblCustomField::create([
				'cat_id' => $catid,
				'html' => $html,
				'field_count' => $fieldcount
			]);
			$insertedId = $query->id;
		} else {
			$record = TblCustomField::where('cat_id', $catid);
			$record->update([
				'html' => $html,
				'field_count' => $fieldcount
			]);
		}
		//begin transaction -- while insert 
		$jsd = json_decode($html, true);
		
		if ($insertedId != 0 && $jsd != null) {
			foreach ($jsd as $r) {
				$name = strip_tags($r['label']);
				// Change type for radio into checkbox if muliselect is true
				// if (array_key_exists('multiSelect', $r)) {
				// 	$type = ($r['multiSelect'] == true) ? "checkbox-group" : "radio-group";
				// }else{
				// 	$type =  $r['type']; //checkbox,select,or text
				// }
				$required = "0";
				if (array_key_exists('required', $r)) {
					$required = ($r['required'] == true) ? "1" : "0";
				}
				$filter = "0";
				if (array_key_exists('filter', $r)) {
					$filter = ($r['filter'] == true) ? "1" : "0";
				}
				$icon = "0";
				if (array_key_exists('icon', $r)) {
					$icon = ($r['icon'] == true) ? "1" : "0";
				}
				$count = "0";
				if (array_key_exists('count', $r)) {
					$count = ($r['count'] == true) ? "1" : "0";
				}
				$multiselect = "0";
				if (array_key_exists('multiSelect', $r)) {
					$multiselect = ($r['multiSelect'] == true) ? "1" : "0";
				}
				$helptext = "";
				if (array_key_exists('description', $r)) {
					$helptext = $r['description'];
				}
				$formfieldname = $r['name'];
				$last_inserted_id = TblFieldsDetail::create([
					'cat_id' => $catid,
					'name' => $name,
					'type' => $type,
					'required' => $required,
					'filter' => $filter,
					'icon' => $icon,
					'count' => $count,
					'is_multiple' => $multiselect,
					'helptext' => $helptext,
					'form_field_name' => $formfieldname
				])->id;
				if (array_key_exists('values', $r)) {
					$options = $r['values'];
					if ($r['name'] == "brandwithmodel") {
						foreach ($options as $j) {
							$opt_key = $j['label'];
							$opt_value = $j['value'];
							$opt_logo = isset($j['logo']) ? $j['logo'] : $j['image'];
							$s = TblFieldsOption::create([
								'cat_id' => $catid,
								'field_id' => $last_inserted_id,
								'key' => $opt_key,
								'value' => $opt_value,
								'logo' => $opt_logo,
								'form_field_name' => $formfieldname
							]);
						}
					} else {
						foreach ($options as $j) {
							$opt_key = $j['label'];
							$opt_value = $j['value'];
							$opt_logo = isset($j['logo']) ? $j['logo'] : $j['image'];
							$s = TblFieldsOption::create([
								'cat_id' => $catid,
								'field_id' => $last_inserted_id,
								'key' => $opt_key,
								'value' => Str::slug($opt_value, "-"),
								'logo' => $opt_logo,
								'form_field_name' => $formfieldname
							]);
						}
					}
				}
			}
		}
		//update process
		if ($insertedId == 0  && $jsd != null) {
			$newFormFields = array();
			foreach ($jsd as $r) {
				$name = strip_tags($r['label']);
				// Change type for radio into checkbox if muliselect is true
				// if (array_key_exists('multiSelect', $r)) {
				// 	$type = ($r['multiSelect'] == true) ? "checkbox-group" : "radio-group";
				// }else{
				// 	
				// }
				$type =  $r['type']; //checkbox,select,or text
				$required = "0";
				if (array_key_exists('required', $r)) {
					$required = ($r['required'] == true) ? "1" : "0";
				}
				$filter = "0";
				if (array_key_exists('filter', $r)) {
					$filter = ($r['filter'] == true) ? "1" : "0";
				}
				$icon = "0";
				if (array_key_exists('icon', $r)) {
					$icon = ($r['icon'] == true) ? "1" : "0";
				}
				$count = "0";
				if (array_key_exists('count', $r)) {
					$count = ($r['count'] == true) ? "1" : "0";
				}
				$multiselect = "0";
				if (array_key_exists('multiSelect', $r)) {
					$multiselect = ($r['multiSelect'] == true) ? "1" : "0";
				}
				$helptext = "";
				if (array_key_exists('description', $r)) {
					$helptext = $r['description'];
				}
				$formfieldname = $r['name'];
				$isInFieldDetail = TblFieldsDetail::where('cat_id', $catid)->where('form_field_name', $formfieldname)->count();
				if ($isInFieldDetail == 0) {
					// dd('koko');
					//begin insert with new fields
					$last_inserted_id = TblFieldsDetail::create([
						'cat_id' => $catid,
						'name' => $name,
						'type' => $type,
						'required' => $required,
						'filter' => $filter,
						'icon' => $icon,
						'count' => $count,
						'is_multiple' => $multiselect,
						'helptext' => $helptext,
						'form_field_name' => $formfieldname
					])->id;
					// if (array_key_exists('values', $r)) {
					// 	$options = $r['values'];
					// 	foreach ($options as $j) {
					// 		$opt_key = $j['label'];
					// 		$opt_value = $j['value'];
					// 		$opt_logo = isset($j['logo'])?$j['logo']:'noimage50.png';
					// 		$s = TblFieldsOption::create([
					// 			'cat_id' => $catid,
					// 			'field_id' => $last_inserted_id,
					// 			'key' => $opt_key,
					// 			'value' => $opt_value,
					// 			'logo'=> $opt_logo,
					// 			'form_field_name' => $formfieldname
					// 		]);
					// 	}
					// }
					if (array_key_exists('values', $r)) {
						$options = $r['values'];
						foreach ($options as $j) {
							// dd($j);
							$opt_key = $j['label'];
							$opt_value = $j['value'];
							$opt_logo = isset($j['logo']) ? $j['logo'] : $j['image'];
							$existingOption = TblFieldsOption::where('cat_id', $catid)
								->where('field_id', $last_inserted_id)
								->where('key', $opt_key)
								->first();
							if ($existingOption) {
								// Update existing option
								$existingOption->update([
									'value' => $opt_value,
									'logo' => $opt_logo,
									'active' => 1 // Set to active when updating
								]);
							} else {
								// Create new option
								TblFieldsOption::create([
									'cat_id' => $catid,
									'field_id' => $last_inserted_id,
									'key' => $opt_key,
									'value' => $opt_value,
									'logo' => $opt_logo,
									'form_field_name' => $formfieldname,
									'active' => 1
								]);
							}
						}
					}
				} else {
					// dd('koko1212');
					$recordId = TblFieldsDetail::where('cat_id', $catid)->where('form_field_name', $formfieldname)->get();
					$last_inserted_id = $recordId[0]['id'];
					// dd($last_inserted_id);
					//update begin with exist keys
					$record = TblFieldsDetail::where('id', $last_inserted_id);
					//$record = TblFieldsDetail::where('cat_id',$catid)->where('form_field_name',$formfieldname);
					$record->update([
						'name' => $name,
						'helptext' => $helptext,
						'required' => $required,
						'filter' => $filter,
						'icon' => $icon,
						'count' => $count,
						'is_multiple' => $multiselect
					]);
					//$last_inserted_id = $record->id;
					// if (array_key_exists('values', $r)) {
					// 	$activeOptions = array();
					// 	$options = $r['values'];
					// 	// print_r($options);
					// 	foreach ($options as $j) {
					// 		$opt_key = $j['label'];
					// 		$opt_value = $j['value'];
					// 		$opt_logo = isset($j['logo'])? $j['logo']: 'noimage50.png';
					// 		$isInFieldOpts = TblFieldsOption::where('cat_id', $catid)->where('key', $opt_key)->where('value', $opt_value)->where('form_field_name', $formfieldname)->count();
					// 		// dd($isInFieldOpts);
					//      if($isInFieldOpts == 0) {
					// 			$s = TblFieldsOption::create([
					// 				'cat_id' => $catid,
					// 				'field_id' => $last_inserted_id,
					// 				'key' => $opt_key,
					// 				'value' => $opt_value,
					// 				'logo' => $opt_logo,
					// 				'form_field_name' => $formfieldname
					// 			]);
					// 		}
					// 		// else{
					// 		// 	$ksk = TblFieldsOption::where('cat_id',$catid)->where('form_field_name',$formfieldname)->where('active',1);
					// 		// 	// dd('update');
					// 		// 	$ksk->update([
					// 		// 		'key'=>$opt_key,
					// 		// 		'value'=>$opt_value,
					// 		// 		'logo' => $opt_logo
					// 		// 	]);
					// 		// }
					// 		array_push($activeOptions, $opt_key);
					// 	}
					// 	// dd($activeOptions);
					// 	DB::enableQueryLog(); 
					// 	$upd0 = TblFieldsOption::where('cat_id', $catid)->where('form_field_name', $formfieldname)->whereNotIn('key', $activeOptions);
					// 	$upd0->update(['active' => '0']);
					// 	//   $query = DB::getQueryLog();
					//     //          echo "<pre>";
					//     //          echo 'Model'; 
					//     //            dd($query);
					// }
					if (array_key_exists('values', $r)) {
						$activeOptions = array();
						$options = $r['values'];
						foreach ($options as $j) {
							// dd($j);
							$opt_key = $j['label'];
							$opt_value = $j['value'];
							$opt_logo = isset($j['logo']) ? $j['logo'] : $j['image'];
							$existingOption = TblFieldsOption::where('cat_id', $catid)
								->where('field_id', $last_inserted_id)
								->where('key', $opt_key)
								->first();
							if ($existingOption) {
								// Update existing option
								$existingOption->update([
									'value' => $opt_value,
									'logo' => $opt_logo,
									'active' => 1 // Set to active when updating
								]);
							} else {
								// Create new option
								TblFieldsOption::create([
									'cat_id' => $catid,
									'field_id' => $last_inserted_id,
									'key' => $opt_key,
									'value' => $opt_value,
									'logo' => $opt_logo,
									'form_field_name' => $formfieldname,
									'active' => 1
								]);
							}
							array_push($activeOptions, $opt_key);
						}
						// Deactivate options not present in the activeOptions array
						TblFieldsOption::where('cat_id', $catid)
							->where('field_id', $last_inserted_id)
							->whereNotIn('key', $activeOptions)
							->update(['active' => 0]);
					}
				}
				array_push($newFormFields, $formfieldname);
				// dd($newFormFields);
			} //exit;
			// die;
			//at last -> deactive previous fields
			$upd1 = TblFieldsDetail::where('cat_id', $catid)->whereNotIn('form_field_name', $newFormFields);
			$upd1->update(['active' => '0']);
			$upd2 = TblFieldsOption::where('cat_id', $catid)->whereNotIn('form_field_name', $newFormFields);
			$upd2->update(['active' => '0']);
		}
		Session::flash('result', '1');
		Session::flash('message', 'Updated Successfully');
		return redirect($redirect_url);
	}
	public function logo_upload(Request $request)
	{
		$name = $request->file->getClientOriginalName();
		$imageName = $name;
		$upload =  $request->file->move(public_path('storage/customfields/filters'), $imageName);
		if ($upload == true) {
			$result = "Uploaded successfully";
		} else {
			$result = "Unable to upload";
		}
		return response()->json(["name" => $imageName, "status" => $result]);
	}
}

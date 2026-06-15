<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ReportType;
use App\Models\User;
use Livewire\WithPagination;

class ReportTypeComponent extends Component
{
    use WithPagination;

    public $name, $report_id, $type;
    public $updateMode = false;
    public $insertMode = false;
    public $cnfopen = 0;
    public $search;

    protected $rules = [
        'name' => 'required',
        'type' => 'required'
    ];

    public function render()
    {
        $search = !empty($this->search) ? $this->search : "";
        $report_type = ReportType::where('name','Like', '%' . $search . '%')->orderBy('created_at','desc')->paginate(15);
        return view('livewire.admin.report_type.compo', [
            'report_type' => $report_type
        ]);
    }

    // for back button redirect page
    public function back()
    {
        return redirect()->route('admin/report-type');
    }


    public function create()
    {

        $this->insertMode = true;
        $this->updateMode = false;
    }


    public function store()
    {
        
     //start check demo user
     $isDemoUser = User::isDemoUser();
     if($isDemoUser["result"]==true)
     {
         $this->insertMode = false;
         $this->updateMode = false;
         session()->flash('message', $isDemoUser["message"]);
         return redirect()->route('admin/report-type');
     }
    //end check demo user

        $this->validate();


        $check = ReportType::where('name', $this->name)->count();
        if ($check == 0) {
            ReportType::create([
                'name' => $this->name,
                'type' => $this->type
            ]);
        }
        $this->insertMode = false;
        $this->updateMode = false;
        session()->flash('message', 'New report created.');
        return redirect()->route('admin/report-type');
    }

    public function edit($id)
    {
        $report = ReportType::find($id);
        $this->report_id = $id;
        $this->name = $report->name;
        $this->type = $report->type;

        $this->updateMode = true;
        $this->insertMode = false;
    }


    public function update()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', $isDemoUser["message"]);
            return redirect()->route('admin/report-type');
        }
    //end check demo user

        $this->validate();

        $report = ReportType::find($this->report_id);
        $check = ReportType::where('name', $this->name)->count();

        if ($check == 0) {
            $report->update([
                'name' => $this->name,
                'type' => $this->type
            ]);
        }

        $this->insertMode = false;
        $this->updateMode = false;
        session()->flash('message', 'Updated successfully.');
        return redirect()->route('admin/report-type');
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

        ReportType::find($id)->delete();
        session()->flash('message', 'Report Type Deleted Successfully.');
        
        
    }
}

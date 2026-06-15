<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblBlacklist;
use App\Models\User;
use Livewire\WithPagination;

class Blacklists extends Component
{

    use WithPagination;
    public $list_id, $type, $entry, $editdata;
    public $insertMode = false;
    public $updateMode = false;
    public $cnfopen = 0;
    public $search;

    public function render()
    {
        $search = !empty($this->search) ? $this->search : "";
        return view('livewire.admin.blacklist.compo', [
            'list' => TblBlacklist::whereNull('deleted_at')->where('entry','Like', '%' . $search . '%')->paginate(10),
        ]);
    }

    // for back button redirect page
    public function back()
    {
        return redirect()->route('admin/blacklist');
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
            if($isDemoUser["result"]==true)
            {
                $this->insertMode = false;
                $this->updateMode = false;
                session()->flash('message', $isDemoUser["message"]);
                Session()->flash('class', 'error');
                return redirect()->route('admin/blacklist');
            }
        //end check demo user

        $allpages = TblBlacklist::where('type', $formdata['type'])
            ->where('entry', $formdata['entry'])->first();
        if (empty($allpages)) {
            TblBlacklist::create([
                'type' => $formdata['type'],
                'entry' => $formdata['entry'],
            ]);
            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', 'Added successfully.');
            Session()->flash('class', 'success');
            return redirect()->route('admin/blacklist');
        } else {
            session()->flash('message', 'This entry already exist!.');
            Session()->flash('class', 'error');
            return redirect()->route('admin/blacklist');
        }
    }

    public function edit($id)
    {
        $this->editdata = TblBlacklist::find($id);
        $this->updateMode = true;
        $this->insertMode = false;
    }

    public function update($formdata)
    {
        //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                $this->insertMode = false;
                $this->updateMode = false;
                session()->flash('message', $isDemoUser["message"]);
                Session()->flash('class', 'error');
                return redirect()->route('admin/blacklist');
            }
        //end check demo user

        $staticpages = TblBlacklist::find($formdata['list_id']);
        $staticpages->update([
            'entry' => $formdata['entry'],
        ]);
        $this->insertMode = false;
        $this->updateMode = false;
        session()->flash('message', 'Updated successfully.');
        Session()->flash('class', 'success');
        return redirect()->route('admin/blacklist');
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
            Session()->flash('class', 'error');
            return;
        }

        $record1 = TblBlacklist::where('id', $id);
        $record1->delete();
        session()->flash('message', 'Deleted successfully.');
        Session()->flash('class', 'success');
        
    }
}

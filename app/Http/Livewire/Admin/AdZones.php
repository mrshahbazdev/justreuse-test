<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\AdZone;
use Livewire\WithPagination;

class AdZones extends Component
{
    use WithPagination;

    public $name, $page_location, $price_per_day;
    public $width, $height;
    public $auto_approve = false;
    public $is_active = true;
    public $zone_id;
    public $isOpen = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'page_location' => 'required|string',
        'price_per_day' => 'required|numeric|min:0',
        'width' => 'required|integer|min:1',
        'height' => 'required|integer|min:1',
        'auto_approve' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        return view('livewire.admin.ad-zones', [
            'zones' => AdZone::paginate(10),
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->reset(['name', 'page_location', 'price_per_day', 'width', 'height', 'auto_approve', 'is_active', 'zone_id']);
    }

    public function store()
    {
        $this->validate();

        AdZone::updateOrCreate(['id' => $this->zone_id], [
            'name' => $this->name,
            'page_location' => $this->page_location,
            'price_per_day' => $this->price_per_day,
            'specifications' => json_encode(['width' => $this->width, 'height' => $this->height, 'type' => 'image']),
            'auto_approve' => $this->auto_approve,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->zone_id ? 'Ad Zone Updated Successfully.' : 'Ad Zone Created Successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $zone = AdZone::findOrFail($id);
        $this->zone_id = $id;
        $this->name = $zone->name;
        $this->page_location = $zone->page_location;
        $this->price_per_day = $zone->price_per_day;
        $specs = json_decode($zone->specifications, true);
        $this->width = $specs['width'] ?? 0;
        $this->height = $specs['height'] ?? 0;
        $this->auto_approve = $zone->auto_approve;
        $this->is_active = $zone->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        AdZone::find($id)->delete();
        session()->flash('message', 'Ad Zone Deleted Successfully.');
    }
}

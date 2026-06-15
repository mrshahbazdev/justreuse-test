<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\AdTemplate;
use App\Models\AdZone;
use Livewire\WithPagination;

class AdTemplates extends Component
{
    use WithPagination;

    public $ad_zone_id, $name, $html_content, $is_active = true;
    public $template_id;
    public $isOpen = false;

    protected $rules = [
        'ad_zone_id' => 'required|exists:ad_zones,id',
        'name' => 'required|string|max:255',
        'html_content' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        return view('livewire.admin.ad-templates', [
            'templates' => AdTemplate::with('adZone:id,name')->paginate(10),
            'adZones' => AdZone::where('is_active', true)->get(),
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal() { $this->isOpen = true; }
    public function closeModal() { $this->isOpen = false; }

    private function resetInputFields()
    {
        $this->reset(['ad_zone_id', 'name', 'html_content', 'is_active', 'template_id']);
    }

    public function store()
    {
        $this->validate();

        AdTemplate::updateOrCreate(['id' => $this->template_id], [
            'ad_zone_id' => $this->ad_zone_id,
            'name' => $this->name,
            'html_content' => $this->html_content,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->template_id ? 'Ad Template Updated.' : 'Ad Template Created.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $template = AdTemplate::findOrFail($id);
        $this->template_id = $id;
        $this->ad_zone_id = $template->ad_zone_id;
        $this->name = $template->name;
        $this->html_content = $template->html_content;
        $this->is_active = $template->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        AdTemplate::find($id)->delete();
        session()->flash('message', 'Ad Template Deleted.');
    }
}

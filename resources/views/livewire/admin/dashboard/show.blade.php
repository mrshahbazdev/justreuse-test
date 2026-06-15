@if(auth()->user()->can('dashboard-list'))

<div class="p-6 border-t border-gray-200">
     
@include('livewire.admin.dashboard.cardstats')
@include('livewire.admin.dashboard.chart')        


</div>

 
@endif 


  

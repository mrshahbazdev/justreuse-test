@if(auth()->user()->can('settings-list'))
<div>
<div class="py-24 hidden">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif
            <br>
            <table class="table-auto w-full">
                <thead>
                    <tr class="border">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Setting</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">description</th>
                        @if(auth()->user()->can('settings-edit'))
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="border grid-style">
                    @foreach($settings as $row)
                    <tr align="center">
                        <td class="border-b px-4 py-2">{{$row->name}}</td>
                        <td class="border-b px-4 py-2">{{$row->description}}</td>
                        @if(auth()->user()->can('settings-edit'))
                        <td class="border-b px-4 py-2">                            
                            <button wire:click="edit('{{ $row->id }}')" class="editbutton  bg-green-500 hover:bg-orange-500 text-white font-bold py-1 px-2 text-xs rounded focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
              {{ $settings->links() }} 
            </div>           
        </div>
        
       
    </div>
    @if($cnfopen)
    @include('livewire.common.confirmation')
    @endif
</div>


<!-- tab section start -->

<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 xl:p-5 top-shadow">
            <!-- start tab -->
        <div x-data="
   {
   openTab: 1,
   activeClasses: 'bg-green-500 text-white',
   inactiveClasses: 'text-body-color hover:bg-orange-500 hover:text-white',
   }
   " class="w-full mb-14 border border-[#E4E4E4] rounded-lg">
   <div class="gap-2 xl:gap-3
      flex flex-wrap
      rounded-lg
      py-3
      px-2
      xl:px-4
      ">

      
      @foreach($settings as $row)
      <?php 
        if($row->id==$setting_id){
            $clsname = "bg-green-500 text-white";
        }else{
            $clsname = "text-body-color hover:bg-gray-100 hover:text-black";
        }
      ?>

      <button wire:click="edit('{{ $row->id }}')" class="<?php echo $clsname; ?> text-sm md:text-base font-medium rounded-md py-3 px-2 md:px-3 lg:px-4 uppercase">
      {{$row->name}}
      </button>
      @endforeach

   </div>
   
    @if ($message = Session::get('message'))
		<script>
			toastr.success("{{ $message }}");
		</script>
	@endif

   <div>
       
    @if($updateMode)
	    @include('livewire.admin.settings.app')
    @elseif($freeAdMode)
        @include('livewire.admin.settings.free-ad')
    @elseif($homeBannerMapMode)
        @include('livewire.admin.settings.home-banner-map')
    @elseif($DistanceAdMode)
        @include('livewire.admin.settings.distance-range')
    @elseif($ImageSizeAdMode)
        @include('livewire.admin.settings.image-size-settings')
    @elseif($TwilioMode)
        @include('livewire.admin.settings.twilio-sms')
    @elseif($BannerAdvertisement)
        @include('livewire.admin.settings.banner-advertisement')
    @elseif($Currency)
        @include('livewire.admin.settings.currency-rate')
    @else
        @include('livewire.admin.settings.app')
    @endif

     
   </div>
</div>

<!-- end tab -->
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
    $(".editbutton:first").click();

    });
</script>
</div>
@endif
@if(auth()->user()->can('page-list'))
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-0 py-5 top-shadow">
        @if(auth()->user()->can('page-create'))
			<div class="bg-gray-100  pl-2 pr-2 mb-2 rounded-sm mx-5">
        <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3">Create New Page</button> 
		</div>	
		@endif

        <div class="bg-gray-50  text-left  ">
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3 alert-{{Session::get('class')}}" role="alert">
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
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs text-center">Slug</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs text-center">Title</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    @foreach($list as $row)
                    <tr align="center">
                        <td class="border-b px-4 py-2">{{$row->slug}}</td>
                        <td class="border-b px-4 py-2">{{$row->title}}</td>
                        <td class="border-b px-4 py-2">
                            @if(auth()->user()->can('pages-edit'))
                            <button wire:click="edit('{{ $row->id }}')" class="bg-green-500 hover:bg-orange-500 text-white text-xs py-1 px-2 rounded-sm focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i> </button>
                            @endif
                            @if(auth()->user()->can('pages-delete'))
                            <!-- <button wire:click="deleteReq('{{ $row->id }}')" class="bg-red-500 hover:bg-red-700 text-white text-xs py-1 px-2 rounded delete focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button> -->
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
            {{ $list->links() }}
            </div>            
        </div>
		</div>
    </div>
    <style>
        .alert-error {
            background: #ffc4c4;
            border-color: palevioletred;
        }
    </style>
    @if($cnfopen)
    @include('livewire.common.confirmation')
    @endif
</div>
@endif
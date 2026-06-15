@if(auth()->user()->can('blacklist-list'))
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">        
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow px-0">            
            <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                @if(auth()->user()->can('blacklist-create'))
                    <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150">Add New Blacklist</button>
                @endif
                <!-- search -->
                <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
                    <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i></div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search Entry">
                </div>
            </div>           
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3 alert-{{Session::get('class')}}" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif
            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Type</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Entry</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs" >Action</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    @foreach($list as $row)
                    <tr align="center">
                        <td class="border-b px-4 py-2">{{$row->type}}</td>
                        <td class="border-b px-4 py-2">{{$row->entry}}</td>
                        <td class="border-b px-4 py-2">
                            @if(auth()->user()->can('blacklist-edit'))
                            <button wire:click="edit('{{ $row->id }}')" class="bg-green-500 hover:bg-orange-500 text-white py-1.5 px-3 rounded-sm text-xs focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                            @endif
                            @if(auth()->user()->can('blacklist-delete'))
                            <button wire:click="deleteReq('{{ $row->id }}')" class="bg-red-500 hover:bg-red-700 text-white py-1.5 px-3 rounded-sm delete text-xs focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
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
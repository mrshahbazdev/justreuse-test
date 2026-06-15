@if(auth()->user()->can('permission-list'))
<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg p-5 px-0">
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- search with button -->
            <div class="p-2 text-sm rounded font-bold items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 mx-2 xl:mx-5">
                @if(auth()->user()->can('permission-create'))
                 <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-3">New Permission</button> 
                @endif
                <!-- search -->
                <div class="flex flex-row flex-wrap items-center lg:ml-auto border border-gray-300 relative z-0">
                    <div class="absolute z-10 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search">
                </div>
            </div>            
            <!-- end search -->

            @if($insertMode)
            @include('livewire.admin.permission.create')
            @elseif($updateMode)
            @include('livewire.admin.permission.update')
            @endif

            <table class="table-fixed w-full">
                <thead>
                    <tr>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Name</th>
                        <!-- <th class="border-b px-4 py-2 text-left">Created at</th>
                        <th class="border-b px-4 py-2">Action</th> -->
                    </tr>
                </thead>
                <tbody class="grid-style border-l-0 border-r-0 tablelast">
                <?php $i=0;?>
                    @foreach($permission as $row)
                    <tr>
                        <td class="border-b px-4 py-2">{{ $row->name }}</td>
                        <!-- <td class="border-b px-4 py-2">{{ $row->created_at->format('d-m-Y h:i a') }}</td> -->
                        <!-- <td class="border-b px-4 py-2 text-center">
                        @if(auth()->user()->can('permission-edit'))
                        <button wire:click="edit('{{ $row->id }}')" class="bg-green-500 hover:bg-orange-500 text-white font-bold py-1 px-2 text-xs rounded focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                        @endif
                        @if(auth()->user()->can('permission-delete'))
                        <button wire:click="deleteReq('{{ $row->id }}')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 text-xs rounded delete focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
                        @endif
                        </td> -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 px-3">
            {{ $permission->links() }}
            </div>
        </div>
    </div>
    @if($cnfopen)
    @include('livewire.common.confirmation')
    @endif
</div>
@endif
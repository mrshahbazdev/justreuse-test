@if(auth()->user()->can('role-list'))
<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white top-shadow sm:rounded-lg px-0 py-5 overflow-x-auto">
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif
            @if(auth()->user()->can('role-create'))
			<div class="bg-gray-100 p-2 mb-3 mx-2 xl:mx-5">
				<button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-2">Create New Role</button>
			</div>	
			@endif
            @if($insertMode)
            @include('livewire.admin.role.create')
            @elseif($updateMode)
            @include('livewire.admin.role.update')
            @endif

            <table class="table-auto w-full tablelast">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Name</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Created_at</th>
                        @if(auth()->user()->can('role-edit') || auth()->user()->can('role-delete'))
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="grid-style border-l-0 border-r-0">
                    @foreach($roles as $role)
                    <tr align="center">
                        @if($role->name != "SuperAdmin")
                        <td class="border-b px-4 py-2">{{ $role->name }}</td>
                        <td class="border-b px-8 py-2">{{ $role->created_at->format('d-m-Y h:i a') }}</td>
                        <td class="border-b px-4 py-2">
                            @if(auth()->user()->can('role-edit'))
                            <button wire:click="edit('{{ $role->id }}')" class="bg-green-500 hover:bg-orange-500 text-white text-xs py-1 px-2 rounded-sm  focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i> </button>
                            @endif
                            @if(auth()->user()->can('role-delete'))
                            <button wire:click="deleteReq('{{ $role->id }}')" class="bg-red-700 hover:bg-red-900 text-white text-xs py-1 px-2 rounded-sm delete  focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
            
            </div>            
        </div>
    </div>
    @if($cnfopen)
    @include('livewire.common.confirmation')
    @endif
</div>
<!-- nav bar -->
@endif
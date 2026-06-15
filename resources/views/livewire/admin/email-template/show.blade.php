@if(auth()->user()->can('email-template-list'))
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg px-0 py-5 top-shadow">
            
            <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
            
            @if(auth()->user()->can('email-template-create'))
            <button wire:click="create()" class="bg-green-500 hover:bg-orange-400 text-white py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Create Email Templates</button>
            @endif
            
            </div>
            
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif
            <table class="table-auto w-full text-md">
                <thead>
                    <tr class="border border-l-0 border-r-0">

                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Subject Title</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Key</th>
						<th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Action</th>
                        
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    @foreach($settings as $row)
                    
                    <tr align="center">

                        <td class="border-b px-4 py-2">
                            {{$row->subject_title}}
                        </td>
						<td class="border-b px-4 py-2">
                            {{$row->key}}
                        </td>
                        <td class="border-b px-4 py-2">
                        @if(auth()->user()->can('email-template-edit'))
                            <button wire:click="edit('{{ $row->id }}')" class=" bg-green-500 hover:bg-orange-700 text-white py-1 px-2 rounded-sm text-xs focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                        @endif   
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
            {{ $settings->links() }} 
            </div>            
        </div>
    </div>
   
</div>
@endif
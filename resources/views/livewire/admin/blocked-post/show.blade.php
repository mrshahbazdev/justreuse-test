@if(auth()->user()->can('blocked-post-list'))
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">        
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow px-0 overflow-x-auto">            
            <div class="flex flex-wrap items-center bg-gray-100 mb-2 mx-5">
            
                <!-- search -->
            <div class="p-2 text-sm font-bold w-full w-full mx-autp items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 rounded-sm relative z-0">
                <div class="flex flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
                    <div class="absolute z-10 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search by post">
                </div>
            </div>
            <!-- end search -->
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
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Post</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Blocked By</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Blocked Date</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Un Blocked By</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Un Blocked Date</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 grid-style tablelast">
                    @foreach($data as $row)
                    <?php 
                        $blockDate = date('Y-m-d h:i A', strtotime($row->blocked_date));
                        $unblockDate = !empty($row->unblocked_date) ?date('Y-m-d h:i A', strtotime($row->unblocked_date)) : "-";
                        $getBlockedBy = App\Models\User::find($row->blocked_by);
                        $getUnBlockedBy = App\Models\User::find($row->unblocked_by);
                        $blocked_by_name = $getBlockedBy->name;
                        $blocked_by_email = $getBlockedBy->email;
                        $unblocked_by_name = !empty($getUnBlockedBy->name) ? "Name : ".$getUnBlockedBy->name : "-" ;
                        $unblocked_by_email = !empty($getUnBlockedBy->email) ? "Email : ".$getUnBlockedBy->email : "-" ;
                        $getpost = App\Models\TblPost::withTrashed()->find($row->post_id);
                    ?>
                    <tr align="center">
                    <td class="border-b px-4 py-2">
                        <p>{{$getpost->title}}</p>
                        </td>
                        <td class="border-b px-4 py-2">
                             <p>Name : {{$blocked_by_name}}</p>
                             <p>Email : {{$blocked_by_email}}</p>
                            
                        </td>
                        <td class="border-b px-4 py-2">{{$blockDate}}</td>
                        <td class="border-b px-4 py-2">
                            <p>{{$unblocked_by_name}}</p>
                            <p>{{$unblocked_by_email}}</p>
                        </td>
                        <td class="border-b px-4 py-2">{{$unblockDate}}</td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
            {{ $data->links() }}
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
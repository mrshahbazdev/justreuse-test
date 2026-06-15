@if(auth()->user()->can('coupon-list'))
<?php
$currency_symbol = App\Models\Setting::get_admin_default_currency();
?>
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg p-5 top-shadow px-0">
            <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded pb-2 xl:pb-0">
                @if(auth()->user()->can('coupon-create'))
                <?php if (!empty($currency_symbol)) { ?>
                    <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Create New Coupon</button>
                <?php } else { ?>
                    <button class="cursor-not-allowed not-allowed bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Create New Coupon</button>
                <?php } ?>
                @endif
                <!-- search -->
                <div class="flex flex-row flex-wrap items-center lg:ml-auto border border-gray-300 relative z-0">
                    <div class="absolute z-10 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-8" placeholder="Search coupon code">
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
            <br>
            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Title</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Code</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Type</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Offer</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Validate from - to</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Action</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    @foreach($list as $row)
                    <tr align="center">
                        <td class="border-b px-4 py-2">{{$row->coupon_title}}</td>
                        <td class="border-b px-4 py-2">{{$row->coupon_code}}</td>
                        <td class="border-b px-4 py-2">{{$row->type}}</td>
                        <td class="border-b px-4 py-2">
                            <?php if ($row->type == "fixed") { ?>
                                <span><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?></span> {{$row->value}}
                            <?php } else { ?>
                                {{$row->value}} <span> %</span>
                            <?php } ?>
                        </td>
                        <td class="border-b px-4 py-2">
                            <?php echo date('d M Y', strtotime($row->start_date)); ?> - <?php echo date('d M Y', strtotime($row->end_date)); ?></td>
                        <td class="border-b px-4 py-2">
                            @if(auth()->user()->can('coupon-edit'))
                            <button wire:click="edit('{{ $row->id }}')" class="bg-green-500 hover:bg-orange-500 text-white py-1 px-2 rounded-sm text-xs focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                            @endif
                            @if(auth()->user()->can('coupon-delete'))
                            <button wire:click="deleteReq('{{ $row->id }}')" class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded-sm delete text-xs focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $list->links() }}
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
    <script>
        $(document).on("click", ".not-allowed", function(e) {
            toastr.warning("Please set the default currency in the application settings!");
        });
    </script>
</div>
@endif
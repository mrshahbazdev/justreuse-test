@if(auth()->user()->can('bulk-payment-list'))
<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
        </div>
        <div class="bg-white p-5 rounded-md px-0">
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- search and delete check box -->
            <div class="p-2 text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
                    <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search, payment id or name">
                </div>
            </div>
            <!-- end search -->

            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2">Payment Details</th>
                        <th class="border-b px-4 py-2">Package</th>
                        <th class="border-b px-4 py-2">Ads Limit</th>
                        <th class="border-b px-4 py-2">User Info</th>
                        <th class="border-b px-4 py-2">Created at</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    @foreach($bulks as $row)
                    <?php
                    $posted_on = date('d M Y', strtotime($row->created_at));
                    $start_date = date('d M Y', strtotime($row->start_date));
                    $end_date = date('d M Y', strtotime($row->end_date));
                    ?>
                    <tr>
                        <td class="border-b px-4 py-2 text-left">
                            <p class="text-gray-700 text-xs pt-1"><b>Type</b>: {{$row->payment_type}}</p>
                            <p class="text-gray-700 text-xs pt-1"><b>Id:</b> {{$row->s_payment_id}}</p>
                            <p class="text-gray-700 text-xs pt-1"><b>Start Date</b>: {{$start_date}}</p>
                            <p class="text-gray-700 text-xs pt-1"><b>End Date</b>: {{$end_date}}</p>
                            <p class="text-gray-700 text-xs pt-1"><b>Validity</b>: {{$row->live_days}} Days</p>
                        </td>
                        <td class="border-b px-4 py-2 text-left">
                            <p class="text-indigo-700 font-bold text-xs pt-1">{{$row->package_name}} / {{$row->duration}} days </p>
                            <p class="text-xs pt-1">Paid: <?php echo $row->currency_hex; ?> {{ $row->package_amount }}</p>
                        </td>
                        <td class="border-b px-4 py-2 text-center">
                            {{$row->ads_limit}}
                        </td>
                        <td class="border-b px-4 py-2 text-center">
                            <p>Name : {{$row->name}}</p>
                            <p>Email : {{$row->email}}</p>
                        </td>
                        <td class="border-b px-4 py-2 text-center text-xs">{{$posted_on}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-5">
            {{ $bulks->links() }}
        </div>
    </div>
</div>
@endif
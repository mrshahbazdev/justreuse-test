<div>
  <div class=py-24>
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-16">
      <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white p-3 mb-2 rounded focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150">Back</button>
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <table class="table-auto w-full">
          <tbody class="border grid-style">
            <tr>
              <td class="border-b px-4 py-2">Web Banner</td>
              <td class="border-b px-4 py-2 text-center">
                <img src="<?php echo URL::to('/storage/' . $data->web_banner); ?>" class="m-auto w-24 h-24 object-cover rounded" />
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">App Banner</td>
              <td class="border-b px-4 py-2 text-center">
                <img src="<?php echo URL::to('/storage/' . $data->app_banner); ?>" class="m-auto w-24 h-24 object-cover rounded" />
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">Web Link</td>
              <td class="border-b px-4 py-2 text-center">
                <a class="text-indigo-700 font-bold" target="_blank" href="{{ $data->web_link }}">{{$data->web_link}}</a>
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">App Link</td>
              <td class="border-b px-4 py-2 text-center">
                <a class="text-indigo-700 font-bold" target="_blank" href="{{ $data->web_link }}">{{$data->app_link}}</a>
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">Posted By</td>
              <td class="border-b px-4 py-2 text-center">
                <p class="pt-1">Name : <b>{{$data->user_name}}</b></p>
                <p class="pt-1">Email : <b>{{$data->user_email}}</b></p>
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">Payment Info</td>
              <td class="border-b px-4 py-2 text-center">
                <p class="text-indigo-700 font-bold whitespace-nowrap">{{$data->payment_type}} / {{$data->live_days}} days </p>
                <?php
                $currency_symbol = App\Models\TblDefaultCurrency::where('id', $data->currency_id)->pluck('currency_hex')->first();
                ?>
                <p class="pt-1 font-bold whitespace-nowrap">Paid: <?php echo $currency_symbol; ?> {{ $data->total_amount }}</p>
                @if($data->page == "home")
                <p class="pt-1 whitespace-nowrap">Show ads in : <span class="font-bold">Home Page </span></p>
                @else
                <?php $category = App\Models\TblCategory::where('id', $data->category_id)->first(); ?>
                <p class="pt-1 whitespace-nowrap">Show ads in : <span class="font-bold">Search list</span> & category : <span class="font-bold">{{$category->title}}</span> </p>
                @endif
                <p class="pt-1 whitespace-nowrap">Created at : <span class="font-bold"><?php echo date('Y-m-d', strtotime($data->created_at)); ?></span></p>
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">Requested Date</td>
              <td class="border-b px-4 py-2 text-center">
                <p class="pt-1 whitespace-nowrap">Start Date : <span class="font-bold"><?php echo date('Y-m-d', strtotime($data->start_date)); ?></span> </p>
                <p class="pt-1 whitespace-nowrap">End Date : <span class="font-bold"><?php echo date('Y-m-d', strtotime($data->end_date)); ?> </span></p>
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">Approved Date</td>
              <td class="border-b px-4 py-2 text-center">
                @if(!empty($$data->approved_start_date))
                <p class="pt-1 whitespace-nowrap">Approved Start Date : <span class="font-bold"><?php echo !empty($data->approved_start_date) ? date('Y-m-d', strtotime($data->approved_start_date)) : "-"; ?></span> </p>
                <p class="pt-1 whitespace-nowrap">Approved End Date : <span class="font-bold"><?php echo !empty($data->approved_end_date) ? date('Y-m-d', strtotime($data->approved_end_date)) : "-"; ?> </span></p>
                @else
                -
                @endif
              </td>
            </tr>
            <tr>
              <td class="border-b px-4 py-2">Status</td>
              <td class="border-b px-4 py-2 text-center">{{$data->status}}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<x-admin-layout>
<div class="relative md:pt-28 pb-32 pt-12 dashboard_wrap">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3"></div>
        <div class="bg-white p-5 rounded-md">
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
            <div class="text-sm font-bold w-full w-full mx-autp items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2">

               
                <button class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-3"><a href="{{route('features-map')}}">Create Map</a></button>

              
                <button class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-3"><a href="{{route('features-order-list')}}"><i class="fa fa-bars"></i> Order Features</a></button>
              
            </div>

            <!-- end search -->

            <table class="w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-left">Id</th>
                        <th class="border-b px-4 py-2">Title</th>
                        <th class="border-b px-4 py-2">Items</th>
                        <th class="border-b px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="border border-l-0 border-r-0 border-b-0 grid-style tablelast">
                    <?php $i = 0; ?>
                    @foreach($featuremaplist as $row)
                    <?php
                    $i++;
                   
                    ?>

                    <tr>
                      <td class="border-b px-4 py-2">{{$i}}</td>
                      <td class="border-b px-4 py-2">{{$row->features_title}}</td>
                      <td class="border-b px-4 py-2">{{ \Illuminate\Support\Str::limit($row->features_items, 50, '...') }}</td>

                      <td class="border-b px-4 py-2">
                      <button class="bg-green-500 hover:bg-orange-500 text-white px-2 py-1 text-xs rounded-sm focus:outline-none focus:border-orange-500 focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><a href="{{ route('features-map-edit', $row->id) }}"><i class="far fa-edit"></i></a></button>
                     <button class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 text-xs rounded-sm focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150"><a href="{{ route('features-map-delete', $row->id) }}"><i class="far fa-trash-alt"></i></a></button>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>


    </div>



</div>

</x-admin-layout>

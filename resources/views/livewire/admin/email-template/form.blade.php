<div class="w-full mb-12 px-4  mx-20">

<div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white">
<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 w-full">

<div class="flex flex-wrap items-center">
<div class="relative w-full px-4 max-w-full flex-grow flex-1">
<h3 class="font-semibold text-base text-gray-800">&nbsp;</h3>
</div>
<div class="relative w-full px-4 max-w-full flex-grow flex-1">
<h1 class="text-center font-semibold text-lg text-gray-800 text-center uppercase">App Detail</h1>
</div>
<div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
<button class="bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150" type="button"><a href="{{URL::to('admin/dashboard')}}" >Back</a></button>
</div>
</div>




    <div class="flex flex-col mb-4 md:w-1/2">
        <label class="block text-gray-700 text-sm font-bold mb-2">User Name:</label>
        <input type="text" wire:model="to_user_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        @error('to_user_name') <span class="text-red-500">{{ $message }}</span>@enderror
    </div>




    <div class="flex flex-col mb-4 md:w-1/2">
        <label class="block text-gray-700 text-sm font-bold mb-2">To Email:</label>
        <input type="email" wire:model="to_email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        @error('to_email') <span class="text-red-500">{{ $message }}</span>@enderror
    </div>


    <div class="flex flex-col mb-4 md:w-1/2">
        <label class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
        <textarea type="text" wire:model="to_message" class="border rounded px-3 py-3 placeholder-gray-400 text-gray-700 text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" rows="4" placeholder="Write your message"></textarea>
        @error('to_message') <span class="text-red-500">{{ $message }}</span>@enderror
    </div>



       <div class="rounded-t bg-white mb-0 px-6 py-6">
              <div class="text-right">
                <button class="bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"><a href="{{URL::to('admin/dashboard')}}">Cancel</a>
                </button>

                <button wire:click.prevent="submit_mail()" class="cus_not bg-pink-500 text-white active:bg-pink-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150" type="button">
                  Update
                </button>

              </div>
            </div>



</div>
</div>

</div>


<div>
</div>


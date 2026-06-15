<div>
   <div class="px-4 md:px-10 mx-auto w-full">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		<div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
        <button class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3"><a href="{{URL::to('admin/bulk-email')}}">Back</a></button>
        </div>
		<form>
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium text-gray-900 mb-3">Edit Bulk Email: </h1>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                    <input type="text" wire:model="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="enter subject title">
                    @error('title') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>

			    <div class="flex flex-col mb-4">
					<label class="block text-gray-700 text-sm font-bold mb-2">Email code:</label>
                    <input type="text" wire:model="email_code" readonly class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="enter email code">
                    @error('email_code') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>
				
                <div class="flex flex-col mb-4">
					<label class="block text-gray-700 text-sm font-bold mb-2">Notification Message:</label>
					<textarea type="text" wire:model="notification_msg" maxlength="120" class="border rounded px-3 py-3 placeholder-gray-400 text-gray-700 text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" rows="3" placeholder="Write your notification message"></textarea>
                    @error('notification_msg') <span class="text-red-500">{{ $message }}</span>@enderror
                    <p class="text-xs font-bold item-center text-gray-800 mt-1">Character limit: 120</p>
                </div>

				<div class="flex flex-col mb-4">
					<label class="block text-gray-700 text-sm font-bold mb-2">Body: [HTML code]</label>
					<textarea type="text" wire:model="html_template" class="border rounded px-3 py-3 placeholder-gray-400 text-gray-700 text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" rows="50" placeholder="Write your email template"></textarea>
                    @error('html_template') <span class="text-red-500">{{ $message }}</span>@enderror
                    <p class="mt-1 font-semibold">Note: Don't edit this types of texts.</p><span>( [[site_logo]], [[site_name]], [[subject_title]], [[to_user_name]])</span>
                </div>
				

            </div>
            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="update()"  class="tesing inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Save
                    </button>
                </span>
            </div>
        </form>

        </div>

 </div>
    </div>

</div>
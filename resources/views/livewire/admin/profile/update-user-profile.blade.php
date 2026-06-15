<div>
   <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left" >
              <div class="mb-4">
                <h1 class="text-lg font-medium text-gray-900">Update User Profile</h1>
                  <label class="block text-gray-700 text-sm font-bold mb-2">First Name:</label>
                  <input type="text" wire:model="first_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter First-Name" wire:model="first_name">
                  @error('first_name') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Last Name:</label>
                  <input type="text" wire:model="last_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Last-Name" wire:model="last_name">
                  @error('last_name') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Address Line 1:</label>
                  <input type="text" wire:model="address_line1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Address" wire:model="address_line1">
                  @error('address_line1') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Address Line 2:</label>
                  <input type="text" wire:model="address_line2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Address" wire:model="address_line2">
                  @error('address_line2') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Phone:</label>
                  <input type="text" wire:model="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Phone" wire:model="phone">
                  @error('phone') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Date of Birth:</label>
                  <input type="date" wire:model="date_of_birth" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Date Of Birth" wire:model="date_of_birth">
                  @error('date_of_birth') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Gender:</label>
                  <ul class="flex flex-wrap content-start">                
                @foreach($gendArr as $key=>$value)
                <label class="inline-flex items-center">
                <input wire:model="gender" type="radio" class="form-radio" value="{{$key}}" <?php if($this->gender==$key){ echo "checked";}?>>
                <span class="ml-2">{{$value}}</span>
                </label>
                @endforeach
                </ul>

                  @error('gender') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>
              
      </div>

      <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
          <button wire:click.prevent="store()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
            Update
          </button>
        </span>
        </div>

    </div>
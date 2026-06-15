
    <div class=" flex items-center justify-center py-28">
     
     <form id="form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-8/12">
         <h1 class="block text-gray-700 font-bold mb-2 text-xl text-center">Update User Profile</h1>
         <br>

         <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
               <input type="text" wire:model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="enter name" wire:model="name">
               @error('name') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
               <input type="text" readonly="readonly" disabled wire:model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="enter email" wire:model="email">
               @error('email') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>
         <!-- user profile -->
         <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">First Name:</label>
               <input type="text" wire:model="first_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="{{__('p_profile.enter first name')}}" wire:model="first_name">
               @error('first_name') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Last Name:</label>
               <input type="text" wire:model="last_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="{{__('p_profile.enter last name')}}" wire:model="last_name">
               @error('last_name') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Address Line 1:</label>
               <input type="text" wire:model="address_line1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="{{__('p_profile.enter address')}}" wire:model="address_line1">
               @error('address_line1') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Address Line 2:</label>
               <input type="text" wire:model="address_line2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="{{__('p_profile.enter address')}}" wire:model="address_line2">
               @error('address_line2') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Phone:</label>
               <input type="text" wire:model="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="{{__('p_profile.enter phone')}}" wire:model="phone">
               @error('phone') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Date of Birth:</label>
               <input type="date" wire:model="date_of_birth" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" wire:model="date_of_birth">
               @error('date_of_birth') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Gender:</label>
               <ul class="flex flex-wrap content-start">                
             @foreach($gendArr as $key=>$value)
             <label class="inline-flex items-center">
             <input wire:model="gender" type="radio" class="form-radio" value="{{$key}}" <?php if($this->gender==$key){ echo "checked";}?>>
             <span class="ml-2 mr-1">{{$value}}</span>
             </label>
             @endforeach
             </ul>
               @error('gender') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>
           <?php 
           $imageUrl = URL::to('storage/'.$this->profile_photo_path); 
           
           ?>
          <img src="{{$imageUrl}}" width="70" height="70" alt="your profile"/>
           <div class="mb-4">
               <label class="block text-gray-700 text-sm font-bold mb-2">Profile Image:</label>
               <input type="file" wire:model="new_profile_photo_path" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" wire:model="date_of_birth">
               @error('new_profile_photo_path') <span class="text-red-500">{{ $message }}</span>@enderror
           </div>

         
           <div class="flex flex-wrap items-center">
              <div class="relative w-full max-w-full flex-grow flex-1 px-8">
                  <button wire:click.prevent="store()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
            Profile Update
          </button>
              </div>
              <div class="relative w-full max-w-full flex-grow flex-1 text-right px-8">
              <a href="{{ URL::to('admin/change-password') }}">
              <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
            Change Password
          </button>
              </a>
              </div>
          </div>

     </form>
     
 </div>
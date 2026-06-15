<div>
    <div class="py-4 px-4">
         <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		 <div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">     
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3">Back</button>
		</div>
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
            <h1 class="text-2xl font-medium text-gray-900 mb-3">Edit User</h1>

            <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                  <input type="text" readonly="readonly" disabled wire:model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Email">
                  @error('email') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                  <input type="text" wire:model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Email">
                  @error('name') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

            <div class="mb-4">
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
                <span class="ml-2 mr-1">{{$value}}</span>
                </label>
                @endforeach
                </ul>

                  @error('gender') <span class="text-red-500">{{ $message }}</span>@enderror
              </div>

              <!-- <div class="justify-center px-2 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              @if($this->is_blocked == 0)
              <button class="ban_user inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150" id="ban-user" data-val="{{$this->is_blocked}}">Block User</button>
               @else
               <button class="ban_user inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150" id="ban-user" data-val="{{$this->is_blocked}}">Un-block User</button>
               @endif
              </div> -->


            </div>

            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button wire:click.prevent="userUpdate()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Update
                    </button> &nbsp;
                    <button wire:click="back()" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">Cancel</button>
                </span>
            </div>

        </div>


    </div>









<script type="text/javascript">


// $.ajaxSetup({
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//           }


     
// 	   });

// $(document).ready(function () {



// 		$('#ban-user').on('click', function(e) {
//             var value = $(this).attr('data-val');
//             var id = "{{$this->user_id}}";

//     // alert(value);
// 			if(value == 0)  
//             {  
//                 var value = 1;
//                 // alert("Please select row.");  
//                 var check = confirm("You want to Block this User?");  
//                 if(check == true){  
					 
// 					$.ajax({
// 							type:'POST',
// 							dataType: 'json',
// 							url:"{{ route('user-blocked') }}",
// 							data:{blocked:value, id:id},
// 							success:function(data){
// 								 alert(data.message);
// 								//  window.location.reload();
// 							}
// 						});

//                 }
                
//             }  else { 
                
                
//                 var value = 0;
//                 // alert(value);
//                 var check = confirm("You want to Un-Block this User?");  
//                 if(check == true){  
					 
// 					$.ajax({
// 							type:'POST',
// 							dataType: 'json',
// 							url:"{{ route('user-blocked') }}",
// 							data:{blocked:value, id:id},
// 							success:function(data){
// 								 alert(data.message);
// 								 window.location.reload();
// 							}
// 						});

//                 }


//                 return false;

// 			}

//         });
	
// });


</script>

</div>
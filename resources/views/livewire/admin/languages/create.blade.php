<div>
    <div class=py-24>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-16">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-700 text-white text-xs font-bold py-2 px-4 rounded my-3">Back</button>
    <form wire:submit.prevent="store(Object.fromEntries(new FormData($event.target)))" id="my-form"> 
            <div class="bg-white px-20 pt-20 pb-20 sm:p-20 sm:pb-20 text-left">
            <h1 class="text-2xl font-medium text-gray-900">Create New Language</h1>


              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Language Name:</label>
                  <select name="language" id="language" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">-select language name-</option>
                    @foreach($languages as $lang_id => $lang_name)
                    <option class="lang_value" value="{{ $lang_name }}">{{ $lang_name }}({{$lang_id}})</option>
                    @endforeach
                    </select>
               
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Native Name:</label>
                  <input type="text" name="native" id="native" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Native Name">
               
              </div>


              <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Locale Code:</label>
                    <select name="locales" id="locales" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">-select locale code-</option>
                    @foreach($locales as $id => $field_name)
                    <option value="{{ $id }}">{{ $field_name }}({{$id}})</option>
                    @endforeach
                    </select>
              
              </div>

              <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Direction:</label>
                    <select name="direction" id="direction" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                        <option value="">-select direction-</option>
                        <option value="ltr">ltr</option>
                        <option value="rtl">rtl</option>
                    </select>
          
              </div>
              

           


            </div>

            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button type="submit" id="create_lang" class="store_data inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Save
                    </button>

                    <!-- <button type="button" id="lang_data2" class="store_data inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Save2
                    </button> -->

                </span>
            </div>
    </form>
        </div>


    </div>



</div>



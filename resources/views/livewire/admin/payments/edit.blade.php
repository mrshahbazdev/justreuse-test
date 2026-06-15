<div>
     <div class="py-4 px-4">
        <!--<div class="max-w-5xl mx-auto sm:px-6 lg:px-16">-->
		  <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
		  <div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
        <button wire:click="back()" class="bg-green-500 hover:bg-orange-700 text-white  py-2 px-4 rounded my-3">Back</button>
		</div>
	<form wire:submit.prevent="update(Object.fromEntries(new FormData($event.target)))" id="my-form">
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
            <h1 class="text-2xl font-medium text-gray-900 mb-4">Edit Language</h1>


              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Language Name:</label>
                  <select name="language" id="language" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">-select language name-</option>
                    @foreach($languages as $lang_id => $lang_name)
                    <option class="lang_value" value="{{ $lang_name }}" <?php if($data->name==$lang_name){ echo "selected";}?>>{{ $lang_name }}({{$lang_id}})</option>
                    @endforeach
                    </select>
                 
              </div>

              <div class="mb-4">
                  <label class="block text-gray-700 text-sm font-bold mb-2">Native Name:</label>
                  <input type="text" id="native" value="{{$data->native}}" name="native" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Native Name">
               
              </div>


              <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Locale Code:</label>
                    <select name="locales" id="locales" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">-select locale code-</option>
                    @foreach($locales as $id => $field_name)
                    <option value="{{ $id }}" <?php if($data->locale==$id){ echo "selected";}?>>{{ $field_name }}({{$id}})</option>
                    @endforeach
                    </select>
               
              </div>

              <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2">Direction:</label>
                    <select name="direction" id="direction" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">

                        <option value="">-select direction-</option>
                        @foreach($direction as $key => $value)
                        <option value="{{$value}}" <?php if($data->direction==$value){ echo "selected";}?>>{{$value}}</option>
                        @endforeach

                    </select>
                 <input type="hidden" name="id" value="{{$data->id}}">
              </div>
              

           


            </div>

            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <button type="submit" id="update_lang" class="store_data inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Update
                    </button>
                </span>
            </div>
    </form>
        </div>


    </div>



</div>


<script>


$("body").delegate("#update_lang","click",function(){
    // alert("hi");
	
	var language = $('#language').val();
	var native = $('#native').val();
	var locales = $('#locales').val();
	var direction = $('#direction').val();
	
			if(language == ''){
				$('#language').focus();
				return false;
			}
            if(native == '')
			{
				$('#native').focus();
					return false;
			}
			if(locales == '')
			{
				$('#locales').focus();
					return false;
			}
			if(direction == '')
			{
				$('#direction').focus();
					return false;
			}

	
});


</script>
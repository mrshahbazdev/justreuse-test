@include('admin.header')
<div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" style="z-index:100; position: fixed;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline"> 
            <form method="post" action="{{route('sublang_store')}}" id="lang_submit">
                @csrf

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4" >
                    <div class="mb-4">
                        <h1 class="text-2xl font-bold">Add sublanguage</h1>
                        <label for="exampleFormControlInput1" class="block text-gray-700 text-sm font-bold mb-2">Language Code:</label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="Language" placeholder="Enter Languagecode" name="Language_code">
                            @if('error') @endif
                            <option value="{{$locale}}"> {{$locale}}</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="exampleFormControlInput1" class="block text-gray-700 text-sm font-bold mb-2">Original Text</label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="Original_text" placeholder="Enter Original text" name="Original_text">

                    </div>

                    <div class="mb-4">
                        <label for="exampleFormControlInput1" class="block text-gray-700 text-sm font-bold mb-2">Transalate Text:</label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="Translate_text" placeholder="Enter translate text" name="Translate_text">
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Save
                        </button>
                    </span>

                    <div class="sm:flex sm:flex-row-reverse sm:space-x-3">
                   <a href="{{URL::to('/admin/sublanguage-show?locale='.$locale)}}">     <button type="button" id="cancel_video" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-linear duration-500">
Cancel
                        </button></a>
                    </div>
            </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>
<script>
    if ($("#lang_submit").length > 0) {
        $('form[id="lang_submit"]').validate({
            rules: {
                Language_code: 'required',
                Original_text: 'required',
                Translate_text: 'required',

            },
            messages: {
                Language_code: 'Language Code is required',
                Original_text: 'Please Enter Original Text',
                Translate_text: 'Please Enter Translate Text',
            },

        });
    }

  
</script>
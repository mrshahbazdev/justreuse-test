<div>
    <div class="px-4 md:px-10 mx-auto w-full">
		<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
        <button class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded my-3"><a href="{{URL::to('admin/email-template')}}">Back</a></button>
        <form action="{{url('admin/createemail-templates')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">

                <div class="flex flex-col mb-4">
                    <h1 class="text-2xl font-medium text-gray-900 mb-3">Email Template Content</h1>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Subject Title:</label>
                    <input type="text" required="required" name="subject_title" class="form-input rounded-md shadow-sm block mt-1 w-full"  autocomplete="off">
                </div>   

                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Key Word:</label>
                    <input type="text" required="required" name="key" class="form-input rounded-md shadow-sm block mt-1 w-full"  autocomplete="off">
                </div>

                <div class="flex flex-col mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Content</label>
                    <textarea id="editor" placeholder="Content" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                </div>

                <textarea class="hidden" id="mycontent" name="content"></textarea>

            </div>
            <div class="justify-center bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                   <button type="submit" class="tesing inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                        Save
                    </button>
                </span>
            </div>
        </form>

        </div>
        <script>
        const editor = CKEDITOR.replace('editor');
        CKEDITOR.config.extraPlugins = 'colorbutton';
        // config.extraPlugins = 'filebrowser';

        // config.colorButton_backStyle = {
        // element: 'table',
        // styles: { 'background-color': '#(color)' }
        // };
            
        editor.on('change', function(event){
            $("#mycontent").text(event.editor.getData());
        });

        </script>

    </div>

</div>


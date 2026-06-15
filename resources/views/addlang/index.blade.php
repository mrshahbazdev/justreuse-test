<x-admin-layout>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif
{{-- toastr --}}
@if(auth()->user()->can('postlist-list'))
<div class="relative md:pt-2 pb-32 pt-1">
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


               


                <div class="lg:flex flex-row flex-wrap items-center lg:ml-auto mt-4 lg:mt-0 mr-2">


                    <!-- <input type='button'  value={{$locale}} class='items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5' data-id=""> -->

                    <div class="lg:flex flex-row flex-wrap items-center lg:ml-auto mt-4 lg:mt-0 mr-2">
                        <select id='locale' class="lang px-4 py-2 placeholder-gray-400 text-gray-500 rounded text-sm md:text-lg shadow outline-none focus:outline-none focus:shadow-outline w-full border border-gray-300 h-12 lg:ml-4">
                        <option value="{{$locale}}">{{$locale}}</option>
                            @foreach($addlanguage as $language)

                            <option value="{{$language->locale}}">{{$language->locale}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- search -->
                <div id="search" class="md:flex hidden flex-row flex-wrap items-center border border-gray-300">
                    <div class="absolute z-50 pl-3"> </div>
                    <input type="text" class=" search px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-8" placeholder="Search by original text">
                </div>
                <div class="form-group">


                    <button class=" view_report items-center px-2 py-1 bg-green-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-red-900 focus:shadow-outline-green disabled:opacity-25 transition ease-in-out duration-150 ml-5" id="add_lang_show"><i class="far fa-edit"></i>Add SubLanguage</button>

                    <!-- <form action="{{url('add_sublang',$locale)}}" method="get" enctype="multipart/form-data" id="add">
                <button type="submit" class="btn btn-info">Add Sublanguage</button>
            </form> -->
                </div>
            </div>

            <!-- end search -->

            <table id="mytable" class="table-auto w-full text-sm">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Language</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Original text</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Translate text</th>

                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    @foreach($addlanguages as $language)
                    <tr>
                        <td id='code' class='lang_code border-b px-4 py-2 text-center' data-id={{$language->id}}>{{$language->lang_code}}</td>
                        <td id='text' class='lang_org_text border-b px-4 py-2 text-center' contenteditable='false' data-id="{{$language->id}}">{{$language->lang_org_text}}</td>
                        <td id='trans_text' class='lang_text border-b px-4 py-2 text-center' contenteditable='true' data-id="{{$language->id}}" data-code="{{$language->lang_code}}" data-key="{{$language->lang_org_text}}" data-old="{{$language->lang_text}}" >{{$language->lang_text}}</td>


                        <td class="border-b px-4 py-2 text-center">

                            <button id='delete' class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 text-xs rounded delete  focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150" data-id="{{$language->id}}"><i class="far fa-trash-alt"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="pt-5">

        </div>

        <!-- addlanguage starts-->


        <div class="fixed z-10 inset-0 overflow-y-auto" id="reports" style="display:none">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <form method="post" action="{{route('sublang_store')}}" id="lang_submit">
                    @csrf
        
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
        
                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                        role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
        
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        
                                    <h3 class="text-lg leading-6  text-gray-900 font-bold" id="modal-headline">Add Language</h3>
                                    <div class="mt-2">
                                        <label for="exampleFormControlInput1"
                                            class="block text-gray-700 text-sm font-bold mb-2">Language Code:</label>
                                        <select
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            id="Language" placeholder="Enter Languagecode" name="Language_code">
        
                                            <option value="{{$locale}}"> {{$locale}}</option>
                                        </select>
                                        <div>
        
        
                                            <div class="mt-2">
                                                <label for="exampleFormControlInput1"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Original Text</label>
                                                <input type="text"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    id="Original_text" placeholder="Enter Original text" name="Original_text">
        
                                            </div>
        
        
        
                                            <div class="mt-2">
                                                <label for="exampleFormControlInput1"
                                                    class="block text-gray-700 text-sm font-bold mb-2">Transalate Text:</label>
                                                <input type="text"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                    id="Translate_text" placeholder="Enter translate text"
                                                    name="Translate_text">
        
        
                                            </div>
        
                                            <div class="mt-5">
                                                <div class="mt-2 mt-2 float-right">
                                                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                                        <button type="submit"
                                                            class="inline-flex justify-center m-auto rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                                            Save
                                                        </button>
                                                    </span>
        
                                                </div>
        
                                                <div class="mt-2">
                                                    <button type="button" id="cancel_video"
                                                        class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-linear duration-500">
                                                        Cancel
                                                    </button>
        
                                                </div>
                                            </div>
        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        </div>
        </div>
      


 
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script>
        $(document).on('click', '.lang', function(e) {
            e.preventDefault();
            var id = $(this).val();
            //console.log(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ URL::to('sublang') }}",
                type: "POST",
                cache: false,
                data: {
                    "_token": "{{ csrf_token() }}",
                    "locale": id,
                },
                success: function(texts) {
                    // alert(texts);

                    // $('#select')[0].reset();
                    $("tbody").html("");
                    $.each(texts, function(index, value) {

                            //console.log(value);
                            var row = $("<tr>" +
                                "<td  id='code' class='lang_code border-b px-4 py-2 text-center' data-id='" + value.id + "'>" + value.lang_code + "</td>" +
                                "<td id='text' class='lang_org_text border-b px-4 py-2 text-center' contenteditable='false' data-id='" + value.id + "'>" + value.lang_org_text + "</td>" +
                                "<td id='trans_text'class='lang_text border-b px-4 py-2 text-center' contenteditable='true' data-id='" + value.id + "'>" + value.lang_text + "</td>" +
                                "<td class='border-b px-4 py-2 text-center'><button id='delete' class='bg-red-500 hover:bg-red-700 text-white px-2 py-1 text-xs rounded delete  focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150' data-id='" + value.id + "' ><i class='far fa-trash-alt'></i></button></td>" +
                                "</tr>");


                            $("tbody").append(row);

                        }

                    )
                }
            });
        });
        //no need to change for langcode

        // $(document).on('keyup click', '.lang_code', function(e) {
        //     e.preventDefault();

        //     var id = $(this).data('id');
        //     //alert(id);
        //     var currentRow = $(this).closest("tr")[0];
        //     var cells = currentRow.cells;
        //     var firstCell = cells[0].textContent;

        //     console.log(firstCell);
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });

        //     $.ajax({
        //         url: "{{route('sublang_edit')}}",
        //         type: "POST",
        //         data: {
        //             "_token": "{{ csrf_token() }}",
        //             "id": id,
        //             "lang_code": firstCell,
        //         },
        //         success: function(data) {
        //             //alert('success');
        //             //console.log('sucess');
        //         }
        //     });

        // });

        $(document).on('keyup click', '.lang_org_text', function(e) {
            console.log('qqq');
            e.preventDefault();

            var id = $(this).data('id');
            //console.log(id);
            var currentRow = $(this).closest("tr")[0];
            var cells = currentRow.cells;
            var secondCell = cells[1].textContent;
            console.log(secondCell);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{route('sublang_edit')}}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "lang_org_text": secondCell,
                },
                success: function(data) {
                    //alert('success');
                    //console.log('sucess');
                }
            });
        });


        $(document).on('keyup click', '.lang_text', function(e) {
            //console.log('zzz');
            e.preventDefault();
            var id = $(this).data('id');
            var language_code = $(this).data('code');
            var language_key = $(this).data('key');
            var old_text = $(this).data('old')
            //console.log(id);
            var currentRow = $(this).closest("tr")[0];
            var cells = currentRow.cells;
            var thirdCell = cells[2].textContent;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //console.log(thirdCell);
            $.ajax({
                url: "{{url('sublang_edit')}}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "language_code" : language_code,
                    "language_key" :language_key,
                    "old_text":old_text,
                    "lang_text": thirdCell,
                },
                success: function(data) {
                    //alert('success');
                    //console.log('sucess');
                    var suce=data.data;
                 
                 if(suce == 1){
                 toastr.success('Updated Sucessfully');
               }
                }
            });
        });



        $(document).on('click', '#delete', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var confirmation = confirm("are you sure you want to remove the item?");
            if (confirmation) {
                $.ajax({
                    url: "{{route('sublang_delete')}}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": id,
                    },
                    success: function(data) {
                        window.location.reload();
                        toastr.success(data.message);
                        setInterval(function() {

                        }, 2000);

                    }
                });
            }
        });


        $(document).ready(function() {
            $("#Close").click(function() {
                $("#track_details").toggle('hide');
                window.location.reload();
            });
        });

        // $(document).ready(function() {

        //     $("#add_lang_show").click(function() {
        //         //console.log('vvvv');
        //         $('#show_mode').show();
        //         //$('#show_mode').hide();

        //     });

        // });

        $(document).on('keyup click', '.search', function(e) {
            //console.log('qqq');

            e.preventDefault();
            var search = $("#search").find('.search').val();
            var locale = document.getElementById('locale').value;

            //console.log(locale);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ URL::to('sublang') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "locale": locale,
                    "search": search,

                },
                success: function(texts) {
                    $("tbody").html("");
                    $.each(texts, function(index, value) {

                            //console.log(value);
                            var row = $("<tr>" +
                                "<td  id='code' class='lang_code border-b px-4 py-2 text-center' data-id='" + value.id + "'>" + value.lang_code + "</td>" +
                                "<td id='text' class='lang_org_text border-b px-4 py-2 text-center' contenteditable='false' data-id='" + value.id + "'>" + value.lang_org_text + "</td>" +
                                "<td id='trans_text'class='lang_text border-b px-4 py-2 text-center' contenteditable='true' data-id='" + value.id + "'>" + value.lang_text + "</td>" +
                                "<td class='border-b px-4 py-2 text-center'><button id='delete' class='bg-red-500 hover:bg-red-700 text-white px-2 py-1 text-xs rounded delete  focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150' data-id='" + value.id + "' ><i class='far fa-trash-alt'></i></button></td>" +
                                "</tr>");


                            $("tbody").append(row);
                        }

                    )
                }
            });
        });




        $(document).on("click", ".view_report", function(e) {
            //console.log('qq');
            var id = $(this).attr('data-report-id');

            document.querySelector("#reports").style.display = "block";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: "{{url('add_sublang_store')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                success: function(data) {
                    if (data.success == true) {
                        $("#fill_comment").text(data.message);
                    }
                    toastr.success(data.message);


                }
            });
        });



</script>


<script>

    

        if ($("#lang_submit").length > 0) {
            //console.log('lll');
            $('form[id="lang_submit"]').validate({
             
                rules: {
                    Original_text: 'required',
                    Translate_text: 'required',

                },
                messages: {
                    Original_text: 'Please Enter Original Text',
                    Translate_text: 'Please Enter Translate Text',
                },
                
            });
        }
 




$(document).ready(function () {
        $("#cancel_video").click(function () {
            $("#reports").toggle('hide');
        });

    });


    </script>


@endif
</x-admin-layout>
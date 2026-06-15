<x-admin-layout>

    @if(auth()->user()->can('page-list'))
            <style>
                .alert-error {
                    background: #ffc4c4;
                    border-color: palevioletred;
                }

                .table-auto.w-full {
                    margin-left: 15%;
                    margin-right: 20%;
                    width: 85%;
                }

                .attach {

                    margin-left: 40%;
                    margin-right: 20%;
                    width: 70%;
                    display: flex;
                    gap: 30px;
                    margin-bottom: 0px;
                    align-items: center;
                    justify-content: center;
                }

                .tab-active {
                    background-color: #3490dc;
                    /* Change this to your desired active tab color */
                    color: white;
                    /* padding: 20px; */
                    border-radius: 4px;
                    /* Text color for the active tab */
                }

                /* Define a class for the hover state of the tabs */
                .tab-hover:hover {
                    background-color: #4a90e2;
                    /* Change this to your desired hover color */
                    color: white;
                    /* padding: 20px; */
                    border-radius: 4px;
                    /* Text color for the hover state */
                }
            </style>
         <!-- tab section start -->

        <div class="py-4">
            <div class="px-4 md:px-10 mx-auto w-full">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
                    <!-- start tab -->
                    <div x-data="
   {
   openTab: 1,
   
   }
   " class="w-full mb-14">
                        <div class="
      flex flex-wrap
      rounded-lg
      py-3
      px-4
      border border-[#E4E4E4]
      ">


                            <ul class=" w-full flex flex-wrap rounded-lg py-3 px-4 border border-[#E4E4E4]">
                                <li class="inline-block approved_user text-body-color text-sm md:text-base font-medium rounded-md py-3 px-2 lg:px-2" data-approved="1">
                                    <button  onclick="filterTable('approved')" class="tab-hover text-body-color hover:bg-gray-100 hover:text-black text-sm md:text-base font-medium rounded-md py-3 px-4 lg:px-6">Approved Verification</button>
                                </li>
                                <li class="inline-block approved_user text-body-color text-sm md:text-base font-medium rounded-md py-3 px-2 lg:px-2" data-approved="0">
                                    <button  onclick="filterTable('not-approved')" class="tab-hover text-body-color hover:bg-gray-100 hover:text-black text-sm md:text-base font-medium rounded-md py-3 px-4 lg:px-6">Pending Verification</button>
                                </li>
                            </ul>
                            <table class="w-full mt-4" id='select'>
                                <thead>
                                    <tr class="border">
                                        <th id="slug" class="border-b px-4 py-2">Name</th>
                                        <th id="slug" class="border-b px-4 py-2">Email</th>
                                        <th class="border-b px-4 py-2">Attachment</th>
                                        <th class="border-b px-4 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="border grid-style">
                                    <?php $i = 0; ?>
                                    @foreach($details as $row)

                                    <?php
                                    $row->document = rand();
                                    $attachments = 'Attachments';
                                    $attach_style = "bg-blue-600";

                                    $fav_style = "bg-red-600";
                                    $is_approved = "approve";
                                    $declined = "Decline";
                                    $fav_stylee = "bg-red-600";

                                    $review_style = "bg-red-600";
                                    if ($row->is_approved == "1") {
                                        $fav_style = "bg-green-500";
                                        $is_approved = "approved";
                                    }
                                    if ($row->is_approved == "0") {
                                        $fav_stylee = "bg-green-500";
                                        $declined = "Declined";
                                    }
                                    ?>
                                    <tr class="user-row {{ ($row->is_approved == 1) ? 'approved' : 'not-approved' }}">
                                        <td class="border-b px-4 py-2" style="text-align: center" ;>{{$row->name}}</td>
                                        <td class="border-b px-4 py-2" style="text-align: center" ;>{{$row->email}}</td>
                                        <td class="border-b px-4 py-2" style="text-align: center">
                                            <button class="view_review items-center px-2 py-1 border border-transparent rounded-md text-xs text-white  tracking-widest attach  focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 <?php echo $attach_style; ?>" data-val="{{$row['id']}}" data-id="{{$row['id']}}">{{$attachments}}</button></a>
                                            <div class="attach proofs_attachment" id="{{$row['id']}}">
                                            </div>

                                        </td>
                                        @if ($row->is_approved == 1)
                                        <td class="border-b px-4 py-2 text-center">
                                            <span>
                                            <i class="fa fa-check-circle text-green-500"></i>
                                           </span>
                                        </td>
                                        @else
                                        <td class="border-b px-4 py-2 text-center"><a href="{{route('admin/verification_request/approve',$row->id)}}">
                                                <button class="view_review items-center px-2 py-1 border border-transparent rounded-md text-xs text-white  tracking-widest  focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 <?php echo $fav_style; ?>" data-val="{{$row->is_approved}}" data-id="{{$row['id']}}">{{$is_approved}}</button></a>

                                        </td>
                                        @endif
                                        @if ($row->is_approved == 1)
                                        <td class="border-b px-4 py-2 text-center">
                                                <span class="delete-icon delete_request cursor-pointer" data-id="{{$row->id}}">
                                                    <i class="fa fa-trash text-red-500"></i>
                                                </span>
                                        </td>
                                        @else
                                        <td class="border-b px-4 py-2 text-center"><a href="{{route('admin/verification_request/decline',$row->id)}}">
                                                <button class="view_review items-center px-2 py-1 border border-transparent rounded-md text-xs text-white  tracking-widest  focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 <?php echo $fav_stylee; ?>" data-val="{{$row->is_approved}}" data-id="{{$row['id']}}">{{$declined}}</button></a>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $details->links() }}
                            </div>
                        </div>
                        <div>



                        </div>
                    </div>

                    <!-- end tab -->


                </div>
            </div>
        </div>
        <script type="text/javascript" src="dist/js/jquery-2.1.1.min.js"></script>

        <script>
            function filterTable(tabName) {
                const tabLinks = document.querySelectorAll('.tab-hover');

                // Remove the active class from all tab links
                tabLinks.forEach((link) => {
                    link.classList.remove('tab-active');
                });

                // Add the active class to the clicked tab link
                const clickedTab = document.querySelector(`[onclick="filterTable('${tabName}')"]`);
                clickedTab.classList.add('tab-active');

                const tableRows = document.querySelectorAll('.user-row');
                tableRows.forEach((row) => {
                    row.style.display = 'table-row'; // Display all rows by default
                    const isApproved = row.classList.contains('approved');
                    if ((tabName === 'approved' && !isApproved) || (tabName === 'not-approved' && isApproved)) {
                        row.style.display = 'none'; // Hide rows that don't match the current tab
                    }
                });
            }
        </script>


        <script>
            $(function() {
                $('.attach').click(function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var $strong = $('#' + id);
                   
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ URL::to('admin/verification_request/attachments') }}",
                        type: "POST",
                        cache: false,
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
                        },
                        success: function(attachments) {
                            $strong.html();

                            $.each(attachments, function(index, value) {
                                var icon = getAttachmentIcon(index); // Get the icon based on attachment index
                                var row = $("<div id='attachment' class='attachment cursor-pointer' data-id='" + value.id + "'>" + icon + "<i class='fa fa-download' aria-hidden='true'></i></div>");
                                $strong.append(row); // Append attachments with icons to the specific row's attach element
                            });
                        }

                    });
                });
            });

            function getAttachmentIcon(index) {
                var icons = [
                    "<img src='{{URL::to('images/goverment_proof.png')}}' title='Government Proof' alt='Government Proof' />",
                    "<img src='{{URL::to('images/Address_proof.png')}}' title='Address Proof' alt='Address Proof' />",
                    "<img src='{{URL::to('images/company_certificate.png')}}' title='Company Certificate' alt='Company Certificate' />",
                ];

                return icons[index] || "<i class='fa fa-download' aria-hidden='true'></i>"; // Default icon
            }


            $(document).on('click', '#attachment', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{('admin/download-file')}}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": id,
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },

                    success: function(blob, status, xhr) {
                        var filename = "";
                        var disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                            var matches = filenameRegex.exec(disposition);
                            if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                        }

                        if (typeof window.navigator.msSaveBlob !== 'undefined') {
                            // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                            window.navigator.msSaveBlob(blob, filename);
                        } else {
                            var URL = window.URL || window.webkitURL;
                            var downloadUrl = URL.createObjectURL(blob);

                            if (filename) {
                                // use HTML5 a[download] attribute to specify filename
                                var a = document.createElement("a");
                                // safari doesn't support this yet
                                if (typeof a.download === 'undefined') {
                                    window.location.href = downloadUrl;
                                } else {
                                    a.href = downloadUrl;
                                    a.download = filename;
                                    document.body.appendChild(a);
                                    a.click();
                                }
                            } else {
                                window.location.href = downloadUrl;
                            }

                            setTimeout(function() {
                                URL.revokeObjectURL(downloadUrl);
                            }, 100); // cleanup
                        }

                    }
                });


            });


            $(function() {
                $('.delete_request').click(function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var $rowToDelete = $(this).closest('tr');
                    var confirmed = confirm("Are you sure you want to delete this request?"); // Show a confirmation dialog
                    if (confirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '/admin/verification_request/delete/' + id,
                        type: "GET",
                        cache: false,
                      
                        success: function(attachments) {
                            toastr.success(attachments);
                            $rowToDelete.remove();
                        }

                    });
                }
                });
            });




        </script>

        @endif

</x-admin-layout>
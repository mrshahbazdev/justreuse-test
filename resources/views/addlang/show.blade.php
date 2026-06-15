<html>
<h6>nnnn</h6>
<head>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" ></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<style>
    .form-control{
        width:20%;
        margin-left: 10%;
    }
</style>

<body>
@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif


@if ($errors->any())
<div class="alert alert-danger">
    There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<a class="btn btn-primary" href="">Add Product</a>
<a class="btn btn-primary" href="">Go Back</a>


<div class="container">
    <table class="table table-bordered content">
        <tr>
            
            <th >Languageeeeee</th>
            <th >Original_text</th>
            <th >Translate_text</th>
            <th width="150px">Action</th>
        </tr>

        @foreach($texts as $text)
        <tr>
 <td id="code"class="lang_code" contenteditable="true"  data-id={{$text->id}}>{{$text->lang_code}}</td>
<td id="text"class="lang_org_text" contenteditable="true"data-id={{$text->id}}>{{$text->lang_org_text }}</td>
<td id="trans_text"class="lang_text" contenteditable="true" data-id={{$text->id}}>{{$text->lang_text }}</td>
            <td>
            <form action="{{route('sublang-delete',$text->id)}}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
            </td>
</form>
        </tr>
        @endforeach

    </table>
</div>
</body>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
   $(document).on('keyup click','.lang_code',function(e){
    e.preventDefault();
  
    var id =$(this).data('id');
    var currentRow = $(this).closest("tr")[0];
    var cells = currentRow.cells;
    var firstCell = cells[0].textContent;
    //console.log( firstCell );
    $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

    $.ajax({
  url:"{{route('sublang_edit')}}",
  type: "POST",
data:{
    "_token": "{{ csrf_token() }}",
    "id":id,
    "lang_code":firstCell,
},
success: function(data) {
//alert('success');
console.log('sucess');
}
});
});


$(document).on('keyup click','.lang_org_text',function(e){
    e.preventDefault();
    var id =$(this).data('id');
    var currentRow = $(this).closest("tr")[0];
    //alert(currentRow);
    var cells = currentRow.cells;
    var secondCell = cells[1].textContent;
    //console.log( firstCell );
    $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

    $.ajax({
  url:"{{route('sublang_edit')}}",
  type: "POST",
data:{
    "_token": "{{ csrf_token() }}",
    "id":id,
    "lang_org_text":secondCell,
},
success: function(data) {
//alert('success');
//console.log('sucess');
}
});
});



$(document).on('keyup click','.lang_text',function(e){
    e.preventDefault();
    var id =$(this).data('id');
    var currentRow = $(this).closest("tr")[0];
    //alert(currentRow);
    var cells = currentRow.cells;
    var thirdCell = cells[2].textContent;
    //console.log( firstCell );
    $.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

    $.ajax({
  url:"{{route('sublang_edit')}}",
  type: "POST",
data:{
    "_token": "{{ csrf_token() }}",
    "id":id,
    "lang_text":thirdCell,
},
success: function(data) {
//alert('success');
//console.log('sucess');
}
});
});

    	</script>

</html>

@if($insertMode)
	@include('livewire.admin.languages.create')
@elseif($updateMode)
	@include('livewire.admin.languages.edit')
@else
	@include('livewire.admin.languages.show')
@endif


<script>
$("body").delegate("#create_lang","click",function(){
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
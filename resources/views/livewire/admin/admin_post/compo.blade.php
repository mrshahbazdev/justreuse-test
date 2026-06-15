
@if($post_updateMode)
	@include('livewire.admin.admin_post.edit_post')
@elseif($user_updateMode)
	@include('livewire.admin.admin_post.edit_user')
@else
	@include('livewire.admin.admin_post.show')
@endif
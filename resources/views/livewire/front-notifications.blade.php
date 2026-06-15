<?php 
$get_meta = App\Models\TblOtherpage::get_meta('notifications');
$meta_title = (!empty($get_meta->meta_title) ? $get_meta->meta_title : "");
$meta_keywords = (!empty($get_meta->meta_key) ? $get_meta->meta_key : "");
$meta_description = (!empty($get_meta->meta_description) ? $get_meta->meta_description : "");

$dir_rtl = App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";
?>
@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
    @section('meta_title', $meta_title)
    @section('meta_keywords', $meta_keywords)
    @section('meta_description', $meta_description)
@endif

<?php 
$user = auth()->user();
$result = $user->roles[0]->name;
if($result == "User"){
?>

<div class="min-h-screen " style="background:#fffbfa;"  {{$class_dir}}>
    <div class="max-w-3xl mx-auto py-6 px-4">
        <!-- Simple Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{__('messages.read notification')}}</h1>
            <p class="text-gray-600">{{$notifications->count()}} notifications</p>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @if($notifications->count() > 0)
                @foreach($notifications as $row)
                    <?php
                    $from_user_profile = App\Models\User::where('id', $row->from_id)->first();
                    if($from_user_profile != null) {
                        $profile_image = $from_user_profile->profile_photo_path;
                        $imgUrlfinal = (!empty($profile_image) && ($profile_image != null)) ? URL::to('storage/app/public/' . $profile_image) : URL::to('storage/noimage150.png');
                    ?>
                    
                    <div class=" border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                        <div class="flex items-start gap-4">
                            <!-- Avatar -->
                            <img class="w-12 h-12 rounded-full object-cover" src="{{$imgUrlfinal}}" alt="{{$from_user_profile->name}}">
                            
                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-gray-900">
                                            <a href="{{ URL::to('seller-profile/' . $row->from_id) }}" target="_blank" class="font-medium text-gray-900 hover:text-blue-600">
                                                {{$from_user_profile->name}}
                                            </a>
                                            <span class="text-gray-700">{{$row->msg}}</span>
                                        </p>
                                        <p class="text-gray-500 text-sm mt-1">
                                            {{ \Carbon\Carbon::parse($row['created_at'])->format('M d, Y')}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php } ?>
                @endforeach
            @else
                <!-- Simple Empty State -->
                <div class="text-center py-12">
                    <div class="text-gray-400 text-4xl mb-4">🔔</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                    <p class="text-gray-600">You don't have any notifications yet.</p>
                </div>
            @endif
        </div>

        <!-- Simple Pagination -->
        @if($notifications->count() > 0)
            <div class="mt-8 flex justify-center">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<?php } else { ?>

<div class="min-h-screen bg-white flex items-center justify-center" {{$class_dir}}>
    <div class="text-center">
        <div class="text-gray-400 text-4xl mb-4">⚠️</div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Notification not available!</h3>
        <p class="text-gray-600">Please contact administrator for access.</p>
    </div>
</div>

<?php } ?>
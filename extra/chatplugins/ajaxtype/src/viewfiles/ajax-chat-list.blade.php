@extends('layouts.frontnew')
	@section('content')
	
	<!DOCTYPE html>
<html lang="en">
<head>
    <?php



		$get_meta = App\Models\TblOtherpage::get_meta('user-chats');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
		
		// only chat list blade
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		
		$class_dir_cl_padd_lr = ($dir_rtl=="true")?'pr-0 md:pr-3 lg:pr-6':'pl-0 md:pl-3 lg:pl-6';
		$class_dir_user_conv_img = ($dir_rtl=="true")?'float-right ml-2 lg:ml-4':'float-left mr-2 lg:mr-4';
		$class_dir_pl_lr = ($dir_rtl=="true")?'pr-6':'pl-6';
		
		
		// chat list blade with reload chat list blade
		
		$class_dir_user_cl_img = ($dir_rtl=="true")?'float-right ml-4 md:ml-2 lg:ml-4':'float-left mr-4 md:mr-2 lg:mr-4';
		$class_dir_ud_padd_lr = ($dir_rtl=="true")?'pl-8 md:pl-14 lg:pl-8':'pr-8 md:pr-14 lg:pr-8';
		$class_dir_rl = ($dir_rtl=="true")?'left':'right';
		
		
		
		// chat list blade with reload conversation list blade
		$class_dir_float_lr = ($dir_rtl=="true")?'float-right':'float-left';
		$class_dir_float_rl = ($dir_rtl=="true")?'float-left':'float-right';
		$class_dir_text_rl = ($dir_rtl=="true")?'text-left':'text-right';
		
		
		
		// Common
		$class_dir_lr = ($dir_rtl=="true")?'right':'left';
		$class_dir_mr_lr = ($dir_rtl=="true")?'mr-2':'ml-2';
		$class_dir_mr_rl = ($dir_rtl=="true")?'ml-2':'mr-2';
		
		
		
	?>

	@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              'primary-bg': '#fffbfa',
              'primary': { 100: '#FFEDD5', 200: '#FED7AA', 500: '#F97316', 600: '#EA580C', 700: '#C2410C' },
              'secondary': { 100: '#F1F5F9', 200: '#E2E8F0', 500: '#64748B', 800: '#1E293B' }
            }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; border: 2px solid #fffbfa; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #CBD5E1; }
        .tab-active { color: #EA580C; border-bottom: 2px solid #EA580C; }
        #options-menu { transition: opacity 0.2s ease-out, transform 0.2s ease-out; }
        .menu-hidden { opacity: 0; transform: translateY(-10px) scale(0.95); pointer-events: none; }
        .menu-visible { opacity: 1; transform: translateY(0) scale(1); }
    </style>
</head>
<body class="bg-primary-bg">

    <div class="flex h-screen antialiased text-secondary-800">
        <div class="flex flex-row h-full w-full overflow-x-hidden">

            <div id="sidebar" class="flex flex-col w-full md:w-80 lg:w-96 bg-white flex-shrink-0 border-r border-secondary-200">
                <div class="flex flex-row items-center justify-between h-12 w-full py-6 px-4">
                    <div class="flex items-center">
                         <div class="flex items-center justify-center rounded-2xl text-primary-700 bg-primary-100 h-10 w-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <div class="text-2xl font-bold ml-3 text-secondary-800">Chats</div>
                    </div>
                </div>
                <div class="mt-2 px-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-secondary-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" class="w-full py-2.5 pl-11 pr-4 bg-secondary-100 border border-transparent rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Search...">
                    </div>
                </div>
                <div id="chat-list" class="flex flex-col mt-4 -mx-2 h-full custom-scrollbar overflow-y-auto px-2">
                    
                  @foreach($chatlists as $chatlist)
                  	<?php
									$visible_posts = App\Models\TblPost::check_payment_pack_expired($chatlist->post_id);
									if(!empty($visible_posts)) {

									$sender = ((auth()->user()->id == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
									$chaturl = URL::to('/chatting') . '?to=' . $sender . '&p=' . $chatlist->post_id . '&type=old';
								?>
								<?php $lastchat = App\Models\TblChat::getLastChat($sender, $chatlist->post_id); ?>
								<?php
									$post_deleted = App\Models\TblPost::where('id', $chatlist->post_id)->whereNull('deleted_at')->pluck('id');
									$deleted_class = count($post_deleted) > 0 ? "" : "bg-red-300";
								?>
								<?php
									if ((request()->to == $sender) && (request()->p == $chatlist->post_id)) {
									$selected = "bg-gray-100";
									} else {
									$selected = "bg-white";
									}
									$post_img = App\Models\User::where('id', $sender)->pluck('profile_photo_path')->first();
									$sendername = App\Models\TblChat::getSender($sender);
                                    $unread_count = App\Models\TblChat::getUnreadCount(auth()->user()->id, $sender, $chatlist->post_id);
                                    $getLastTime = App\Models\TblChat::getTimeZoneUser($lastchat['created_at']);
									$last_time = date('h:i a', strtotime($getLastTime['converted_datetime']));
									$last_time = \Carbon\Carbon::parse($getLastTime['converted_datetime'])->format('h:i a'); //date('h:i a', strtotime($getLastTime['converted_datetime']));
								?>
                  <div class="group chat-list-item flex flex-row items-center bg-primary-100 border-l-4 border-primary-500 rounded-r-2xl p-3 cursor-pointer relative">
                        <div class="relative flex items-center justify-center h-10 w-10">
                             <img src="<?php
                                $imgPath = 'storage/' . $post_img;
                                if (!empty($post_img) && file_exists(public_path($imgPath))) {
                                    echo URL::to($imgPath);
                                } else {
                                    $name = explode(' ', trim($sendername));
                                    $initials = strtoupper(substr($name[0] ?? '', 0, 1) . substr($name[1] ?? '', 0, 1));
                                    // Generate placeholder URL with initials
                                    echo "https://placehold.co/40x40/E2E8F0/4A5568?text={$initials}";
                                }
                                ?>" class="rounded-full object-cover h-10 w-10" alt="user.jpg">
                             <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full border-2 border-primary-100 
                                <?php echo !empty($selected) ? 'bg-green-500' : 'bg-gray-400'; ?>">
                            </span>
                        </div>
                        <div class="ml-3 text-sm flex-grow">
                            <div class="font-semibold text-secondary-800"><?php echo ucwords($sendername); ?></div>
                            <div class="text-xs text-secondary-500 truncate">RE: BMW 160000</div>
                        </div>
                        <div class="flex flex-col items-end">
                             <span class="text-xs text-secondary-500 mb-1.5">{{$last_time}}</span>
                             <span class="flex items-center justify-center bg-green-500 text-white text-xs rounded-full h-5 w-5 font-bold"><?php echo $unread_count; ?></span>
                        </div>
                    </div>
                  		<?php } ?>
                    @endforeach
                  
                  
                </div>
            </div>

            <div id="main-chat" class="hidden flex-col flex-auto h-full p-2 md:p-6 md:flex">
                <div class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-white h-full shadow-lg">
                    <div class="flex items-center justify-between p-4 border-b border-secondary-100">
                        <div class="flex items-center">
                            <button id="back-to-list-btn" class="p-2 mr-2 rounded-full hover:bg-secondary-100 text-secondary-500 md:hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            </button>
                            <div class="relative flex items-center justify-center h-10 w-10">
                                <img src="https://placehold.co/40x40/E2E8F0/4A5568?text=HK" class="rounded-full object-cover h-10 w-10" alt="Hammad Kazmi">
                                <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-primary-500 border-2 border-white"></span>
                            </div>
                            <div class="flex flex-col ml-3">
                                <div class="font-semibold text-lg">Hammad Kazmi</div>
                                <span class="text-sm text-secondary-500">Discussing: BMW M5 Competition (2023)</span>
                            </div>
                        </div>
                        <div class="relative flex items-center space-x-2">
                             <button id="more-options-btn" class="p-2 rounded-full hover:bg-secondary-100 text-secondary-500 transition-colors duration-200" title="More options">
                                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                            </button>
                            <div id="options-menu" class="absolute right-0 top-12 w-56 bg-white rounded-xl shadow-lg z-20 menu-hidden border border-secondary-100">
                                <div class="py-2">
                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-secondary-800 hover:bg-secondary-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 text-secondary-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        Report User
                                    </a>
                                     <a href="#" id="block-user-btn" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                                        Block User
                                    </a>
                                    <div class="border-t border-secondary-100 my-2"></div>
                                    <a href="#" id="delete-chat-btn" class="flex items-center px-4 py-2 text-sm text-secondary-800 hover:bg-secondary-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 text-secondary-500"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        Delete Chat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="chat-container" class="flex flex-col h-full overflow-y-auto mb-4 custom-scrollbar p-4">
                        <div id="message-grid" class="flex flex-col h-full">
                            <div class="grid grid-cols-12 gap-y-2"></div>
                        </div>
                    </div>
                    
                    <div id="chat-input-container" class="flex flex-col p-4 border-t border-secondary-100">
                        <div class="flex border-b">
                            <button id="questionsTab" class="py-2 px-6 text-sm font-medium tab-active transition-colors duration-200">Questions</button>
                            <button id="offerTab" class="py-2 px-6 text-sm font-medium text-secondary-500 hover:text-secondary-800 transition-colors duration-200">Make Offer</button>
                        </div>
                        <div id="questionsContent" class="mt-4">
                            <div class="flex items-center space-x-2 mb-3 overflow-x-auto pb-2 -mx-2 px-2">
                                <button class="quick-reply-btn text-sm text-secondary-800 bg-secondary-100 hover:bg-secondary-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors duration-200">Hello</button>
                                <button class="quick-reply-btn text-sm text-secondary-800 bg-secondary-100 hover:bg-secondary-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors duration-200">Is it available?</button>
                                <button class="quick-reply-btn text-sm text-secondary-800 bg-secondary-100 hover:bg-secondary-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors duration-200">Okay</button>
                            </div>
                            <div class="flex flex-row items-center h-16 rounded-2xl bg-secondary-100 w-full px-4">
                                <div class="flex items-center space-x-1">
                                    <button class="p-2 rounded-full text-secondary-500 hover:bg-secondary-200 transition-colors duration-200" title="Upload Image"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></button>
                                    <button class="p-2 rounded-full text-secondary-500 hover:bg-secondary-200 transition-colors duration-200" title="Share Location"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></button>
                                </div>
                                <div class="flex-grow ml-2">
                                    <input id="message-input" type="text" class="flex w-full bg-transparent focus:outline-none pl-4 h-10" placeholder="Type your message..."/>
                                </div>
                                <div class="ml-3">
                                    <button id="send-btn" class="flex items-center justify-center bg-primary-500 hover:bg-primary-600 text-white px-4 py-2.5 rounded-xl flex-shrink-0 font-semibold transition-colors duration-200">
                                        <span class="mr-1.5">Send</span>
                                        <svg class="w-4 h-4 transform rotate-45 -mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="offerContent" class="hidden mt-4">
                             <div class="flex items-center space-x-2 mb-3 overflow-x-auto pb-2 -mx-2 px-2">
                                <button class="quick-offer-btn text-sm text-primary-700 bg-primary-100 hover:bg-primary-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors duration-200" data-amount="200000">€200,000</button>
                                <button class="quick-offer-btn text-sm text-primary-700 bg-primary-100 hover:bg-primary-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors duration-200" data-amount="190000">€190,000</button>
                                <button class="quick-offer-btn text-sm text-primary-700 bg-primary-100 hover:bg-primary-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors duration-200" data-amount="180000">€180,000</button>
                             </div>
                            <div class="flex flex-row items-center h-16 rounded-2xl bg-secondary-100 w-full px-4">
                                <div class="flex-grow">
                                    <input id="offer-input" type="number" class="flex w-full bg-transparent focus:outline-none pl-4 h-10" placeholder="Enter offer amount..."/>
                                </div>
                                <div class="ml-4">
                                    <button id="send-offer-btn" class="flex items-center justify-center bg-primary-500 hover:bg-primary-600 text-white px-4 py-2.5 rounded-xl flex-shrink-0 font-semibold transition-colors duration-200">
                                        <span>Send Offer</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-80 z-50 hidden flex items-center justify-center p-4 transition-opacity duration-300">
        <div class="relative bg-white p-2 rounded-lg max-w-4xl max-h-full shadow-lg">
            <button id="closeModalBtn" class="absolute -top-4 -right-4 text-white bg-red-500 hover:bg-red-600 rounded-full h-10 w-10 flex items-center justify-center text-2xl font-bold leading-none z-10">&times;</button>
            <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-[85vh] object-contain">
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const questionsTab = document.getElementById('questionsTab');
        const offerTab = document.getElementById('offerTab');
        const questionsContent = document.getElementById('questionsContent');
        const offerContent = document.getElementById('offerContent');
        const messageGrid = document.querySelector('#message-grid .grid');
        const chatContainer = document.getElementById('chat-container');
        const messageInput = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');
        const quickReplyBtns = document.querySelectorAll('.quick-reply-btn');
        const offerInput = document.getElementById('offer-input');
        const sendOfferBtn = document.getElementById('send-offer-btn');
        const quickOfferBtns = document.querySelectorAll('.quick-offer-btn');
        const moreOptionsBtn = document.getElementById('more-options-btn');
        const optionsMenu = document.getElementById('options-menu');
        const blockUserBtn = document.getElementById('block-user-btn');
        const deleteChatBtn = document.getElementById('delete-chat-btn');
        const chatInputContainer = document.getElementById('chat-input-container');
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const chatList = document.getElementById('chat-list');

        const scrollToBottom = () => { chatContainer.scrollTop = chatContainer.scrollHeight; };
        
        const addSystemMessageToChat = (message) => {
            const systemMessageHtml = `<div class="col-start-1 col-end-13 p-3 rounded-lg flex justify-center"><div class="text-xs text-secondary-500 bg-secondary-100 rounded-full py-1.5 px-4">${message}</div></div>`;
            messageGrid.innerHTML += systemMessageHtml;
            scrollToBottom();
        };

        const addMessageToChat = (message, type = 'outgoing') => {
            if (!message.trim()) return;
            let messageHtml = (type === 'outgoing') ? `<div class="col-start-4 md:col-start-6 col-end-13 p-3 rounded-lg"><div class="flex items-center justify-start flex-row-reverse"><div class="relative mr-3 text-sm bg-primary-500 text-black py-2 px-4 shadow rounded-2xl rounded-br-none"><div>${message}</div></div></div></div>` : `<div class="col-start-1 col-end-10 md:col-end-8 p-3 rounded-lg"><div class="flex flex-row items-center"><div class="flex items-center justify-center h-10 w-10 rounded-full bg-secondary-200 flex-shrink-0 text-sm font-bold">HK</div><div class="relative ml-3 text-sm bg-secondary-100 py-2 px-4 shadow rounded-2xl rounded-bl-none"><div>${message}</div></div></div></div>`;
            messageGrid.innerHTML += messageHtml;
            scrollToBottom();
        };
        
        const addImageToChat = (src, type = 'outgoing') => {
             let imageHtml = (type === 'outgoing') ? `<div class="col-start-4 md:col-start-6 col-end-13 p-3 rounded-lg"><div class="flex items-center justify-start flex-row-reverse"><div class="relative mr-3 p-1.5 bg-primary-100 shadow rounded-2xl"><img src="${src}" class="chat-image rounded-lg cursor-pointer max-w-[200px] md:max-w-xs" alt="User uploaded image"></div></div></div>` : `<div class="col-start-1 col-end-10 md:col-end-8 p-3 rounded-lg"><div class="flex flex-row items-center"><div class="flex items-center justify-center h-10 w-10 rounded-full bg-secondary-200 flex-shrink-0 text-sm font-bold">HK</div><div class="relative ml-3 p-1.5 bg-secondary-100 shadow rounded-2xl"><img src="${src}" class="chat-image rounded-lg cursor-pointer max-w-[200px] md:max-w-xs" alt="User uploaded image"></div></div></div>`;
             messageGrid.innerHTML += imageHtml;
             scrollToBottom();
        };
        
        const addOfferToChat = (amount) => {
            if (!amount || amount <= 0) return;
            const formattedAmount = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 }).format(amount);
            const sentDate = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            const offerHtml = `<div class="col-start-3 md:col-start-5 col-end-13 p-3 rounded-lg"><div class="flex items-center justify-start flex-row-reverse"><div class="w-full max-w-xs bg-white border border-secondary-200 rounded-2xl shadow-lg p-4"><div class="flex items-center justify-between pb-2 border-b border-secondary-200"><h3 class="text-md font-bold text-secondary-800">Your Offer</h3><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Pending</span></div><div class="py-4"><p class="text-4xl font-bold text-center text-primary-600">${formattedAmount}</p><p class="text-sm text-secondary-500 text-center mt-1">Sent on ${sentDate}</p></div><div class="pt-2"><button class="w-full text-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">Withdraw Offer</button></div></div></div></div>`;
            messageGrid.innerHTML += offerHtml;
            scrollToBottom();
        };

        const initialMessages = [{ type: 'incoming', text: "Welcome! Let me know if you have any questions about the car." }, { type: 'outgoing', text: "Hello! It looks great. I'd like to make an offer." }, { type: 'image', src: 'https://placehold.co/600x400/94a3b8/ffffff?text=Sample+Car+Image', userType: 'incoming' }, { type: 'offer', amount: 160000 }];
        initialMessages.forEach(msg => { if (msg.type === 'offer') addOfferToChat(msg.amount); else if (msg.type === 'image') addImageToChat(msg.src, msg.userType); else addMessageToChat(msg.text, msg.type); });

        const toggleMenu = () => { optionsMenu.classList.toggle('menu-hidden'); optionsMenu.classList.toggle('menu-visible'); };
        moreOptionsBtn.addEventListener('click', (event) => { event.stopPropagation(); toggleMenu(); });
        document.addEventListener('click', (event) => { if (!optionsMenu.contains(event.target) && !moreOptionsBtn.contains(event.target) && optionsMenu.classList.contains('menu-visible')) toggleMenu(); });
        
        blockUserBtn.addEventListener('click', (e) => { e.preventDefault(); addSystemMessageToChat('You have blocked Hammad Kazmi.'); chatInputContainer.style.display = 'none'; if (optionsMenu.classList.contains('menu-visible')) toggleMenu(); });
        deleteChatBtn.addEventListener('click', (e) => { e.preventDefault(); messageGrid.innerHTML = ''; addSystemMessageToChat('This chat has been deleted.'); chatInputContainer.style.display = 'none'; if (optionsMenu.classList.contains('menu-visible')) toggleMenu(); });

        questionsTab.addEventListener('click', () => { questionsTab.classList.add('tab-active'); offerTab.classList.remove('tab-active'); questionsContent.classList.remove('hidden'); offerContent.classList.add('hidden'); });
        offerTab.addEventListener('click', () => { offerTab.classList.add('tab-active'); questionsTab.classList.remove('tab-active'); offerContent.classList.remove('hidden'); questionsContent.classList.add('hidden'); });

        const handleSendMessage = () => { addMessageToChat(messageInput.value); messageInput.value = ''; };
        sendBtn.addEventListener('click', handleSendMessage);
        messageInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') handleSendMessage(); });
        quickReplyBtns.forEach(btn => btn.addEventListener('click', () => addMessageToChat(btn.textContent)));

        const handleSendOffer = () => { addOfferToChat(offerInput.value); offerInput.value = ''; };
        sendOfferBtn.addEventListener('click', handleSendOffer);
        offerInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') handleSendOffer(); });
        quickOfferBtns.forEach(btn => btn.addEventListener('click', () => addOfferToChat(btn.getAttribute('data-amount'))));

        messageGrid.addEventListener('click', (e) => { if (e.target.classList.contains('chat-image')) { modalImage.src = e.target.src; imageModal.classList.remove('hidden'); } });
        const hideModal = () => imageModal.classList.add('hidden');
        closeModalBtn.addEventListener('click', hideModal);
        imageModal.addEventListener('click', (e) => { if (e.target === imageModal) hideModal(); });

        chatList.addEventListener('click', (e) => { if (e.target.closest('.delete-chat-list-btn')) { e.target.closest('.group').remove(); } });

        // --- Mobile View Switching Logic ---
        const sidebar = document.getElementById('sidebar');
        const mainChat = document.getElementById('main-chat');
        const backToListBtn = document.getElementById('back-to-list-btn');

        const showChatWindow = () => {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
            mainChat.classList.remove('hidden');
            mainChat.classList.add('flex');
        };

        const showChatList = () => {
            mainChat.classList.add('hidden');
            mainChat.classList.remove('flex');
            sidebar.classList.remove('hidden');
            sidebar.classList.add('flex');
        };

        chatList.addEventListener('click', (e) => {
            if (e.target.closest('.chat-list-item')) {
                if (window.innerWidth < 768) { // md breakpoint in Tailwind
                    showChatWindow();
                }
            }
        });
        backToListBtn.addEventListener('click', showChatList);
    });
</script>

</body>
</html>
	@endsection	
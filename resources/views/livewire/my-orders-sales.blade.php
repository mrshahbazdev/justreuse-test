<div>
    {{-- Header Section --}}
    <div class="w-full float-left border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold  py-8">My Orders & Sales</h1>
        </div>
    </div>

    {{-- Tabs and Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">

            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <button wire:click="switchTab('orders')"
                           class="inline-flex items-center gap-2 py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'orders' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-shopping-bag"></i>
                            <span>My Orders ({{ $orders->total() }})</span>
                        </button>
                    </li>
                    <li class="mr-2">
                         <button wire:click="switchTab('sales')"
                           class="inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'sales' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-dollar-sign"></i>
                            <span>My Sales ({{ $sales->total() }})</span>
                        </button>
                    </li>
                </ul>
            </div>
            
            <div wire:loading.flex class="w-full justify-center items-center py-16">
                <i class="fa fa-spinner fa-spin text-green-500 text-4xl"></i>
            </div>

            <div wire:loading.remove>
                {{-- MY ORDERS TAB --}}
                @if($activeTab == 'orders')
                    <div class="space-y-6">
                        @forelse($orders as $order)
                            @php
                                $post_info = \App\Models\TblPost::where('id', $order->post_id)->withTrashed()->first();
                                $post_img = \App\Models\TblChat::getPostImgForList($order->post_id);
                                $post_url = $post_info ? \App\Models\TblPost::get_post_slug($post_info->slug) : '#';
                                $seller_info = \App\Models\User::where('id', $order->seller_id)->withTrashed()->first();
                            @endphp
                             <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">ORDER #{{ $order->orderId }}</p>
                                        <p class="text-xs text-gray-500">Placed on: {{ $order->created_at->format('d M Y') }}</p>
                                    </div>
                                    <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                                        @if($order->order_status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                        @if($order->order_status == 'delivered') bg-green-100 text-green-800 @endif
                                        @if($order->order_status == 'shipped') bg-blue-100 text-blue-800 @endif
                                        @if($order->order_status == 'processing') bg-indigo-100 text-indigo-800 @endif
                                        @if($order->order_status == 'cancelled') bg-red-100 text-red-800 @endif
                                    ">{{ $order->order_status }}</span>
                                </div>
                                <div class="p-4 md:flex items-center gap-4">
                                    <div class="flex-shrink-0 mb-4 md:mb-0">
                                        <a href="{{ $post_url }}">
                                            <img class="rounded-lg object-cover h-24 w-24 border" src="{{$post_img}}" />
                                        </a>
                                    </div>
                                    <div class="flex-grow">
                                        <a href="{{$post_url}}"><h3 class="text-lg font-semibold hover:text-green-500">{{ $post_info->title ?? 'Post Not Available' }}</h3></a>
                                        <p class="text-sm text-gray-500">Sold by: <a href="{{ url('/seller-profile/' . $order->seller_id) }}" class="text-green-600 font-medium">{{ $seller_info->name ?? 'User Deleted' }}</a></p>
                                    </div>
                                    <div class="text-right mt-4 md:mt-0">
                                        <a href="{{ url('/vieworder/' . $order->orderId) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600">View Details</a>
                                        @if($order->order_status == 'pending')
                                            <button wire:click="updateOrderStatus('{{ $order->id }}', 'cancelled')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200">Cancel</button>
                                        @endif
                                        @if($order->order_status == 'shipped')
                                            <button wire:click="updateOrderStatus('{{ $order->id }}', 'delivered')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-lg hover:bg-blue-600">Mark as Delivered</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16"><p class="text-gray-500">You have not placed any orders yet.</p></div>
                        @endforelse
                        <div class="mt-6">{{ $orders->links() }}</div>
                    </div>
                @endif

                {{-- MY SALES TAB --}}
                @if($activeTab == 'sales')
                     <div class="space-y-6">
                        @forelse($sales as $sale)
                             @php
                                $post_info = \App\Models\TblPost::where('id', $sale->post_id)->withTrashed()->first();
                                $post_img = \App\Models\TblChat::getPostImgForList($sale->post_id);
                                $post_url = $post_info ? \App\Models\TblPost::get_post_slug($post_info->slug) : '#';
                                $buyer_info = \App\Models\User::where('id', $sale->user_id)->withTrashed()->first();
                            @endphp
                             <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">SALE #{{ $sale->orderId }}</p>
                                        <p class="text-xs text-gray-500">Order from: {{ $buyer_info->name ?? 'User Deleted' }}</p>
                                    </div>
                                     <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                                        @if($sale->order_status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                        @if($sale->order_status == 'delivered') bg-green-100 text-green-800 @endif
                                        @if($sale->order_status == 'shipped') bg-blue-100 text-blue-800 @endif
                                        @if($sale->order_status == 'processing') bg-indigo-100 text-indigo-800 @endif
                                        @if($sale->order_status == 'cancelled') bg-red-100 text-red-800 @endif
                                    ">{{ $sale->order_status }}</span>
                                </div>
                                <div class="p-4 md:flex items-center gap-4">
                                    <div class="flex-shrink-0 mb-4 md:mb-0">
                                        <a href="{{ $post_url }}">
                                            <img class="rounded-lg object-cover h-24 w-24 border" src="{{$post_img}}" />
                                        </a>
                                    </div>
                                    <div class="flex-grow">
                                        <a href="{{$post_url}}"><h3 class="text-lg font-semibold hover:text-green-500">{{ $post_info->title ?? 'Post Not Available' }}</h3></a>
                                        <p class="text-sm text-gray-500">Order date: {{ $sale->created_at->format('d M Y') }}</p>
                                    </div>
                                    <div class="text-right mt-4 md:mt-0">
                                        <a href="{{ url('/vieworder/' . $sale->orderId) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600">View Details</a>
                                        @if($sale->order_status == 'pending')
                                            <button wire:click="updateOrderStatus('{{ $sale->id }}', 'processing')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600">Mark as Processing</button>
                                        @endif
                                        @if($sale->order_status == 'processing')
                                            <button wire:click="openShippingModal('{{ $sale->id }}')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-lg hover:bg-indigo-600">Mark as Shipped</button>
                                        @endif
                                         @if($sale->order_status == 'shipped')
                                            <button wire:click="openShippingModal('{{ $sale->id }}')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-lg hover:bg-blue-600">Edit Tracking</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16"><p class="text-gray-500">You have not made any sales yet.</p></div>
                        @endforelse
                        <div class="mt-6">{{ $sales->links() }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Shipping Details Modal --}}
    <div x-data="{ show: @entangle('showShippingModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen">
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div x-show="show" x-transition class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                <form wire:submit.prevent="saveShippingDetails">
                    <div class="bg-white px-6 py-4">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Shipping Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Shipment Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model.defer="shipping_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('shipping_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Courier Name <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="courier_name" placeholder="e.g., DHL, FedEx" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                 @error('courier_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Service Type <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="courier_service" placeholder="e.g., Express, Standard" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                 @error('courier_service') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Tracking ID <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="tracking_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('tracking_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                <textarea wire:model.defer="more_info" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">Save & Mark as Shipped</button>
                        <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Toaster --}}
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;"><p id="toast-message"></p></div>
    <script>
        document.addEventListener('livewire:load', function () {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            let toastTimeout;
            window.addEventListener('show-toast', event => {
                if (toastTimeout) clearTimeout(toastTimeout);
                toastMessage.innerText = event.detail.message;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 10);
                toastTimeout = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(0.5rem)';
                    setTimeout(() => { toast.style.display = 'none'; }, 300);
                }, 3000);
            });
        });
    </script>
  <style>
  	
        
        
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            background-color: #fffbfa;
            color: #0f172a;
            min-height: 100vh;
        }
        
        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
        }

        /* Footer */
        footer {
            border-top: 1px solid #e2e8f0;
            
            margin-top: auto;
            width: 100%;
        }
        
  </style>
</div>

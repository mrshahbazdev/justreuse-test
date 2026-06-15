<?php

namespace App\Http\Livewire;

use App\Models\TblBuynowOrder;
use App\Models\TblCourierInfo;
use App\Models\TblPost;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrdersSales extends Component
{
    use WithPagination;

    public $activeTab = 'orders';
    public $search = '';

    // Modal State
    public $showShippingModal = false;
    public $orderToShipId;

    // Shipping Form Fields
    public $shipping_date;
    public $courier_name;
    public $courier_service;
    public $tracking_id;
    public $more_info;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Check URL for initial tab
        if (request()->segment(2) === 'sales') {
            $this->activeTab = 'sales';
        } else {
            $this->activeTab = 'orders';
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = TblBuynowOrder::find($orderId);

        if (!$order) return;

        // Add authorization check if needed
        // For example: if($order->seller_id !== Auth::id()) return;

        $order->order_status = $status;
        $order->save();
        
        // You can add your notification logic here
        
        $this->dispatchBrowserEvent('show-toast', ['message' => "Order status updated to {$status}."]);
    }

    public function openShippingModal($orderId)
    {
        $this->resetValidation();
        $this->reset(['shipping_date', 'courier_name', 'courier_service', 'tracking_id', 'more_info']);

        $this->orderToShipId = $orderId;

        // Pre-fill form if editing
        $courierInfo = TblCourierInfo::where('order_id', $orderId)->first();
        if ($courierInfo) {
            $this->shipping_date = $courierInfo->shipping_date;
            $this->courier_name = $courierInfo->courier_name;
            $this->courier_service = $courierInfo->courier_service;
            $this->tracking_id = $courierInfo->tracking_id;
            $this->more_info = $courierInfo->more_info;
        }

        $this->showShippingModal = true;
    }

    public function saveShippingDetails()
    {
        $this->validate([
            'shipping_date' => 'required|date',
            'courier_name' => 'required|string|max:255',
            'courier_service' => 'required|string|max:255',
            'tracking_id' => 'required|string|max:255',
        ]);

        TblCourierInfo::updateOrCreate(
            ['order_id' => $this->orderToShipId],
            [
                'shipping_date' => $this->shipping_date,
                'courier_name' => $this->courier_name,
                'courier_service' => $this->courier_service,
                'tracking_id' => $this->tracking_id,
                'more_info' => $this->more_info,
            ]
        );

        $this->updateOrderStatus($this->orderToShipId, 'shipped');
        
        $this->showShippingModal = false;
        $this->dispatchBrowserEvent('show-toast', ['message' => 'Shipping details saved and order marked as shipped.']);
    }


    public function render()
    {
        $userId = Auth::id();

        $orders = TblBuynowOrder::where('user_id', $userId)->orderBy('created_at', 'desc')->paginate(10, ['*'], 'ordersPage');
        $sales = TblBuynowOrder::where('seller_id', $userId)->orderBy('created_at', 'desc')->paginate(10, ['*'], 'salesPage');

        return view('livewire.my-orders-sales', [
            'orders' => $orders,
            'sales' => $sales,
        ]);
    }
}

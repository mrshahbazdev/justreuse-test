<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblPayment;
use App\Models\TblBuynowOrder;
use App\Models\Package;
use App\Models\TblExchangedPost;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage; // File operations ke liye behtar

class PostComponent extends Component
{
    use WithPagination;

    public $search;
    public $pid;

    // --- Naye Properties ---
    public $selectedPosts = []; // Multiple delete ke liye selected post IDs store karega
    public $selectAll = false; // "Select All" checkbox ki state ke liye
	public $showMultiDeleteModal = false;
    public $showDeleteModal = false; // Delete confirmation modal ko show/hide karne ke liye
    public $postToDeleteId = null; // Jis post ko delete karna hai uski ID store karega
	public $showSoldModal = false;
    public $postForStatusChange = null;
    public $newStatusForPost = null;
    public function confirmMultiDelete()
    {
        $this->showMultiDeleteModal = true;
    }
  	
    public function deleteSelected()
    {
        if(count($this->selectedPosts) > 0) {
            
            TblPost::whereIn('id', $this->selectedPosts)->delete(); 

            // Success message
            $this->dispatchBrowserEvent('show-toast', ['type' => 'success', 'message' => count($this->selectedPosts) . ' posts deleted successfully!']);

            
            $this->selectedPosts = [];
            $this->selectAll = false;

            $this->showMultiDeleteModal = false; 

        } else {
             $this->dispatchBrowserEvent('show-toast', ['type' => 'warning', 'message' => 'Please select posts to delete.']);
        }
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
	public function confirmSoldStatusChange($postId, $newStatus)
    {
        $this->postForStatusChange = $postId;
        $this->newStatusForPost = $newStatus;
        $this->showSoldModal = true;
    }
    public function updatingPid()
    {
        $this->resetPage();
    }

    // "Select All" ki functionality
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Agar "Select All" check hai, to current page ki sari post IDs ko select kar lein
            $this->selectedPosts = TblPost::where('user_id', Auth::id())->pluck('id')->map(fn ($id) => (string) $id);
        } else {
            // Warna khali kar dein
            $this->selectedPosts = [];
        }
    }

    public function render()
    { 
        // Aapka render logic bilkul aesa hi rahega
        if (!empty($this->pid) && $this->pid != "free") {
            $visible_posts = TblPost::select("tbl_posts.*")
                ->where('tbl_posts.user_id', Auth::id())
                ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
                ->whereNull('tbl_posts.deleted_at')
                ->where('tbl_posts.active', 1)
                ->Join("tbl_payments", function ($join) {
                    $join->on("tbl_payments.post_id", "=", "tbl_posts.id")
                        ->where("tbl_payments.user_id", "=", Auth::id())
                        ->where("tbl_payments.package_id", "=", $this->pid)
                        ->where("tbl_payments.active", "=", '1')
                        ->whereDate("tbl_payments.end_date", ">=", date("Y-m-d"));
                })
                ->paginate(20);
        } elseif (!empty($this->pid) && $this->pid == "free") {
            $visible_posts = TblPost::select("tbl_posts.*")
                ->where('tbl_posts.user_id', Auth::id())
                ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
                ->whereNull('tbl_posts.deleted_at')
                ->where('tbl_posts.active', 1)
                ->orderBy('tbl_posts.created_at', 'desc')
                ->Join("tbl_posted_ad_package_infos", function ($join) {
                    $join->on("tbl_posted_ad_package_infos.post_id", "=", "tbl_posts.id")
                        ->where("tbl_posted_ad_package_infos.user_id", "=", Auth::id())
                        ->where("tbl_posted_ad_package_infos.active", "=", '1');
                })
                ->paginate(20);
        } else {
            $visible_posts = TblPost::where('title', 'like', '%' . $this->search . '%')
                ->where('user_id', Auth::id())
                ->whereNull('deleted_at')
                ->where('active', 1)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        $packages_list = Package::where('active', '1')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'asc')
            ->get();

        // Agar sari posts select ho jayein to "Select All" checkbox ko update karein
        $this->selectAll = count($this->selectedPosts) === $visible_posts->count();

        return view('livewire.post.show', ['list' => $visible_posts, 'packages_list' => !empty($packages_list) ? $packages_list : ""]);
    }


    // Is function ko is naye code se replace karein
      public function toggleSoldStatus()
      {
          if ($this->postForStatusChange !== null) {
              $post = TblPost::find($this->postForStatusChange);
              $status = $this->newStatusForPost;

              if ($post) {
                  $post->sold_status = $status;
                  $post->save();

                  $message = $status == 1 ? 'Post marked as sold!' : 'Post is back on sale!';
                  $this->dispatchBrowserEvent('show-toast', ['type' => 'success', 'message' => $message]);
              }

              // State ko reset karein aur modal band karein
              $this->showSoldModal = false;
              $this->postForStatusChange = null;
              $this->newStatusForPost = null;
          }
      }

    // Step 1: Delete ke liye confirmation modal open karega
    public function confirmDelete($id)
    {
        $this->postToDeleteId = $id;
        $this->showDeleteModal = true;
    }

    // Step 2: Asal delete operation yahan hoga
    public function destroy()
    {
        if ($this->postToDeleteId) {
            $id = $this->postToDeleteId;

            $incomming = TblExchangedPost::where('post_owner_id', Auth::id())->where('post_id', $id)->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'accepted');
            })->exists(); // exists() behtar performance deta hai

            $check_buynow_order = TblBuynowOrder::where('post_id', $id)->where('order_status', '!=', 'delivered')->exists();

            if ($check_buynow_order) {
                $this->dispatchBrowserEvent('show-toast', ['type' => 'error', 'message' => 'Before proceed to delete, please cancel all your sale orders!']);
                $this->showDeleteModal = false; // Modal band kar dein
                return;
            }

            if ($incomming) {
                $this->dispatchBrowserEvent('show-toast', ['type' => 'error', 'message' => 'Before proceed to delete, please cancel all your post incoming exchanges!']);
                $this->showDeleteModal = false; // Modal band kar dein
                return;
            }

            // Delete process start
            $post = TblPost::find($id);
            if ($post) {
                // Image delete logic (behtar tareeqe se)
                if (!empty($post->images)) {
                    $images = explode(',', $post->images);
                    foreach ($images as $imagePath) {
                        // Assuming your files are in the public disk
                        // Path example: adpost/predefined/your_image.jpg
                        Storage::disk('public')->delete($imagePath);
                        Storage::disk('public')->delete(str_replace('predefined', 'predefined/normal', $imagePath));
                        Storage::disk('public')->delete(str_replace('predefined', 'predefined/list', $imagePath));
                        Storage::disk('public')->delete(str_replace('predefined', 'applist', $imagePath));
                        Storage::disk('public')->delete(str_replace('predefined', 'appdetail', $imagePath));
                    }
                }

                $post->delete(); // Soft delete ya hard delete, model pe depend karta hai
                TblPostValue::where('post_id', $id)->update(['active' => 0]);
                
                // Success message
                $this->dispatchBrowserEvent('show-toast', ['type' => 'success', 'message' => 'Post deleted successfully!']);
            }
        }
        
        // Modal ko reset aur band kar dein
        $this->showDeleteModal = false;
        $this->postToDeleteId = null;
    }

    
}
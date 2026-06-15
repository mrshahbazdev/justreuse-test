<?php 
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TblPost;
use App\Models\TblPostValue;
use Illuminate\Support\Facades\URL;

class Userdelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userdelete:cron';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user delete description';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");
        $before_oneweek = date('Y-m-d', strtotime("-7 days"));
        $blockeduser = User::onlyTrashed()
        ->where('is_blocked', 1)
        ->whereDate('deleted_at', '<', $before_oneweek)
        ->get();  
        $output = "There are no blocked users.";
        if(!empty($blockeduser)){
            foreach($blockeduser as $blockusers){
                $user_id = $blockusers['id'];
                $users_post = TblPost::where('user_id',$user_id)->get();
                if (count($users_post) > 0) {
                    foreach ($users_post as $post) {
                        // image delete
                        if (!empty($post->images)) {
                            $url = URL::to('storage/') . '/';
                            $old_img = str_replace($url, '', $post->images);
                            /* remove the deleted image from the storage folder start */
                            if (strpos($post->images, 'applist') !== false) {
                                $unmatched_img_name = str_replace("adpost/applist/", '', $old_img);
                            } else {
                                $unmatched_img_name = str_replace("adpost/predefined/", '', $old_img);
                            }
                            /* remove web normal img file */
                            if (is_file(base_path() . '/storage/app/public/adpost/predefined/normal/' . $unmatched_img_name)) {
                                $path = base_path() . '/storage/app/public/adpost/predefined/normal/' . $unmatched_img_name;
                                unlink($path);
                            }
                            /* remove web list img file */
                            if (is_file(base_path() . '/storage/app/public/adpost/predefined/list/' . $unmatched_img_name)) {
                                $path = base_path() . '/storage/app/public/adpost/predefined/list/' . $unmatched_img_name;
                                unlink($path);
                            }
                            /* remove web image file */
                            if (is_file(base_path() . '/storage/app/public/adpost/predefined/' . $unmatched_img_name)) {
                                $path = base_path() . '/storage/app/public/adpost/predefined/' . $unmatched_img_name;
                                unlink($path);
                            }
                            /* remove image file from app list folder */
                            if (is_file(base_path() . '/storage/app/public/adpost/applist/' . $unmatched_img_name)) {
                                $app_list = base_path() . '/storage/app/public/adpost/applist/' . $unmatched_img_name;
                                unlink($app_list);
                            }
                            /* remove image file from app detail folder */
                            if (is_file(base_path() . '/storage/app/public/adpost/appdetail/' . $unmatched_img_name)) {
                                $app_detail = base_path() . '/storage/app/public/adpost/appdetail/' . $unmatched_img_name;
                                unlink($app_detail);
                            }
                            $post_imgs = TblPost::where('id', $post->id)->first();
                            $array = explode(',', $post_imgs['images']);
                            $array = array_map(function ($value) {
                                return str_replace("adpost/predefined/", '', $value);
                            }, $array);
                            $array = \array_diff($array, [$unmatched_img_name]);
                            $data = array();
                            $newimg = array();
                            foreach ($array as $img) {
                                if (!empty($img)) {
                                    $newimg[] = "adpost/predefined/" . $img;
                                    $data[] = URL::to('storage/adpost/predefined/' . $img);
                                }
                            }
                            $final_imgs = !empty($newimg) ? implode(',', $newimg) : '';
                            $post_imgs->update([
                                'images' => $final_imgs
                            ]);
                        }
                        // image delete
                        $delete_post =  TblPost::where('id', $post->id)->forceDelete();
                        $delete_post_val = TblPostValue::where('post_id', $post->id)->forceDelete();
                        $delete_user =  User::where('id', $user_id)->forceDelete();
                    }
                    $output .= "Posts deleted successfully.<br>";
                } else {
                    $output .= "Posts already deleted.<br>";
                }
            }
        }else{
            $output .= "There are no blocked users.";
        }
        return $output;
        /*
           Write your database logic we bellow:
           Item::create(['name'=>'hello new']);
        */
    }
}

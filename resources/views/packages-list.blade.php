<div class="relative mt-4 mb-16">     
  <h1 class="text-center font-bold text-4xl p-3">Packages</h1>
  <p class="text-center">The premium package help sellers to promote their products or services by giving more visibility to their ads to attract more buyers and sell faster.</p>
  <div class="flex flex-wrap mt-2 mb-2 w-10/12 list">
        @foreach($list as $row)
          <div class="w-full md:w-6/12 lg:w-4/12 lg:mb-0 mb-12 px-4 py-2 list">            
               <div class="rounded-t-lg rounded-tr-none bg-white mt-4 border-2 border-gray-300 shadow-xl shadow-md">
                   <div class="p-3 rounded-t-md rounded-l-md text-2xl border-0 text-white font-bold text-center bg-indigo-500">
                    {{ $row->name }}
                   </div>
                   <div class="text-center p-4">
                    <h1 class="text-xl text-black font-bold">
                      <?php echo "Rs. ".round($row->price); ?>
                    <span class="text-gray font-sm"> / Ad</span>
                    </h1>
                  </div>
                  <div class="border-0 border-grey-light border-t border-solid text-sm">
                      <div class="text-center border-0 border-grey-light border-b border-solid py-4">
                          Availability <b>{{ $row->duration }} days</b>
                      </div>
                      <div class="text-center border-0 border-grey-light border-b border-solid py-4">
                           Promo Duration <b>{{ $row->promo_duration }} days</b>
                      </div>
                  </div>
                  <div class="text-center mt-8 mb-8 mt-auto"></div>
                 </div>          
          </div>    
        @endforeach 
  </div>
</div>
<style>
.list {
    margin: auto;
    margin-top: 17px !important;
}
</style>
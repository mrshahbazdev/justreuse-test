<main>
	    <section class="bg-gradient-to-b from-orange-50 to-white">
	      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12">
	        <div class="mx-auto max-w-3xl text-center reveal">
	          <h1 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900">
	            Find Great Deals. <span class="text-primary">Buy & Sell</span> Smart.
	          </h1>
	          <p class="mt-4 text-slate-600 text-lg">
	            Search local listings or post your own quick, safe, and hassle-free.
	          </p>
	        </div>

	        <div class="mt-8 mx-auto max-w-3xl">
	          <div class="bg-white rounded-2xl border border-slate-200 smooth-shadow">
	            <div class="flex flex-col md:flex-row">
	              <div class="flex-1 flex items-center gap-3 px-4 py-3">
	                <i class="fa-solid fa-magnifying-glass text-slate-400 text-lg"></i>
	                <input id="q" type="text" placeholder="What are you looking for?" class="w-full bg-transparent text-slate-800 placeholder-slate-400 focus:outline-none text-base">
	              </div>
	              <div class="h-px md:h-auto md:w-px bg-slate-200"></div>
	              <div class="flex-1 flex items-center gap-3 px-4 py-3">
	                <i class="fa-solid fa-location-dot text-slate-400 text-lg"></i>
	                <input id="location" type="text" placeholder="Location" class="w-full bg-transparent text-slate-800 placeholder-slate-400 focus:outline-none text-base">
	                <button id="geoBtn" class="text-primary hover:text-primary-dark" title="Detect location"><i class="fa-solid fa-crosshairs"></i></button>
	              </div>
	              <button id="search" class="md:w-auto w-full bg-primary text-white px-6 py-3 font-bold rounded-b-2xl md:rounded-bl-none md:rounded-r-2xl hover:bg-primary-dark transition">
	                Search
	              </button>
	            </div>
	            <div id="suggestions" class="hidden border-t border-slate-200">
	            </div>
	          </div>
	        </div>
	      </div>
	    </section>

	    <section class="py-14">
	      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
	        <div class="text-center reveal">
	          <h2 class="text-3xl font-extrabold text-slate-900">Browse by Category</h2>
	          <p class="mt-2 text-slate-600">Find what you need, faster.</p>
	        </div>

	        <div class="mt-10 relative">
	          <div class="swiper category-swiper">
	            <div class="swiper-wrapper">
	            <?php

						//Note: while load first time, unable to get session *Searchedurl* url
						//so the below condition has been updated
						$searchUrl = "";
						if (Session::has('Searchedurl')) {
							$searchUrl = Session::get('Searchedurl');
						} else {
							$country_name = App\Models\Setting::get_admin_default_country();
							$searchUrl = URL::to('/' . str_replace(' ', '_', strtolower($country_name)) . "?loc=" . $country_name . "&country=" . $country_name);
						}
						?>
	            	@foreach($main_categories as $main_category)
					<?php
						$cat_img = !empty($main_category->image) ? URL::to('storage') . '/' . $main_category->image : URL::to('storage/noimage150.png');
						//dd($cat_img);
					?>
	              <a href="<?php echo $searchUrl . "&c=" . $main_category->slug; ?>" class="swiper-slide reveal lift rounded-xl border border-slate-200 bg-white p-5 text-center hover:border-primary">
	                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-primary/10 text-primary" ><img class="w-10 h-10 object-cover inline-block lazyload" width="24" height="24" src="{{$cat_img}}" data-src="{{$cat_img}}" alt="{{$main_category->title}}"></div>
	                <h3 class="font-bold text-slate-900"><?php echo $main_category->title; ?></h3>
	              </a>
	            @endforeach
	            </div>
	          </div>
	          <div class="swiper-button-prev category-prev category-nav hidden md:flex"></div>
	          <div class="swiper-button-next category-next category-nav hidden md:flex"></div>
	        </div>
	      </div>
	    </section>

	    <section class="py-14 bg-white relative overflow-hidden">
	      <div class="absolute top-0 -left-1/4 w-3/4 h-3/4 bg-gradient-to-tr from-orange-100 to-rose-100 rounded-full opacity-30 blur-3xl -z-10"></div>

	      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
	        <div class="text-center reveal">
	          <h2 class="text-3xl font-extrabold text-slate-900">Explore Our Listings</h2>
	          <div id="tabs" class="mt-6 inline-flex gap-2 rounded-full bg-slate-100 p-1">
	            <button class="tab-btn font-semibold px-4 py-2 rounded-full text-slate-600 data-[active=true]:bg-white data-[active=true]:shadow data-[active=true]:text-slate-900 transition-colors"
	                    data-tab="featured" data-active="true">{{__('landing.feature ads')}}</button>
	            <button class="tab-btn font-semibold px-4 py-2 rounded-full text-slate-600 data-[active=true]:bg-white data-[active=true]:shadow data-[active=true]:text-slate-900 transition-colors"
	                    data-tab="popular" data-active="false">{{__('landing.top ads')}}</button>
	            <!-- <button class="tab-btn font-semibold px-4 py-2 rounded-full text-slate-600 data-[active=true]:bg-white data-[active=true]:shadow data-[active=true]:text-slate-900 transition-colors"
	                    data-tab="latest" data-active="false">Latest</button> -->
	          </div>
	        </div>

	        <div id="tab-content" class="mt-10">
	          <template id="adCardTpl">
	            <div class="bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-primary transition lift">
	              <div class="relative">
	                <img src="https://placehold.co/600x400/e2e8f0/334?text=Ad" alt="Ad" class="w-full h-48 object-cover"/>
	                <span class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-black/60 text-white text-xs px-2 py-1">
	                  <i class="fa-solid fa-check-circle text-emerald-400"></i> Verified
	                </span>
	              </div>
	              <div class="p-4">
	                <h3 class="font-bold text-slate-900 line-clamp-1 ad-title">Ad Title</h3>
	                <p class="text-primary font-extrabold text-xl mt-1 ad-price">Price</p>
	                <div class="mt-3 flex items-center justify-between text-sm text-slate-500">
	                  <span class="ad-loc"><i class="fa-solid fa-location-dot mr-1"></i>Location</span>
	                  <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 transition-colors hover:bg-slate-50">
	                    <i class="fa-regular fa-eye"></i> View
	                  </button>
	                </div>
	              </div>
	            </div>
	          </template>

	          <div id="featured" class="tab-pane grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
	          	
	             	@foreach($featurs_ad_list as $featurs_ad)
	            		<?php echo $k = App\Models\Setting::htmlAdBlock($featurs_ad['id']); ?>
	            	@endforeach
	            
	          </div>
	          <div id="popular" class="tab-pane grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6  hidden">
	          		<!-- <div class="col-span-full flex flex-col items-center justify-center text-center bg-slate-50 rounded-xl p-8 md:p-12">
	                    <div class="w-20 h-20 mb-4 rounded-full bg-primary/10 text-primary flex items-center justify-center">
	                        <i class="fa-solid fa-folder-open text-4xl"></i>
	                    </div>
	                    <h3 class="text-2xl font-bold text-slate-800"></h3>
	                   
	                </div>  -->
	             	@foreach($top_ads_list as $top_ad_list)
			            <?php echo $k = App\Models\Setting::htmlAdBlock($top_ad_list['id']); ?>
	          		@endforeach
	          </div>
	          <!-- <div id="latest" class="tab-pane grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6  hidden">
	            <div class="bg-white rounded-xl overflow-hidden border border-slate-200 hover:border-primary transition lift">
	              <div class="relative">
	                <img src="https://placehold.co/600x400/e2e8f0/334?text=Ad" alt="Ad" class="w-full h-48 object-cover">
	                <span class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-black/60 text-white text-xs px-2 py-1">
	                  <i class="fa-solid fa-check-circle text-emerald-400"></i> Verified
	                </span>
	              </div>
	              <div class="p-4">
	                <h3 class="font-bold text-slate-900 line-clamp-1 ad-title">Used Books Collection</h3>
	                <p class="text-primary font-extrabold text-xl mt-1 ad-price">Rs. 5,000</p>
	                <div class="mt-3 flex items-center justify-between text-sm text-slate-500">
	                  <span class="ad-loc"><i class="fa-solid fa-location-dot mr-1"></i>Multan</span>
	                  <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-slate-200 transition-colors hover:bg-slate-50">
	                    <i class="fa-regular fa-eye"></i> View
	                  </button>
	                </div>
	              </div>
	            </div>
	          </div> -->
	        </div>
	      </div>
	    </section>
	    
	 
	    
	    <section class="py-14 lg:-mt-20">
	      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
	        <div class="grid md:grid-cols-2 gap-8 items-center rounded-2xl bg-primary text-white p-8 md:p-12 smooth-shadow overflow-hidden relative">
	          <div class="reveal">
	            <h2 class="text-3xl md:text-4xl font-extrabold">Get the JustReused App</h2>
	            <p class="mt-3 text-white/90">Manage your ads, chat with buyers, and discover deals on the go.</p>
	            <div class="mt-6 flex flex-col sm:flex-row gap-4">
	              <a href="#" class="bg-white text-slate-900 px-6 py-3 rounded-lg font-bold inline-flex items-center gap-3 hover:bg-slate-200 transition-colors">
	                <i class="fab fa-apple text-2xl"></i>
	                <span><span class="block text-xs">Download on the</span><span class="block text-lg font-semibold">App Store</span></span>
	              </a>
	              <a href="https://play.google.com/store/apps/details?id=com.justreused&hl=en" class="bg-white text-slate-900 px-6 py-3 rounded-lg font-bold inline-flex items-center gap-3 hover:bg-slate-200 transition-colors">
	                <i class="fab fa-google-play text-xl"></i>
	                <span><span class="block text-xs">GET IT ON</span><span class="block text-lg font-semibold">Google Play</span></span>
	              </a>
	            </div>
	          </div>
	          <div class="relative hidden md:flex items-center justify-center h-full">
	            <div class="absolute w-72 h-72 rounded-full border-2 border-white/10"></div>
	            <div class="absolute w-96 h-96 rounded-full border-2 border-white/10"></div>
	            <div class="absolute w-[32rem] h-[32rem] rounded-full border-2 border-white/10"></div>
	            <img class="absolute bottom-[-3rem] w-72 h-auto drop-shadow-2xl z-10"
	                 src="https://www.pngmart.com/files/15/Apple-iPhone-12-PNG-Image.png" alt="App Preview">
	          </div>
	        </div>
	      </div>
	    </section>
	  </main>
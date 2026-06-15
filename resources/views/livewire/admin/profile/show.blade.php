<x-admin-layout>
    
    
      <section class="relative block" style="height: 500px;">
        <div
          class="absolute top-0 w-full h-full bg-center bg-cover"
          style='background-image: url("https://images.unsplash.com/photo-1499336315816-097655dcfbda?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=2710&amp;q=80");'
        >
          <span
            id="blackOverlay"
            class="w-full h-full absolute opacity-50 bg-black"
          ></span>
        </div>
       
      </section>
	  
      <section class="relative py-16 bg-white">
        <div class="container mx-auto">
          <div
            class="relative flex flex-col min-w-0 break-words bg-gray-50 w-full mb-6 shadow-xl -mt-64"
          >
            <div class="px-1">
              <div class="flex flex-wrap justify-center">
                <div
                  class="w-full  px-4 lg:order-2 flex justify-center"
                >
                  <div class="relative">
                  @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <img
                      alt="{{Auth::user()->name}}"
                      
                      src="{{Auth::user()->profile_photo_url}}"
                      class="shadow-xl rounded-full h-auto align-middle border-none absolute -m-16 -ml-20 lg:-ml-16"
                      style="max-width: 150px;"
                    />
                 @endif
                  </div>
                </div>
                
                

              </div>
              <div class="text-center mt-36">
                <h3
                  class="text-4xl font-semibold leading-normal mb-2 text-gray-800 mb-2"
                >
                  {{ Auth::user()->name }}
                </h3>
                <div
                  class="text-sm leading-normal mt-0 mb-2 text-gray-500 font-bold"
                >
                  <i
                    class="mr-2 text-lg text-gray-500"
                  ></i>
                  {{ Auth::user()->email }}
                </div>
                
              </div>
              <div class="mt-10 py-10 border-t border-gray-300 text-center">
                <div class="flex flex-wrap justify-center">
                  <div class="w-full lg:w-9/12 px-4">
                  <!-- profile information -->
                  @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <div class="py-8">
                    <div class="border-t border-gray-200"></div>
                </div>
               
            @endif

                   <!-- user profiles -->

        @livewire('admin.user_profiles')

        <div class="py-8">
                    <div class="border-t border-gray-200"></div>
                </div>

            <!-- update password form -->
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <div class="py-8">
                    <div class="border-t border-gray-200"></div>
                </div>
            @endif

           
             <!--logout other browser sessions  -->
            



                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
   

    
<!-- nav bar -->


</x-admin-layout>

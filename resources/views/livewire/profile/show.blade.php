@extends('layouts.frontend')
@section('content')


<div class="min-h-screen bg-gray-100">
  <main class="profile-page">

    <section class="relative bg-gray-300">
      <div class="">
        <div class="relative flex flex-col min-w-0 break-words bg-white w-full shadow-xl">
          <div class="px-2">
            <div class="flex flex-wrap justify-center">
              <div class="w-full mt-16 mb-10 px-4 lg:order-2 flex justify-center">
                <div class="relative">
                  @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                  <img alt="{{Auth::user()->name}}" src="{{Auth::user()->profile_photo_url}}" class="shadow-xl rounded-full h-auto align-middle border-none absolute -m-16 -ml-20 lg:-ml-16" style="max-width: 150px;" />
                  @endif
                </div>
              </div>



            </div>
            <div class="text-center mt-36 py-8">
              <h3 class="text-4xl font-semibold leading-normal mb-2 text-gray-800 mb-2">
                {{ Auth::user()->name }}
              </h3>
              <div class="text-sm leading-normal mt-0 mb-2 text-gray-500 font-bold">
                <i class="mr-2 text-lg text-gray-500"></i>
                {{ Auth::user()->email }}
              </div>

            </div>
            <div class="border-t border-gray-300 text-center">
              <div class="flex flex-wrap justify-center">
                <div class="w-full lg:w-9/12 px-4 py-2">
                  <!-- profile information -->
                  @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                  @livewire('profile.update-profile-information-form')

                  <x-jet-section-border />
                  @endif

                  <!-- user profiles -->

                  @livewire('user-detail-profiles')

                  <div class="py-2">
                    <div class="border-t border-gray-200"></div>
                  </div>



                  <!-- update password form -->
                  @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                  <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                  </div>

                  <x-jet-section-border />
                  @endif


                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>



@endsection
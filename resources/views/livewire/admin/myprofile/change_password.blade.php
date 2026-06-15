<x-admin-layout>

 <!-- update password form -->

 <div class="py-24">
 <div class="flex items-center justify-center px-32 pt-6 pb-8 mb-4">
 <br><br><br>
 @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">


    <?php 
    $isDemoUser = App\Models\User::isDemoUser();
    if($isDemoUser["result"]==false)
    {
    ?>
                    @livewire('profile.update-password-form')
                
    <?php } ?>            
                
                
                </div>

                <x-jet-section-border />
            @endif
</div>
</div>
</x-admin-layout>

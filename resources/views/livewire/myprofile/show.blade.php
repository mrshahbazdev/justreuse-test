<div class="min-h-screen bg-warm-light py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header with Stats -->
        <div class="mb-8 text-center">
            
            <h1 class="text-4xl font-bold text-primary mb-2">Profile Settings</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Manage your personal information and account preferences</p>
            
            <!-- Quick Stats -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mx-auto">
                <div class="card-warm rounded-xl p-4 shadow-sm border border-primary-light">
                    <div class="text-2xl font-bold text-primary">{{ $mobile_verified ? '✓' : '!' }}</div>
                    <div class="text-sm text-gray-600 mt-1">Phone {{ $mobile_verified ? 'Verified' : 'Pending' }}</div>
                </div>
                <div class="card-warm rounded-xl p-4 shadow-sm border border-primary-light">
                    <div class="text-2xl font-bold text-accent-green">{{ $profile_photo_path ? '✓' : '+' }}</div>
                    <div class="text-sm text-gray-600 mt-1">Photo {{ $profile_photo_path ? 'Uploaded' : 'Missing' }}</div>
                </div>
                <div class="card-warm rounded-xl p-4 shadow-sm border border-primary-light">
                    <div class="text-2xl font-bold text-accent-purple">!</div>
                    <div class="text-sm text-gray-600 mt-1">Profile Strength</div>
                </div>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8 max-w-4xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold">1</div>
                    <div class="ml-2 text-sm font-medium text-gray-700">Basic Info</div>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">2</div>
                    <div class="ml-2 text-sm font-medium text-gray-500">Contact</div>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold">3</div>
                    <div class="ml-2 text-sm font-medium text-gray-500">Preferences</div>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
        <div class="mb-6 max-w-4xl mx-auto">
            <div class="rounded-xl p-4 {{ session('class') == 'error' ? 'alert-danger' : 'alert-success' }} shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fa {{ session('class') == 'error' ? 'fa-exclamation-circle text-danger' : 'fa-check-circle text-success' }} text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ session('class') == 'error' ? 'text-danger-dark' : 'text-success-dark' }}">
                            {{ session('message') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button onclick="this.parentElement.parentElement.style.display='none'" class="text-gray-400 hover:text-gray-600">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1">
                <div class="card rounded-2xl shadow-md p-6 sticky top-8">
                    <nav class="space-y-2">
                        <a href="#basic" class="flex items-center px-4 py-3 text-sm font-medium text-primary bg-primary-lighter rounded-xl border border-primary-light">
                            <i class="fa fa-user-circle mr-3"></i>
                            Basic Information
                        </a>
                        <a href="#contact" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:text-primary rounded-xl hover:bg-warm-light transition-colors">
                            <i class="fa fa-phone mr-3"></i>
                            Contact Details
                        </a>
                        <a href="#preferences" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:text-primary rounded-xl hover:bg-warm-light transition-colors">
                            <i class="fa fa-cog mr-3"></i>
                            Preferences
                        </a>
                        <a href="#security" class="flex items-center px-4 py-3 text-sm font-medium text-gray-600 hover:text-primary rounded-xl hover:bg-warm-light transition-colors">
                            <i class="fa fa-shield-alt mr-3"></i>
                            Security
                        </a>
                    </nav>
                    
                    <!-- Profile Completion -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Profile Completion</span>
                            <span class="text-sm font-bold text-primary">65%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="progress-bar h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <div class="lg:col-span-3">
                <div class="card rounded-2xl shadow-md overflow-hidden">
                    <form wire:submit.prevent="saveProfile">
                        
                        <!-- Basic Information Section -->
                        <div id="basic" class="p-8 border-b border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-primary-lighter rounded-lg flex items-center justify-center mr-4">
                                    <i class="fa fa-user-circle text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-primary">Basic Information</h3>
                                    <p class="text-gray-600">Your basic profile details</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                
                                <!-- Profile Photo Card -->
                                <div class="lg:col-span-2">
                                    <div class="bg-warm-lighter rounded-xl p-6 border border-primary-light">
                                        <div class="flex items-center space-x-6">
                                            <div class="relative group">
                                                <div class="w-24 h-24 rounded-2xl bg-primary-gradient overflow-hidden border-4 border-white shadow-lg">
                                                    @if($profile_photo_path)
                                                        <img src="{{ Storage::disk('public')->url($profile_photo_path) }}" 
                                                             class="h-full w-full object-cover" 
                                                             alt="Profile photo">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center">
                                                            <span class="text-white text-2xl font-bold">
                                                                {{ strtoupper(substr($name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-2xl transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <i class="fa fa-camera text-white text-lg"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-primary mb-2">Profile Photo</h4>
                                                <p class="text-sm text-gray-600 mb-4">Recommended: Square JPG, PNG at least 400x400px</p>
                                                
                                                <div class="flex flex-wrap gap-3">
                                                    <label class="cursor-pointer">
                                                        <span class="bg-white py-2.5 px-4 border border-primary rounded-xl shadow-sm text-sm font-medium text-primary hover:bg-primary-lighter focus:outline-none focus:ring-2 focus:ring-primary transition-all duration-200 flex items-center">
                                                            <i class="fa fa-upload mr-2"></i>Upload Photo
                                                        </span>
                                                        <input type="file" 
                                                               wire:model="new_profile_photo_path" 
                                                               class="hidden" 
                                                               accept="image/*">
                                                    </label>
                                                    
                                                    @if($profile_photo_path)
                                                    <button type="button" 
                                                            wire:click="removeProfilePhoto"
                                                            class="py-2.5 px-4 border border-danger text-danger rounded-xl hover:bg-danger-light text-sm font-medium transition-all duration-200 flex items-center">
                                                        <i class="fa fa-trash mr-2"></i>Remove
                                                    </button>
                                                    @endif
                                                    
                                                    @if($new_profile_photo_path)
                                                    <span class="badge-primary inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                                                        <i class="fa fa-check mr-1"></i>New photo selected
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Name Fields -->
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">Full Name *</label>
                                    <div class="relative">
                                        <i class="fa fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" 
                                               wire:model="name"
                                               class="form-input w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 bg-white"
                                               placeholder="Enter your full name">
                                    </div>
                                    @error('name') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">Email Address</label>
                                    <div class="relative">
                                        <i class="fa fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="email" 
                                               wire:model="email"
                                               disabled
                                               class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-300 rounded-xl shadow-sm cursor-not-allowed"
                                               placeholder="Your email address">
                                    </div>
                                </div>

                                <!-- First & Last Name -->
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">First Name *</label>
                                    <input type="text" 
                                           wire:model="first_name"
                                           class="form-input w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                           placeholder="Enter first name">
                                    @error('first_name') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">Last Name *</label>
                                    <input type="text" 
                                           wire:model="last_name"
                                           class="form-input w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                           placeholder="Enter last name">
                                    @error('last_name') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div id="contact" class="p-8 border-b border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-accent-green-light rounded-lg flex items-center justify-center mr-4">
                                    <i class="fa fa-phone text-accent-green"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-primary">Contact Information</h3>
                                    <p class="text-gray-600">How others can reach you</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                
                                <!-- Location Card -->
                                <div class="bg-warm-lighter rounded-xl p-6 border border-primary-light">
                                    <h4 class="font-semibold text-primary mb-4 flex items-center">
                                        <i class="fa fa-map-marker-alt mr-2 text-danger"></i>
                                        Current Location
                                    </h4>
                                    
                                    @if($currentLocation)
                                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-success mb-4">
                                        <div class="flex items-center">
                                            <i class="fa fa-check-circle text-success mr-3"></i>
                                            <span class="text-gray-800">{{ $currentLocation }}</span>
                                        </div>
                                        <button type="button" 
                                                wire:click="clearLocation"
                                                class="text-danger hover:text-danger-dark text-sm font-medium" style="box-shadow:none;">
                                            Change
                                        </button>
                                    </div>
                                    @endif

                                    <label class="block text-sm font-semibold text-primary mb-3">Search Your Location</label>
                                    <div class="relative">
                                        <i class="fa fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" 
                                               id="searchTextField"
                                               class="form-input w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                               placeholder="Start typing your city or address..."
                                               value="{{ $currentLocation }}">
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                            <i class="fa fa-map-pin text-primary"></i>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">We'll automatically detect your city, state, and country</p>
                                </div>

                                <!-- Phone Verification Card -->
                                <div class="bg-primary-lighter rounded-xl p-6 border border-primary-light">
                                    <h4 class="font-semibold text-primary mb-4 flex items-center">
                                        <i class="fa fa-mobile-alt mr-2 text-primary"></i>
                                        Phone Verification
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-primary mb-2">Phone Number</label>
                                            <div class="relative">
                                                <i class="fa fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                                <input type="tel" 
                                                       wire:model="phone"
                                                       class="form-input w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                                       placeholder="+1 (555) 123-4567">
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-end space-x-3">
                                            @if(!$mobile_verified && $phone)
                                            <button type="button" 
                                                    wire:click="sendOtp"
                                                    class="btn-primary flex-1 py-3.5 px-6 rounded-xl hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary transition-all duration-200 font-semibold shadow-sm flex items-center justify-center">
                                                <i class="fa fa-paper-plane mr-2"></i>Send OTP
                                            </button>
                                            @endif
                                            
                                            @if($mobile_verified)
                                            <div class="alert-success flex-1 py-3.5 px-6 rounded-xl font-semibold text-center flex items-center justify-center">
                                                <i class="fa fa-check-circle mr-2"></i>Verified
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- OTP Field -->
                                    @if($showOtpField)
                                    <div class="mt-4 p-4 bg-white rounded-lg border border-primary shadow-sm">
                                        <label class="block text-sm font-semibold text-primary mb-3">Enter Verification Code</label>
                                        <div class="flex space-x-3">
                                            <input type="text" 
                                                   wire:model="otp"
                                                   maxlength="6"
                                                   class="form-input flex-1 px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-center text-lg font-mono"
                                                   placeholder="000000">
                                            <button type="button" 
                                                    wire:click="verifyOtp"
                                                    class="btn-secondary px-8 rounded-xl hover:shadow-md focus:outline-none focus:ring-2 focus:ring-secondary transition-all duration-200 font-semibold shadow-sm">
                                                Verify
                                            </button>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">Enter the 6-digit code sent to your phone</p>
                                    </div>
                                    @endif
                                </div>
                              	
                            </div>
                        </div>
						<!-- Preferences Section -->
                        <div id="preferences" class="p-8 border-b border-gray-200">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-accent-purple-light rounded-lg flex items-center justify-center mr-4">
                                    <i class="fa fa-cog text-accent-purple"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-primary">Preferences</h3>
                                    <p class="text-gray-600">Customize your experience</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                <!-- Language & Currency -->
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">Preferred Language</label>
                                    <div class="relative">
                                        <i class="fa fa-language absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select wire:model="p_lang" 
                                                class="form-select w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 appearance-none bg-white">
                                            <option value="">Select Language</option>
                                            @foreach($select_language as $language)
                                                <option value="{{ $language->abbr }}" {{ $p_lang == $language->abbr ? 'selected' : '' }}>
                                                    {{ $language->native }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="fa fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    @error('p_lang') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                </div>

                                <!-- Preferred Currency -->
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">Preferred Currency</label>
                                    <div class="relative">
                                        <i class="fa fa-money-bill-wave absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <select wire:model="p_curr" 
                                                class="form-select w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200 appearance-none bg-white">
                                            <option value="">Select Currency</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}" 
                                                        {{ $p_curr == $currency->id ? 'selected' : '' }}>
                                                    {{ $currency->short_code }} {!! $currency->currency_hex !!}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="fa fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    @error('p_curr') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                </div>

                                <!-- Date of Birth -->
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">Date of Birth</label>
                                    <div class="relative">
                                        <i class="fa fa-calendar absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                        <input type="date" 
                                               wire:model="date_of_birth"
                                               class="form-input w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                               value="{{ $date_of_birth }}">
                                    </div>
                                    @error('date_of_birth') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror

                                    @if($date_of_birth)
                                    <p class="mt-2 text-sm text-gray-500">
                                        <i class="fa fa-info-circle mr-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($date_of_birth)->age }} years old
                                    </p>
                                    @endif
                                </div>

                                <!-- Gender -->
                                <!-- Gender -->
                                <div>
                                    <label class="block text-sm font-semibold text-primary mb-2">Gender</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach($gendArr as $key => $value)
                                        <label class="relative flex cursor-pointer">
                                            <input type="radio" 
                                                   wire:model="gender"
                                                   value="{{ $key }}"
                                                   class="peer sr-only"
                                                   {{ $gender == $key ? 'checked' : '' }}>
                                            <div class="w-full py-3 px-4 text-center border border-gray-300 rounded-xl transition-all duration-200 hover:bg-gray-50 
                                                        peer-checked:border-primary peer-checked:bg-primary-lighter peer-checked:text-primary-dark 
                                                        {{ $gender == $key ? 'border-primary bg-primary-lighter text-primary-dark' : 'border-gray-300 text-gray-700' }}">
                                                <span class="text-sm font-medium">{{ $value }}</span>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                    @error('gender') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                </div>

                                <!-- Stripe Keys -->
                                <div class="lg:col-span-2">
                                    <div class="bg-warm-lighter rounded-xl p-6 border border-primary-light">
                                        <h4 class="font-semibold text-primary mb-4 flex items-center">
                                            <i class="fa fa-credit-card mr-2 text-accent-green"></i>
                                            Stripe Payment Settings
                                        </h4>

                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-primary mb-2">Stripe Public Key</label>
                                                <input type="text" 
                                                       wire:model="stripe_public_key"
                                                       class="form-input w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                                       placeholder="pk_test_...">
                                                @error('stripe_public_key') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-primary mb-2">Stripe Private Key</label>
                                                <input type="password" 
                                                       wire:model="stripe_private_key"
                                                       class="form-input w-full px-4 py-3.5 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-200"
                                                       placeholder="sk_test_...">
                                                @error('stripe_private_key') <p class="mt-2 text-sm text-danger">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">
                                            <i class="fa fa-info-circle mr-1"></i>
                                            These keys are used for payment processing. Keep them secure.
                                        </p>
                                    </div>
                                </div>

                                <!-- Privacy Settings -->
                                <div class="lg:col-span-2">
                                    <div class="bg-warm-lighter rounded-xl p-6 border border-primary-light">
                                        <h4 class="font-semibold text-primary mb-4 flex items-center">
                                            <i class="fa fa-eye mr-2 text-warning"></i>
                                            Privacy Settings
                                        </h4>

                                        <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                            <div>
                                                <div class="font-medium text-primary">Show Mobile Number Publicly</div>
                                                <div class="text-sm text-gray-600 mt-1">Allow other users to see your phone number in your public profile</div>
                                            </div>
                                            <button type="button" 
                                                    wire:click="$set('show_mobile', {{ $show_mobile ? 0 : 1 }})"
                                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 {{ $show_mobile ? 'bg-primary' : 'bg-gray-200' }} shadow-sm"
                                                    role="switch">
                                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $show_mobile ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="px-8 py-6 bg-warm-light border-t border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" 
                                            class="btn-primary px-8 py-3.5 rounded-xl shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-200 font-semibold flex items-center">
                                        <i class="fa fa-save mr-2"></i>Save Changes
                                    </button>
                                    
                                    <a href="{{ URL::to('change-password') }}" 
                                       class="btn-outline-primary px-8 py-3.5 rounded-xl shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-200 font-semibold flex items-center">
                                        <i class="fa fa-lock mr-2"></i>Change Password
                                    </a>
                                </div>
                                
                                <button type="button" 
                                        wire:click="confirmDelete"
                                        class="px-6 py-3.5 text-danger bg-white border border-danger rounded-xl hover:bg-danger-light focus:outline-none focus:ring-2 focus:ring-danger focus:ring-offset-2 transition-all duration-200 font-semibold flex items-center">
                                    <i class="fa fa-trash mr-2"></i>Delete Account
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($deleteConfirmation)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 animate-fade-in">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 transform animate-scale-in">
            <div class="flex items-center justify-center w-16 h-16 bg-danger-light rounded-full mx-auto mb-4">
                <i class="fa fa-exclamation-triangle text-danger text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-primary text-center mb-2">Delete Account</h3>
            <p class="text-gray-600 text-center mb-6">
                Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently lost.
            </p>
            <div class="flex space-x-3">
                <button wire:click="cancelDelete" 
                        class="btn-outline-primary flex-1 py-3 px-4 rounded-xl font-semibold">
                    Cancel
                </button>
                <button wire:click="deleteAccount" 
                        class="btn-primary bg-danger hover:bg-danger-dark flex-1 py-3 px-4 rounded-xl font-semibold">
                    Delete Account
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Google Maps Script -->
{!! config('app.google_map_script') !!}
<script>
function initializeAutocomplete() {
    const input = document.getElementById('searchTextField');
    
    if (!input) return;

    const autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['(regions)'],
        componentRestrictions: {country: []} // Remove country restrictions for global search
    });

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        const place = autocomplete.getPlace();
        
        if (!place.geometry) {
            console.log('No details available for input: ' + place.name);
            return;
        }

        const country = place.address_components.find(item => item.types.includes('country'));
        const state = place.address_components.find(item => item.types.includes('administrative_area_level_1'));
        const cityComponent = place.address_components.find(item => 
            item.types.includes('locality') || 
            item.types.includes('administrative_area_level_2') || 
            item.types.includes('administrative_area_level_3') ||
            item.types.includes('sublocality')
        );

        const locality = place.address_components.find(item => item.types.includes('locality'));
        const cityName = locality ? locality.long_name : (cityComponent ? cityComponent.long_name : place.name);

        const placeData = {
            city_name: place.name || '',
            main_city_name: cityName || '',
            city_lat: place.geometry.location.lat() || '',
            city_lag: place.geometry.location.lng() || '',
            country_long: country?.long_name || '',
            country_short: country?.short_name || '',
            state_long: state?.long_name || '',
            state_short: state?.short_name || ''
        };

        // Update Livewire component
        @this.call('setLocation', placeData);
        
        // Show success message
        const locationText = [cityName, state?.long_name, country?.long_name].filter(Boolean).join(', ');
        showNotification('Location updated: ' + locationText, 'success');
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fa ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => notification.style.transform = 'translateX(0)', 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Initialize when Livewire is loaded
document.addEventListener('livewire:load', function() {
    initializeAutocomplete();
});

// Re-initialize when Livewire updates the DOM
document.addEventListener('livewire:update', function() {
    initializeAutocomplete();
});

// Smooth scrolling for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>

<style>
/* Custom animations */
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scale-in {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Google Places Autocomplete styling */
.pac-container {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    margin-top: 8px;
    font-family: inherit;
}

.pac-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background-color 0.2s;
}

.pac-item:hover {
    background-color: #f8fafc;
}

.pac-item-selected {
    background-color: #eff6ff;
}

.pac-icon {
    margin-right: 12px;
}

.pac-item-query {
    font-size: 14px;
    color: #1f2937;
}

/* Focus states for better accessibility */
input:focus, select:focus, button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Loading states */
button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
  /* Gender Selection Styles */
.gender-option {
    transition: all 0.3s ease-in-out;
}

.gender-option.selected {
    border-color: var(--primary);
    background-color: var(--primary-lighter);
    color: var(--primary-dark);
    box-shadow: var(--shadow-sm);
}

/* Radio button custom styling */
input[type="radio"]:checked + div {
    border-color: var(--primary);
    background-color: var(--primary-lighter);
    color: var(--primary-dark);
    box-shadow: var(--shadow-sm);
}

/* Hover effects */
.gender-option:hover {
    background-color: var(--gray-50);
    border-color: var(--primary-light);
}
</style>
<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-7xl lg:py-8">
        <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">
            Investment Forum 2025 Registration
        </h2>

        <form wire:submit.prevent="register" enctype="multipart/form-data" class="space-y-8">

            {{-- ✅ Row 1: Image Preview + Upload --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div>
                    @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" class="w-28 h-28 object-cover rounded-lg border">
                    @else
                    <div class="w-28 h-28 flex items-center justify-center border-2 border-dashed 
                border-gray-300 rounded-lg text-gray-400 dark:border-gray-600">
                        No Image
                    </div>
                    @endif
                </div>

                <div class="w-full sm:w-auto">
                    <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Upload Image
                    </label>
                    <input type="file" wire:model="image" id="image" accept="image/*"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full 
                 p-2 cursor-pointer dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ✅ Grid Fields --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Names --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First Name</label>
                    <input type="text" wire:model="firstname" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="First Name">
                    @error('firstname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Middle Name</label>
                    <input type="text" wire:model="middlename"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Middle Name">
                    @error('middlename') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
                    <input type="text" wire:model="lastname"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Last Name">
                    @error('lastname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Work --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Affiliation</label>
                    <input type="text" wire:model="affiliation"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Affiliation">
                    @error('affiliation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Designation</label>
                    <input type="text" wire:model="designation"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Designation">
                    @error('designation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gender</label>
                    <select wire:model="gender"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                  dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Vulnerability</label>
                    <input type="text" wire:model="vulnerability"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="e.g. PWD">
                    @error('vulnerability') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Location --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Region</label>
                    <select wire:model.live="region" 
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Region</option>
                        @foreach($regions as $reg)
                        <option value="{{ $reg->id }}">{{ $reg->region_short_name }}</option>
                        @endforeach
                    </select>
                    @error('region') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror 
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Province</label>
                    <select wire:model="province"  @if(count($provinces) == 0) disabled @endif
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select Province</option>
                        @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}">{{ $prov->PROVINCE }}</option>
                        @endforeach
                    </select>
                    @error('province') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Contact --}}
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone</label>
                    <input type="text" wire:model="phone" minlength="11" maxlength="11"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="09123456789">
                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input type="email" wire:model="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="you@example.com">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Food Restriction --}}
            <div class="col-span-1 sm:col-span-2 lg:col-span-3">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Food Restriction</label>
                <textarea wire:model="food_restriction" rows="3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                  dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Specify any food restriction..."></textarea>
                @error('food_restriction') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="mt-6 inline-flex items-center px-5 py-2.5 text-sm font-medium text-center 
              text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 
              focus:ring-blue-300 dark:focus:ring-blue-800 w-full sm:w-auto">
                Register
            </button>
        </form>
    </div>
</section>
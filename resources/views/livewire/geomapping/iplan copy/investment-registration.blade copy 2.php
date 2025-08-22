<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-7xl lg:py-8">
        <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">
            Investment Forum 2025 Registration
        </h2>

        <form wire:submit.prevent="register" enctype="multipart/form-data" class="space-y-10">

            {{-- ✅ Upload Profile Image --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Profile Image</label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div>
                        @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="w-28 h-28 object-cover rounded-lg border">
                        @else
                        <div class="w-28 h-28 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg text-gray-400 dark:border-gray-600">
                            No Image
                        </div>
                        @endif
                    </div>

                    <div class="w-full sm:w-auto">
                        <input type="file" wire:model="image" id="image" accept="image/*"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2 cursor-pointer dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 🆔 Primary Info --}}
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Primary Info</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First Name <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="firstname" placeholder="First Name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('firstname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Middle Name<span class="text-gray-400"> (optional)</span></label>
                        <input type="text" wire:model="middlename" placeholder="Middle Name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('middlename') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="lastname" placeholder="Last Name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('lastname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name Extension<span class="text-gray-400"> (optional)</span></label>
                        <input type="text" wire:model="ext_name" placeholder="e.g. Jr., Sr."
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('ext_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gender <span class="text-red-600">*</span></label>
                        <select wire:model="gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 🏢 Organizational Info --}}
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Organizational Info</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Institution --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="institution">
                            Institution <span class="text-red-600">*</span>
                        </label>
                        <select wire:model="institution" id="institution"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Institution</option>
                            <option value="Provincial Local Government Unit">Provincial Local Government Unit</option>
                            <option value="Department of Agriculture">Department of Agriculture</option>
                            <option value="Other Institutions">Other Institutions</option>
                        </select>
                        @error('institution') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Office --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Office <span class="text-red-600">*</span>
                        </label>
                        <input type="text" wire:model="office" placeholder="Office"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('office') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Designation --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Designation <span class="text-red-600">*</span>
                        </label>
                        <input type="text" wire:model="designation" placeholder="Designation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('designation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Region --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Region <span class="text-red-600">*</span>
                        </label>
                        <select wire:model="region" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Region</option>
                            @foreach($regions as $reg)
                            <option value="{{ $reg->id }}">{{ $reg->region_short_name }}</option>
                            @endforeach
                        </select>
                        @error('region') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Province --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Province <span class="text-red-600">*</span>
                        </label>
                        <select wire:model="province" @if(count($provinces)==0) disabled @endif
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Province</option>
                            @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}">{{ $prov->PROVINCE }}</option>
                            @endforeach
                        </select>
                        @error('province') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>


            {{-- 📞 Contact Info --}}
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Contact Info <span class="text-red-600">*</span></h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email <span class="text-red-600">*</span></label>
                        <input type="email" wire:model="email" placeholder="you@example.com"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact No. <span class="text-red-600">*</span></label>
                        <input type="text" wire:model="contact_no" minlength="11" maxlength="11" placeholder="09123456789"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        @error('contact_no') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Others --}}
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Others</h3>
                <div class="grid grid-cols-1">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Food Restriction <span class="text-red-600">*</span><span class="text-gray-400"> (Put N/A if not applicable)</span></label>
                    <textarea wire:model="food_restriction" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5
                          dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Specify any food restriction..."></textarea>
                    @error('food_restriction') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Submit Button Aligned Right -->
            <div class="flex justify-end">
                <button type="submit"
                    class="mt-6 inline-flex items-center px-5 py-2.5 text-sm font-medium text-center 
        text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 
        focus:ring-blue-300 dark:focus:ring-blue-800">
                    Register
                </button>
            </div>

        </form>
    </div>
</section>
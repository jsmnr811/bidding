<?php

use App\Models\Region;
use App\Models\Province;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\GeomappingUser;
use App\Notifications\MailUserId;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

new class extends Component {
    use WithFileUploads;

    public GeomappingUser $user;

    public $image, $existing_image;
    public $firstname, $middlename, $lastname, $ext_name, $sex;
    public $institution, $office, $designation, $region_id, $province_id;
    public $email, $contact_number;
    public $food_restriction;
    public $group_number, $table_number;
    public $regions = [];
    public $provinces = [];

    public $validatedUserData = [];

    public $editModal = false;
    public $assignModal = false;

    public function mount(): void
    {
        $this->regions = Region::orderBy('name')->get();
        $this->provinces = collect();
    }

    #[On('editGeomappingUser')]
    public function edit(GeomappingUser $user)
    {
        $this->user = $user;
        $this->existing_image = $user->image;
        $this->image = null;
        $this->firstname = $user->firstname;
        $this->middlename = $user->middlename;
        $this->lastname = $user->lastname;
        $this->ext_name = $user->ext_name;
        $this->sex = $user->sex;

        $this->institution = $user->institution;
        $this->office = $user->office;
        $this->designation = $user->designation;
        $this->region_id = $user->region_id;
        $this->province_id = $user->province_id;

        if ($this->region_id) {
            $this->provinces = Province::where('region_code', $this->region_id)->orderBy('name')->get();
        } else {
            $this->provinces = collect();
        }

        $this->email = $user->email;
        $this->contact_number = $user->contact_number;

        $this->food_restriction = $user->food_restriction;

        $this->group_number = $user->group_number;
        $this->table_number = $user->table_number;

        $this->editModal = true;
    }

    #[On('assignGeomappingUser')]
    public function assignGeomappingUser(GeomappingUser $user)
    {
        $this->user = $user;
        $this->group_number = $user->group_number;
        $this->table_number = $user->table_number;

        $this->assignModal = true;
    }

    public function confirmUpdate()
    {
        $this->validatedUserData = $this->validate([
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'ext_name' => 'nullable|string|max:255',
            'sex' => 'required|in:Male,Female',

            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'contact_number' => 'nullable|string|max:11|unique:users,contact_number,' . $this->user->id,
            'food_restriction' => 'nullable|string|max:255',

            'region_id' => 'required',
            'province_id' => 'required',
            'institution' => 'required|string|max:255',
            'office' => 'required|string|max:255',
            'designation' => 'required|string|max:255',

            'group_number' => 'required|string|max:255',
            'table_number' => 'required|string|max:255',
        ]);
        if (!$this->existing_image && !$this->image) {
            $this->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }
        if ($this->image) {
            $path = $this->image->store('profile-images', 'public');
            $validated['image'] = $path;
        }

        LivewireAlert::title('Are you sure?')->question()->timer(0)->withConfirmButton('Update')->withCancelButton('Cancel')->onConfirm('updateUser')->show();
    }

    public function confirmUpdateAssignment()
    {
        $this->validate([
            'group_number' => 'required|string|max:255',
            'table_number' => 'required|string|max:255',
        ]);
        LivewireAlert::title('Are you sure?')->text('Are you sure you want to assign this user to this group?')->question()->timer(0)->withConfirmButton('Assign')->withCancelButton('Cancel')->onConfirm('assignUser')->show();
    }

    public function assignUser()
    {
        $this->user->group_number = $this->group_number;
        $this->user->table_number = $this->table_number;
        $this->user->save();
        LivewireAlert::success()->title('Success!')->text('Group and Table has been assigned successfully.')->toast()->position('top-end')->show();
        $this->dispatch('reloadDataTable');
        $this->assignModal = false;
    }

    public function updatedRegionId()
    {
        $this->provinces = Province::where('region_code', $this->region_id)->orderBy('name')->get();
        $this->province_id = null;
    }

    public function updateUser()
    {
        $this->validatedUserData['name'] = $this->firstname . ' ' . $this->middlename . ' ' . $this->lastname . ' ' . $this->ext_name;
        $this->user->update($this->validatedUserData);
        $this->editModal = false;
        LivewireAlert::title('Success')->success()->toast()->position('top-end')->show();
        $this->dispatch('reloadDataTable');
    }

    #[On('confirmUpdateBlockStatus')]
    public function confirmUpdateBlockStatus(GeomappingUser $user)
    {
        $this->user = $user;
        LivewireAlert::title('Are you sure?')->question()->text('Are you sure you want to update the status of this user? ')->timer(0)->withConfirmButton('Update')->withCancelButton('Cancel')->onConfirm('updateBlockStatus')->show();
    }

    public function updateBlockStatus()
    {
        if ($this->user->is_blocked) {
            $this->user->is_blocked = 0;
        } else {
            $this->user->is_blocked = 1;
        }
        $this->user->save();
        LivewireAlert::title('Success')->success()->text('User status has been updated successfully')->toast()->position('top-end')->show();
        $this->dispatch('reloadDataTable');
    }

    #[On('confirmSendGeomappingUserId')]
    public function confirmSendGeomappingUserId(GeomappingUser $user)
    {
        $this->user = $user;
        LivewireAlert::title('Are you sure?')->question()->text('Are you sure you want to mail the geomapping user id? ')->timer(0)->withConfirmButton('Send')->withCancelButton('Cancel')->onConfirm('sendGeomappingUserId')->show();
    }

    public function sendGeomappingUserId()
    {
        $this->user->notify(new MailUserId($this->user));
        LivewireAlert::title('Success')->text('Geomapping User ID has been sent successfully')->success()->toast()->position('top-end')->show();
        $this->dispatch('reloadDataTable');
    }
};

?>
<div>
    @if ($editModal)
        <!-- Bootstrap Modal (container only) -->
        <div class="modal fade show d-block" id="editUserModal" tabindex="-1" role="dialog"
            aria-labelledby="editUserModalLabel" aria-modal="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content rounded-2xl shadow-lg">

                    <!-- Modal Header -->
                    <div class="modal-header border-b">
                        <h5 class="modal-title font-semibold text-lg" id="editUserModalLabel">Edit Information</h5>
                        <button type="button" class="close" wire:click='$set("editModal", false)' aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="confirmUpdate">
                        <div class="modal-body space-y-6">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {{-- ✅ Profile Image --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Profile Image</label>
                                <div class="flex items-center gap-3 mt-2">
                                    @if ($image)
                                        <img src="{{ $image->temporaryUrl() }}" class="rounded-lg border" width="80"
                                            height="80">
                                    @elseif ($existing_image)
                                        <div style="position: relative">
                                            <img src="{{ asset($existing_image) }}" class="rounded-lg border"
                                                width="200px" height="200px">
                                            <div class="absolute top-0 right-0 bg-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer"
                                                onclick="document.getElementById('profile_image').click()">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687 1.687a1.875 1.875 0 010 2.652l-8.955 8.955a4.5 4.5 0 01-1.897 1.13l-3.615.965a.75.75 0 01-.927-.928l.965-3.615a4.5 4.5 0 011.13-1.897l8.955-8.955a1.875 1.875 0 012.652 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 7.125L16.875 4.5" />
                                                </svg>

                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="border rounded-lg flex items-center justify-center text-gray-400 w-20 h-20">
                                            No Image
                                        </div>
                                    @endif
                                    <input type="file" style="display: none" id="profile_image" wire:model="image"
                                        accept="image/*" class="text-sm">
                                </div>
                                @error('image')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>


                            {{-- 🆔 Primary Info --}}
                            <div>
                                <h6 class="text-gray-700 font-semibold mb-2">Primary Info</h6>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="text-sm font-medium">First Name <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model="firstname" placeholder="First Name"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('firstname')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Middle Name <small
                                                class="text-gray-400">(optional)</small></label>
                                        <input type="text" wire:model="middlename" placeholder="Middle Name"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Last Name <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model="lastname" placeholder="Last Name"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name
                                            Extension<span class="text-gray-400"> (optional)</span></label>
                                        <input type="text" wire:model="ext_name" placeholder="e.g. Jr., Sr."
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                        @error('ext_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sex
                                            <span class="text-red-600">*</span></label>
                                        <select wire:model="sex"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                            <option value="">Select Sex</option>
                                            <option {{ $sex == 'Male' ? 'selected' : '' }} value="Male">Male</option>
                                            <option {{ $sex == 'Female' ? 'selected' : '' }} value="Female">Female
                                            </option>
                                        </select>
                                        @error('sex')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Organizational Info --}}
                            <div>
                                <h6 class="text-gray-700 font-semibold mb-2">Organizational Info</h6>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium">Institution <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model="institution"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Institution</option>
                                            <option value="Provincial Local Government Unit">Provincial Local Government
                                                Unit</option>
                                            <option value="Department of Agriculture">Department of Agriculture</option>
                                            <option value="Other Institutions">Other Institutions</option>
                                        </select>
                                        @error('institution')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Office <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model="office" placeholder="Office"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('office')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Designation <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text" wire:model="designation" placeholder="Designation"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                        @error('designation')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Region & Province -->
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label class="text-sm font-medium">Region <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model.live.debounce="region_id" class="form-control">
                                            <option value="">Select Region</option>
                                            @foreach ($regions as $reg)
                                                <option {{ $reg->code == $region_id ? 'selected' : '' }}
                                                    value="{{ $reg->code }}">{{ $reg->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('region_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Province <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model="province_id" class="form-control">
                                            <option value="">Select Province</option>
                                            @foreach ($provinces as $prov)
                                                <option {{ $prov->id == $province_id ? 'selected' : '' }}
                                                    value="{{ $prov->id }}">{{ $prov->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('province_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- 📞 Contact Info --}}
                            <div>
                                <h6 class="text-gray-700 font-semibold mb-2">Contact Information</h6>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium">Email <span
                                                class="text-red-500">*</span></label>
                                        <input type="email" wire:model="email" placeholder="you@example.com"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Contact Number <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model="contact_number" minlength="11"
                                            maxlength="11" placeholder="09123456789"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>


                            <!-- Food Restriction -->
                            <div>
                                <label class="text-sm font-medium">Food Restriction <span
                                        class="text-red-500">*</span>
                                    <small class="text-gray-400">(Put N/A if not applicable)</small>
                                </label>
                                <textarea wire:model="food_restriction" rows="3" placeholder="Specify any food restriction..."
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                @error('food_restriction')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-t mt-6">
                            <button type="button" class="btn btn-secondary"
                                wire:click='$set("editModal", false)'>Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div class="modal-backdrop fade show"></div>
    @endif

    @if ($assignModal)
        <!-- Bootstrap Modal (container only) -->
        <div class="modal fade show d-block" id="assignUser" tabindex="-1" role="dialog"
            aria-labelledby="assignUserLabel" aria-modal="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content rounded-2xl shadow-lg">

                    <!-- Modal Header -->
                    <div class="modal-header border-b">
                        <h5 class="modal-title font-semibold text-lg" id="assignUserLabel">Assign Group and Table</h5>
                        <button type="button" class="close" wire:click='$set("assignModal", false)'
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form wire:submit.prevent="confirmUpdateAssignment">
                        <div class="modal-body space-y-6">

                            <div>
                                <!-- Group & Table -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="text-sm font-medium">Group Number <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model="group_number"
                                            placeholder="Enter group number" minlength="1" maxlength="2"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('group_number')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium">Table Number <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" wire:model="table_number"
                                            placeholder="Enter table number" minlength="1" maxlength="1"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @error('table_number')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>


                            </div>

                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer border-t mt-6">
                            <button type="button" class="btn btn-secondary"
                                wire:click='$set("assignModal", false)'>Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div class="modal-backdrop fade show"></div>
    @endif
</div>

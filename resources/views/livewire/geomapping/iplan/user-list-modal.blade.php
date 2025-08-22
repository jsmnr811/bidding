<?php

use App\Models\Commodity;
use Livewire\Attributes\On;
use App\Models\GeoCommodity;
use App\Models\Intervention;
use Livewire\Volt\Component;
use App\Models\GeomappingUser;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

new class extends Component {
    public GeomappingUser $user;

    public $firstname;
    public $middlename;
    public $lastname;

    public $editModal = false;

    public function mount(): void {}
    #[On('editGeomappingUser')]
    public function edit(GeomappingUser $user)
    {
        $this->user = $user;
        $this->firstname = $user->firstname;
        $this->middlename = $user->middlename;
        $this->lastname = $user->lastname;
        $this->editModal = true;
    }

    public function confirmUpdate()
    {
        LivewireAlert::title('Are you sure?')->question()->timer(0)->withConfirmButton('Update')->withCancelButton('Cancel')->onConfirm('updateUser')->show();
    }

    public function updateUser()
    {
        $this->user->firstname = $this->firstname;
        $this->user->middlename = $this->middlename;
        $this->user->lastname = $this->lastname;
        $this->user->name = $this->firstname . ' ' . $this->middlename . ' ' . $this->lastname;
        $this->user->save();
        $this->editModal = false;
        LivewireAlert::title('Success')->success()->toast()->position('top-end')->show();
        $this->dispatch('reloadDataTable');
    }
};
?>
<div>
    @if ($editModal)
        <div id="exampleModalLive" class="modal fade show" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLiveLabel" style="padding-right: 17px; display: block;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLiveLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="confirmUpdate">

                        <div class="modal-body">
                            <p>Woohoo, you're reading this text in a modal!</p>
                            <div class="mb-3">

                                <label for="">First Name</label>
                                <input type="text" class="form-control" wire:model='firstname'>
                            </div>
                            <div class="mb-3">
                                <label for="">Middle Name</label>
                                <input type="text" class="form-control" wire:model='middlename'>
                            </div>
                            <div class="mb-3">
                                <label for="">Last Name</label>
                                <input type="text" class="form-control" wire:model='lastname'>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click='$set("editModal", false)'>Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

</div>

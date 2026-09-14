<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card title="Register New Patient" icon="bi bi-person-plus">
            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="first_name" value="First name" />
                        <x-text-input wire:model="first_name" id="first_name" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="last_name" value="Last name" />
                        <x-text-input wire:model="last_name" id="last_name" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="date_of_birth" value="Date of birth" />
                        <x-text-input type="date" wire:model="date_of_birth" id="date_of_birth" />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="sex" value="Sex" />
                        <select wire:model="sex" id="sex" class="form-select">
                            <option value="">Select...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('sex')" class="mt-1" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="phone" value="Phone (for SMS reminders)" />
                        <x-text-input wire:model="phone" id="phone" placeholder="+255700000000" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="email" value="Email" />
                        <x-text-input type="email" wire:model="email" id="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="national_id" value="National ID" />
                        <x-text-input wire:model="national_id" id="national_id" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="nhif_card_number" value="NHIF card number" />
                        <x-text-input wire:model="nhif_card_number" id="nhif_card_number" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="blood_group" value="Blood group" />
                        <x-text-input wire:model="blood_group" id="blood_group" placeholder="O+" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="address" value="Address" />
                        <x-text-input wire:model="address" id="address" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="emergency_contact_name" value="Emergency contact name" />
                        <x-text-input wire:model="emergency_contact_name" id="emergency_contact_name" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="emergency_contact_phone" value="Emergency contact phone" />
                        <x-text-input wire:model="emergency_contact_phone" id="emergency_contact_phone" />
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('patients.index') }}" wire:navigate class="small">Cancel</a>
                    <x-primary-button>Register Patient</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>

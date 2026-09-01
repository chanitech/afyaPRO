<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Register New Patient</h3>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="first_name" value="First name" />
                    <x-text-input wire:model="first_name" id="first_name" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="last_name" value="Last name" />
                    <x-text-input wire:model="last_name" id="last_name" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="date_of_birth" value="Date of birth" />
                    <x-text-input type="date" wire:model="date_of_birth" id="date_of_birth" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="sex" value="Sex" />
                    <select wire:model="sex" id="sex" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select...</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" value="Phone (for SMS reminders)" />
                    <x-text-input wire:model="phone" id="phone" placeholder="+255700000000" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input type="email" wire:model="email" id="email" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="national_id" value="National ID" />
                    <x-text-input wire:model="national_id" id="national_id" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="blood_group" value="Blood group" />
                    <x-text-input wire:model="blood_group" id="blood_group" placeholder="O+" class="mt-1 block w-full" />
                </div>
            </div>

            <div>
                <x-input-label for="address" value="Address" />
                <x-text-input wire:model="address" id="address" class="mt-1 block w-full" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="emergency_contact_name" value="Emergency contact name" />
                    <x-text-input wire:model="emergency_contact_name" id="emergency_contact_name" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="emergency_contact_phone" value="Emergency contact phone" />
                    <x-text-input wire:model="emergency_contact_phone" id="emergency_contact_phone" class="mt-1 block w-full" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('patients.index') }}" wire:navigate class="text-sm text-gray-600 hover:underline">Cancel</a>
                <x-primary-button>Register Patient</x-primary-button>
            </div>
        </form>
    </div>
</div>

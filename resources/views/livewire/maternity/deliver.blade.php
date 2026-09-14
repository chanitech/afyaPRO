<div class="row">
    <div class="col-12 col-lg-8">
        <x-adminlte-card icon="bi bi-clipboard2-pulse"
            title="Record Delivery — {{ $delivery->patient->fullName() }} ({{ $delivery->patient->patient_number }})">
            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="delivery_mode" value="Mode of delivery" />
                        <select wire:model="delivery_mode" id="delivery_mode" class="form-select">
                            <option value="">Select...</option>
                            <option value="spontaneous_vaginal">Spontaneous Vaginal</option>
                            <option value="vacuum">Vacuum</option>
                            <option value="forceps">Forceps</option>
                            <option value="caesarean">Caesarean</option>
                        </select>
                        <x-input-error :messages="$errors->get('delivery_mode')" class="mt-1" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="delivered_at" value="Time of delivery" />
                        <x-text-input type="datetime-local" wire:model="delivered_at" id="delivered_at" />
                        <x-input-error :messages="$errors->get('delivered_at')" class="mt-1" />
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="outcome" value="Outcome" />
                        <select wire:model="outcome" id="outcome" class="form-select">
                            <option value="">Select...</option>
                            <option value="live_birth">Live Birth</option>
                            <option value="stillbirth">Stillbirth</option>
                        </select>
                        <x-input-error :messages="$errors->get('outcome')" class="mt-1" />
                    </div>
                    <div class="col-md-6">
                        <x-input-label for="perineal_status" value="Perineal status" />
                        <x-text-input wire:model="perineal_status" id="perineal_status" placeholder="Intact / episiotomy / tear" />
                    </div>

                    <div class="col-md-4">
                        <x-input-label for="baby_sex" value="Baby's sex" />
                        <select wire:model="baby_sex" id="baby_sex" class="form-select">
                            <option value="">Select...</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <x-input-label for="baby_weight_grams" value="Birth weight (g)" />
                        <x-text-input type="number" min="200" max="6000" wire:model="baby_weight_grams" id="baby_weight_grams" />
                    </div>
                    <div class="col-md-2">
                        <x-input-label for="apgar_1min" value="Apgar 1 min" />
                        <x-text-input type="number" min="0" max="10" wire:model="apgar_1min" id="apgar_1min" />
                    </div>
                    <div class="col-md-2">
                        <x-input-label for="apgar_5min" value="Apgar 5 min" />
                        <x-text-input type="number" min="0" max="10" wire:model="apgar_5min" id="apgar_5min" />
                    </div>

                    <div class="col-12">
                        <x-input-label for="complications" value="Complications" />
                        <textarea wire:model="complications" id="complications" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="col-12">
                        <x-input-label for="notes" value="Notes" />
                        <textarea wire:model="notes" id="notes" rows="2" class="form-control"></textarea>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <a href="{{ route('maternity.partograph', ['delivery' => $delivery->id]) }}" wire:navigate class="small">Back to Partograph</a>
                    <x-primary-button>Save Delivery Record</x-primary-button>
                </div>
            </form>
        </x-adminlte-card>
    </div>
</div>

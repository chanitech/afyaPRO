<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:100')]
    public string $first_name = '';

    #[Validate('required|string|max:100')]
    public string $last_name = '';

    #[Validate('required|date|before:today')]
    public string $date_of_birth = '';

    #[Validate('required|in:male,female')]
    public string $sex = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:30')]
    public string $national_id = '';

    #[Validate('nullable|string|max:30')]
    public string $nhif_card_number = '';

    #[Validate('nullable|string|max:30')]
    public string $nssf_member_number = '';

    #[Validate('nullable|string|max:255')]
    public string $address = '';

    #[Validate('nullable|string|max:100')]
    public string $emergency_contact_name = '';

    #[Validate('nullable|string|max:20')]
    public string $emergency_contact_phone = '';

    #[Validate('nullable|string|max:5')]
    public string $blood_group = '';

    public function save()
    {
        $this->validate();

        $patient = Patient::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'sex' => $this->sex,
            'phone' => $this->phone,
            'email' => $this->email,
            'national_id' => $this->national_id,
            'nhif_card_number' => $this->nhif_card_number,
            'nssf_member_number' => $this->nssf_member_number,
            'address' => $this->address,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'blood_group' => $this->blood_group,
            'facility_id' => Auth::user()->facility_id,
            'patient_number' => Patient::generatePatientNumber(),
            'registered_by' => Auth::id(),
        ]);

        session()->flash('status', "Patient {$patient->fullName()} registered (No. {$patient->patient_number}).");

        return $this->redirect(route('appointments.create', ['patient' => $patient->id]), navigate: true);
    }

    public function render()
    {
        return view('livewire.patients.create');
    }
}

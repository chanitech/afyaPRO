<?php

use App\Livewire\Appointments\Create as AppointmentsCreate;
use App\Livewire\Appointments\Index as AppointmentsIndex;
use App\Livewire\Billing\Create as BillingCreate;
use App\Livewire\Billing\Pending as BillingPending;
use App\Livewire\Billing\Show as BillingShow;
use App\Livewire\Diagnostics\Order as DiagnosticsOrder;
use App\Livewire\Diagnostics\Pending as DiagnosticsPending;
use App\Livewire\Diagnostics\Show as DiagnosticsShow;
use App\Livewire\Inpatient\Admit as InpatientAdmit;
use App\Livewire\Inpatient\Board as InpatientBoard;
use App\Livewire\Inpatient\Wards as InpatientWards;
use App\Livewire\Opd\Consultation;
use App\Livewire\Patients\Create as PatientsCreate;
use App\Livewire\Patients\Index as PatientsIndex;
use App\Livewire\Pharmacy\Drugs as PharmacyDrugs;
use App\Livewire\Pharmacy\Pending as PharmacyPending;
use App\Livewire\Pharmacy\Prescribe as PharmacyPrescribe;
use App\Livewire\Pharmacy\Show as PharmacyShow;
use App\Livewire\Queue\Board as QueueBoard;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('permission:patients.manage')->group(function () {
        Route::get('patients', PatientsIndex::class)->name('patients.index');
        Route::get('patients/create', PatientsCreate::class)->name('patients.create');
    });

    Route::middleware('permission:appointments.manage')->group(function () {
        Route::get('appointments', AppointmentsIndex::class)->name('appointments.index');
        Route::get('appointments/create', AppointmentsCreate::class)->name('appointments.create');
    });

    Route::middleware('permission:queue.manage')->group(function () {
        Route::get('queue', QueueBoard::class)->name('queue.board');
    });

    Route::middleware('permission:opd.manage')->group(function () {
        Route::get('opd/consultation', Consultation::class)->name('opd.consultation');
    });

    Route::middleware('permission:billing.manage')->group(function () {
        Route::get('billing', BillingPending::class)->name('billing.pending');
        Route::get('billing/visits/{visit}/create', BillingCreate::class)->name('billing.create');
        Route::get('billing/invoices/{invoice}', BillingShow::class)->name('billing.show');
    });

    Route::middleware('permission:diagnostics.manage')->group(function () {
        Route::get('diagnostics', DiagnosticsPending::class)->name('diagnostics.pending');
        Route::get('diagnostics/visits/{visit}/order', DiagnosticsOrder::class)->name('diagnostics.order');
        Route::get('diagnostics/orders/{order}', DiagnosticsShow::class)->name('diagnostics.show');
    });

    Route::middleware('permission:pharmacy.manage')->group(function () {
        Route::get('pharmacy', PharmacyPending::class)->name('pharmacy.pending');
        Route::get('pharmacy/drugs', PharmacyDrugs::class)->name('pharmacy.drugs');
        Route::get('pharmacy/visits/{visit}/prescribe', PharmacyPrescribe::class)->name('pharmacy.prescribe');
        Route::get('pharmacy/prescriptions/{prescription}', PharmacyShow::class)->name('pharmacy.show');
    });

    Route::middleware('permission:inpatient.manage')->group(function () {
        Route::get('inpatient', InpatientBoard::class)->name('inpatient.board');
        Route::get('inpatient/wards', InpatientWards::class)->name('inpatient.wards');
        Route::get('inpatient/visits/{visit}/admit', InpatientAdmit::class)->name('inpatient.admit');
    });
});

require __DIR__.'/auth.php';

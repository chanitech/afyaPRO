<div>
    <style>
        @font-face {
            font-family: 'Libre Barcode 39';
            src: url('{{ asset('fonts/LibreBarcode39-Regular.woff2') }}') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        .wristband {
            width: 190mm;
            height: 25mm;
            margin: 0 auto;
            padding: 4px 10px;
            box-sizing: border-box;
            border: 1px dashed #ccc;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .wristband .details { font-size: 11px; line-height: 1.4; white-space: nowrap; }
        .wristband .details .name { font-size: 14px; font-weight: bold; }
        .wristband .barcode-code39 { font-family: 'Libre Barcode 39', monospace; font-size: 30px; line-height: 1; }
        .wristband .patient-number { font-size: 10px; text-align: center; letter-spacing: 0.1em; }

        @media print {
            @page { size: 190mm 25mm; margin: 0; }
            .wristband { border: none; }
        }
    </style>

    <div class="wristband">
        <div class="details">
            <div class="name">{{ $patient->fullName() }}</div>
            <div>
                {{ ucfirst($patient->sex) }} &middot; DOB {{ $patient->date_of_birth->format('d M Y') }}
                @if ($patient->blood_group)
                    &middot; Blood {{ $patient->blood_group }}
                @endif
                &middot; {{ $patient->facility->name }}
            </div>
        </div>
        <div>
            <x-barcode :value="$patient->patient_number" />
            <div class="patient-number">{{ $patient->patient_number }}</div>
        </div>
    </div>
</div>

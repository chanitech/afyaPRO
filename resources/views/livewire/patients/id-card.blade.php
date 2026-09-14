<div>
    <style>
        @font-face {
            font-family: 'Libre Barcode 39';
            src: url('{{ asset('fonts/LibreBarcode39-Regular.woff2') }}') format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        .id-card {
            width: 86mm;
            height: 54mm;
            margin: 0 auto;
            padding: 10px 14px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .id-card .facility { font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #555; }
        .id-card .name { font-size: 16px; font-weight: bold; margin: 4px 0 2px; }
        .id-card .meta { font-size: 11px; color: #333; line-height: 1.4; }
        .id-card .barcode-code39 { font-family: 'Libre Barcode 39', monospace; font-size: 34px; line-height: 1; display: block; text-align: center; }
        .id-card .patient-number { font-size: 11px; text-align: center; letter-spacing: 0.1em; }

        @media print {
            @page { size: 86mm 54mm; margin: 0; }
            .id-card { border: none; }
        }
    </style>

    <div class="id-card">
        <div class="facility">{{ $patient->facility->name }}</div>
        <div>
            <div class="name">{{ $patient->fullName() }}</div>
            <div class="meta">
                {{ ucfirst($patient->sex) }}, {{ $patient->age() }} yrs &middot; DOB {{ $patient->date_of_birth->format('d M Y') }}
                @if ($patient->blood_group)
                    &middot; Blood {{ $patient->blood_group }}
                @endif
            </div>
            @if ($patient->nhif_card_number)
                <div class="meta">NHIF: {{ $patient->nhif_card_number }}</div>
            @endif
            @if ($patient->nssf_member_number)
                <div class="meta">NSSF: {{ $patient->nssf_member_number }}</div>
            @endif
        </div>
        <div>
            <x-barcode :value="$patient->patient_number" />
            <div class="patient-number">{{ $patient->patient_number }}</div>
        </div>
    </div>
</div>

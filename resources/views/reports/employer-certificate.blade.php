<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Accreditation - {{ $employer->company_name }}</title>
    <style>
        /* Base Reset & Fonts */
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Exact A4 Paper Layout */
        .certificate-paper {
            background-color: #ffffff;
            width: 210mm;
            height: 297mm;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            padding: 25mm 20mm 20mm 20mm;
            /* Padding to match the doc */
            box-sizing: border-box;
            overflow: hidden;
        }

        /* Left Red and Blue Vertical Border */
        .certificate-paper::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 12mm;
            /* Distance from left edge */
            width: 4mm;
            /* Thickness of the stripes */
            background: linear-gradient(to right, #002b7f 50%, #ce1126 50%);
            z-index: 10;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logos {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .logos img {
            height: 70px;
            width: 70px;
            object-fit: contain;
        }

        .header p {
            margin: 0;
            line-height: 1.3;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            /* Matches sample */
        }

        .header .dept {
            font-size: 14.5px;
        }

        .header .address {
            font-family: 'Times New Roman', Times, serif;
            /* Matches sample */
            font-style: italic;
            font-size: 13px;
            margin-top: 5px;
        }

        /* Main Title */
        .title {
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 30px 0 10px 0;
            text-transform: uppercase;
        }

        /* Body Content */
        .body-text {
            text-align: center;
            font-size: 14.5px;
            line-height: 1.5;
        }

        .company-name {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 2px;
        }

        .reg-no {
            font-family: 'Times New Roman', Times, serif;
            font-style: italic;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Checklist */
        .checklist-container {
            width: 80%;
            /* Indented like the sample */
            margin: 25px auto;
            text-align: left;
        }

        .checklist {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: inline-block;
        }

        .checklist li {
            font-size: 14.5px;
            margin-bottom: 4px;
            display: flex;
            align-items: flex-start;
            color: #000;
        }

        /* Faded style for missing documents */
        .checklist li.missing {
            color: #9ca3af;
        }

        .checklist li .check-icon {
            font-family: Arial, Helvetica, sans-serif;
            margin-right: 15px;
            font-weight: bold;
            font-size: 16px;
            width: 16px;
            display: inline-block;
            text-align: center;
        }

        /* Validity */
        .validity {
            font-weight: bold;
            font-size: 14.5px;
            margin-top: 30px;
            text-align: center;
        }

        /* Signatory */
        .signatory {
            margin-top: 50px;
            text-align: center;
        }

        .signatory .name {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            font-size: 15px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .signatory .title {
            font-size: 14px;
            font-style: italic;
            letter-spacing: normal;
            margin: 0;
            text-transform: uppercase;
            /* Matches sample formatting */
        }

        /* Print Controls */
        .print-btn {
            margin-bottom: 20px;
            padding: 12px 24px;
            background-color: #1e293b;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background 0.2s;
        }

        .print-btn:hover {
            background-color: #0f172a;
        }

        /* Print Media Query */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
                display: block;
            }

            .print-btn {
                display: none !important;
            }

            .certificate-paper {
                box-shadow: none;
                width: 210mm;
                height: 297mm;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Print Button -->
    <button onclick="window.print()" class="print-btn">
        Print / Save as PDF
    </button>

    <!-- Paper Container -->
    <div class="certificate-paper">

        <!-- Header Logos & Text -->
        <div class="header">
            <div class="logos">
                <img src="{{ asset('images/cebu.png') }}" alt="Cebu City Logo">
                <img src="{{ asset('images/dmdp.png') }}" alt="DMDP Logo">
            </div>
            <p>Republic of the Philippines</p>
            <p class="dept">CEBU CITY GOVERNMENT</p>
            <p class="dept">DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT</p>
            <p class="address">Ramos Public Market Bldg., Echavez Ext., Cor D. Jakosalem St., Cogon Ramos, Cebu City</p>
        </div>

        <!-- Title -->
        <div class="title">
            CERTIFICATE OF ACCREDITATION
        </div>

        <!-- Body Content -->
        <div class="body-text">
            <p>This is to certify that</p>

            <div class="company-name">{{ $employer->company_name }}</div>
            <div class="reg-no">Registration No. {{ $accreditation->certificate_no ?? ('DMDP-EMP-' . date('Y') . '-' . str_pad($employer->employer_id, 5, '0', STR_PAD_LEFT)) }}</div>

            <p>
                with business address at
                @if(!empty($employer->company_address))
                {{ $employer->company_address }}
                @else
                <span style="display: inline-block; border-bottom: 1px solid #000; min-width: 350px;">&nbsp;</span>
                @endif
                is a duly accredited Employer-Partner of the Department of Manpower<br>
                Development and Placement.
            </p>

            <p>This accreditation is granted after the company has satisfactorily complied with and submitted the<br>required documentary requirements, as evaluated and verified by the PESO Office:</p>

            @php
            // 1. Get the uploaded documents from the database
            $docs = $accreditation->documents ?? [];
            if (is_string($docs)) {
            $docs = json_decode($docs, true) ?: [];
            }

            // 2. Helper function to check if a file exists in the JSON array
            $hasDoc = function($keywords) use ($docs) {
            foreach ((array)$docs as $key => $val) {
            if (!$val) continue;
            foreach ((array)$keywords as $kw) {
            if (str_contains(strtolower($key), strtolower($kw))) {
            return true;
            }
            }
            }
            return false;
            };

            // 3. Define the exact checklist requirements
            $requirements = [
            ['label' => 'BIR Certificate of Registration (BIR Form 2303)', 'keys' => ['bir', '2303']],
            ['label' => 'SEC Registration or DTI Registration', 'keys' => ['sec', 'dti']],
            ['label' => "Mayor's Business Permit", 'keys' => ['mayor', 'permit', 'business']],
            ['label' => 'PhilJobNet Proof of Registration', 'keys' => ['philjobnet']],
            ['label' => 'DOLE License / Department Order No. 174', 'keys' => ['dole', '174']],
            ['label' => 'DMW License', 'keys' => ['dmw_license', 'dmw']],
            ['label' => 'DMW Approved and Validated Job Orders', 'keys' => ['order', 'job_order']]
            ];
            @endphp

            <!-- Dynamic Checklist -->
            <div class="checklist-container">
                <ul class="checklist">
                    @foreach($requirements as $req)
                    @php $passed = $hasDoc($req['keys']); @endphp
                    <li class="{{ $passed ? '' : 'missing' }}">
                        <span class="check-icon">{{ $passed ? '✓' : '' }}</span>
                        <span>{{ $req['label'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <p>This accreditation authorizes the above-named company to participate in PESO-facilitated employment<br>programs and services, including job referrals, job fairs, and other employment facilitation activities, in<br>accordance with existing laws, rules, and regulations.</p>

            <div class="validity">
                Valid from <span style="font-weight: normal;">{{ $accreditation->certificate_issued_at ? date('F d, Y', strtotime($accreditation->certificate_issued_at)) : date('F d, Y') }}</span> to <span style="font-weight: normal;">{{ $accreditation->certificate_issued_at ? date('F d, Y', strtotime($accreditation->certificate_issued_at . ' +1 year')) : date('F d, Y', strtotime('+1 year')) }}</span>, unless earlier revoked or suspended for cause.
            </div>
        </div>

        <!-- Signatory -->
        <div class="signatory">
            <div class="name">ANTHONY V. AGUHAR, PhD</div>
            <div class="title">CG Acting Department Head (PESO Manager)</div>
        </div>

    </div>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Training - {{ $enrollment->first_name }} {{ $enrollment->last_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* A4 Portrait Paper with the Canva Design */
        .certificate-container {
            background-color: #ffffff;
            width: 210mm;
            height: 297mm;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            box-sizing: border-box;

            /* THIS LOADS YOUR EXACT BORDER IMAGE */
            background-image: url('/images/border-design.png');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
        }

        .certificate-inner {
            position: absolute;
            top: 10%;
            left: 12%;
            right: 12%;
            bottom: 10%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }

        .header-logos {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .header-logos img {
            height: 60px;
            width: 60px;
            object-fit: contain;
        }

        .header-text h2 {
            font-family: 'Playfair Display', serif;
            color: #0a1d3f;
            margin: 0;
        }

        /* Title */
        .title {
            font-family: 'Cinzel', serif;
            font-size: 38px;
            color: #0a1d3f;
            font-weight: 800;
            letter-spacing: 2px;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
        }

        .is-awarded {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 16px;
            color: #0a1d3f;
            margin-bottom: 15px;
        }

        /* Name */
        .name-box {
            margin: 15px 0;
        }

        .name-box h2 {
            font-family: 'Cinzel', serif;
            font-size: 34px;
            color: #0a1d3f;
            margin: 0;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Body Text */
        .body-content {
            font-size: 14px;
            color: #1e293b;
            line-height: 1.6;
            font-weight: 500;
        }

        .course-title {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            color: #0a1d3f;
            font-weight: 700;
            margin: 10px 0;
            text-transform: uppercase;
        }

        /* Standard CSS 2-Column Layout */
        .two-column-list {
            display: flex;
            justify-content: space-between;
            text-align: left;
            margin: 15px auto 0;
            width: 100%;
            max-width: 600px;
            font-size: 12px;
            line-height: 1.6;
        }

        .column {
            width: 48%;
        }

        .list-item {
            position: relative;
            padding-left: 20px;
            margin-bottom: 5px;
            color: #1e293b;
            font-weight: 500;
        }

        .list-item::before {
            content: '✦';
            position: absolute;
            left: 0;
            color: #0a1d3f;
            font-size: 12px;
        }

        /* 1-Column Layout */
        .one-column-list {
            text-align: left;
            display: inline-block;
            margin: 15px auto 0;
        }

        /* Dynamic sizing based on count */
        .list-dense {
            font-size: 10px !important;
            line-height: 1.3 !important;
        }

        .list-dense .list-item {
            font-size: 10px !important;
            margin-bottom: 2px !important;
        }

        /* Footer (Signature) */
        .footer {
            font-size: 14px;
            color: #334155;
        }

        .signatory h3 {
            font-family: 'Cinzel', serif;
            font-size: 18px;
            color: #0a1d3f;
            margin: 5px 0;
            font-weight: 800;
        }

        /* Print Settings */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
                display: block;
            }

            .print-btn {
                display: none !important;
            }

            .certificate-container {
                box-shadow: none;
                page-break-after: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }

        .print-btn {
            margin-bottom: 20px;
            padding: 12px 24px;
            background-color: #0a1d3f;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="print-btn">Print / Save as PDF</button>

    @php
    $certContent = $enrollment->cert_content ?? null;
    if (is_string($certContent)) { $certContent = json_decode($certContent, true); }

    $certTitle = $certContent['title'] ?? 'CERTIFICATE OF TRAINING';
    $competencies = $certContent['competencies'] ?? [];
    $compCount = count($competencies);

    // Switch to 2 columns ONLY if there are 15 or more
    $isTwoColumn = $compCount >= 15;

    if ($isTwoColumn) {
    $half = ceil($compCount / 2);
    $leftColumn = array_slice($competencies, 0, $half);
    $rightColumn = array_slice($competencies, $half);
    }

    // If 14 or below, shrink the font size slightly to fit the page
    $fontSize = '13px';
    $lineHeight = '1.8';

    if ($compCount >= 12 && $compCount <= 14) {
        $fontSize='11px' ;
        $lineHeight='1.5' ;
        }

        $startDate=$enrollment->start_date ? date('M d', strtotime($enrollment->start_date)) : 'N/A';
        $endDate = $enrollment->end_date ? date('M d, Y', strtotime($enrollment->end_date)) : date('M d, Y');
        $issueDate = date('jS \d\a\y \of F, Y', strtotime($enrollment->certificate_issued_at ?? now()));
        @endphp

        <div class="certificate-container">
            <div class="certificate-inner">

                <!-- Header (Logos & Official Text) -->
                <div>
                    <div class="header-logos">
                        <img src="{{ asset('images/cebu.png') }}" alt="Cebu">
                        <img src="{{ asset('images/dmdp.png') }}" alt="DMDP">
                    </div>
                    <div class="header-text">
                        <h2 style="font-size: 14px; font-weight: 500;">Republic of the Philippines</h2>
                        <h2 style="font-size: 16px; font-weight: 500;">City of Cebu</h2>
                        <h2 style="font-size: 15px; font-weight: 700;">Department of Manpower Development and Placement</h2>
                    </div>
                </div>

                <!-- Title & Name -->
                <div>
                    <div class="title">{{ $certTitle }}</div>
                    <div class="is-awarded">is awarded to</div>
                    <div class="name-box">
                        <h2>{{ $enrollment->first_name }} {{ $enrollment->last_name }}</h2>
                    </div>
                </div>

                <!-- Body -->
                <div class="body-content">
                    <p style="margin: 0;">for having completed the competency based training program in</p>
                    <div class="course-title">{{ $enrollment->course_title }}</div>
                    <p style="margin: 0;">conducted on <strong>{{ $startDate }} to {{ $endDate }}</strong> and satisfactorily achieved the requirements of<br>the following units of competency:</p>

                    @if($isTwoColumn)
                    <!-- 2-Column Layout -->
                    <div class="two-column-list {{ $compCount > 18 ? 'list-dense' : '' }}">
                        <div class="column">
                            @foreach($leftColumn as $comp)
                            <div class="list-item">{{ $comp }}</div>
                            @endforeach
                        </div>
                        <div class="column">
                            @foreach($rightColumn as $comp)
                            <div class="list-item">{{ $comp }}</div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <!-- 1-Column Layout -->
                    <div class="one-column-list" style="font-size: {{ $fontSize }}; line-height: {{ $lineHeight }};">
                        @foreach($competencies as $comp)
                        <div class="list-item" style="font-size: {{ $fontSize }}; margin-bottom: {{ $compCount >= 12 ? '2px' : '5px' }};">{{ $comp }}</div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Footer (Moved outside body-content so it pins to the bottom) -->
                <div class="footer">
                    <p style="font-family: 'Playfair Display', serif; margin-bottom: 20px;">Given this <strong>{{ $issueDate }}</strong> at Cebu City, Philippines</p>
                    <div class="signatory">
                        <h3>ANTHONY V. AGUHAR, PhD</h3>
                        <p style="font-size: 12px; margin: 0;">CG Acting Department Head (PESO Manager)</p>
                    </div>
                </div>

            </div>
        </div>

</body>

</html>
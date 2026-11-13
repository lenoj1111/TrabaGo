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

        .certificate-container {
            background-color: #ffffff;
            width: 210mm;
            height: 297mm;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            box-sizing: border-box;
            border: 12px solid #0a1d3f;
        }

        .certificate-inner {
            position: absolute;
            inset: 8px;
            border: 2px solid #0a1d3f;
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }

        /* Header */
        .logos {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .logos img {
            height: 70px;
            width: 70px;
            object-fit: contain;
        }

        .header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: #0a1d3f;
            margin: 0;
            font-weight: 500;
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: #0a1d3f;
            margin: 2px 0;
            font-weight: 700;
        }

        /* Title */
        .title {
            font-family: 'Cinzel', serif;
            font-size: 38px;
            color: #0a1d3f;
            font-weight: 800;
            letter-spacing: 1.5px;
            margin: 25px 0 10px 0;
            text-transform: uppercase;
        }

        .is-awarded {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 16px;
            color: #0a1d3f;
            margin-bottom: 15px;
            font-weight: 500;
        }

        /* Name Box */
        .name-box {
            background: #f8fafc;
            padding: 15px 0;
            margin: 0 40px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
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

        /* Body */
        .body-content {
            font-size: 14px;
            color: #1e293b;
            line-height: 1.6;
            margin-top: 20px;
            font-weight: 500;
        }

        .body-content .course-title {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            color: #0a1d3f;
            font-weight: 800;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Highlights */
        .highlight-date {
            font-weight: 700;
            color: #000;
        }

        /* Competencies */
        .competencies-list {
            text-align: left;
            display: inline-block;
            margin: 20px auto;
            font-size: 13px;
            color: #334155;
            line-height: 1.8;
            font-weight: 500;
        }

        .competencies-list li {
            list-style-type: none;
            position: relative;
            padding-left: 25px;
        }

        .competencies-list li::before {
            content: '✦';
            position: absolute;
            left: 0;
            color: #0a1d3f;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
        }

        .given-date {
            font-family: 'Playfair Display', serif;
            font-size: 14px;
            color: #334155;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .given-date strong {
            font-weight: 800;
            color: #0a1d3f;
        }

        .signatory {
            display: inline-block;
            text-align: center;
        }

        .signatory h3 {
            font-family: 'Cinzel', serif;
            font-size: 18px;
            color: #0a1d3f;
            margin: 0;
            font-weight: 800;
        }

        .signatory p {
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            color: #0a1d3f;
            margin: 2px 0 0 0;
            font-weight: 600;
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

    // Decode the JSON string if it exists
    if (is_string($certContent)) {
    $certContent = json_decode($certContent, true);
    }

    $certTitle = $certContent['title'] ?? 'CERTIFICATE OF TRAINING';

    // THIS IS THE CRITICAL PART
    $competencies = [];
    if (isset($certContent['competencies']) && is_array($certContent['competencies'])) {
    $competencies = $certContent['competencies'];
    }

    $startDate = $enrollment->start_date ? date('M d', strtotime($enrollment->start_date)) : 'N/A';
    $endDate = $enrollment->end_date ? date('M d, Y', strtotime($enrollment->end_date)) : date('M d, Y');
    $issueDate = date('jS \d\a\y \of F, Y', strtotime($enrollment->certificate_issued_at ?? now()));
    @endphp

    <div class="certificate-container">
        <div class="certificate-inner">
            <div class="header">
                <div class="logos">
                    <img src="{{ asset('images/cebu.png') }}" alt="Cebu City">
                    <img src="{{ asset('images/dmdp.png') }}" alt="DMDP">
                </div>
                <h2>Republic of the Philippines</h2>
                <h1>City of Cebu</h1>
                <h1>Department of Manpower Development and Placement</h1>
            </div>

            <div>
                <div class="title">{{ $certTitle }}</div>
                <div class="is-awarded">is awarded to</div>
                <div class="name-box">
                    <h2>{{ $enrollment->first_name }} {{ $enrollment->last_name }}</h2>
                </div>
            </div>

            <div class="body-content">
                <p>for having completed the competency based training program in</p>
                <div class="course-title">{{ $enrollment->course_title }}</div>
                <p>conducted on <span class="highlight-date">{{ $startDate }} to {{ $endDate }}</span> and satisfactorily achieved the requirements of<br>the following units of competency:</p>

                <ul class="competencies-list">
                    @foreach($competencies as $comp)
                    <li>{{ $comp }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="footer">
                <div class="given-date">Given this <strong>{{ $issueDate }}</strong> at Cebu City, Philippines</div>
                <div class="signatory">
                    <h3>ANTHONY V. AGUHAR, PhD</h3>
                    <p>CG Acting Department Head (PESO Manager)</p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
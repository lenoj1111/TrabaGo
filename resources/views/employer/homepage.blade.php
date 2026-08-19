@extends('layouts.employer')

@section('title', 'Employer Homepage')

@section('content')
<div style="max-width: 1100px; margin: 40px auto; padding: 20px;">
    <h1 style="font-size: 32px; margin-bottom: 10px;">Welcome to TrabaGo</h1>
    <p style="color: #666; margin-bottom: 30px;">Manage your company profile, job postings, and applications.</p>

    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px;">
        <div style="flex:1; min-width:200px; border:1px solid #ddd; border-radius:10px; padding:20px; background: #f9f9f9;">
            <h3 style="margin:0 0 10px 0; color:#333;">Active Job Postings</h3>
            <p style="font-size:30px; font-weight:bold; margin:0; color:#2563eb;">0</p>
        </div>
        <div style="flex:1; min-width:200px; border:1px solid #ddd; border-radius:10px; padding:20px; background: #f9f9f9;">
            <h3 style="margin:0 0 10px 0; color:#333;">Applications</h3>
            <p style="font-size:30px; font-weight:bold; margin:0; color:#16a34a;">0</p>
        </div>
        <div style="flex:1; min-width:200px; border:1px solid #ddd; border-radius:10px; padding:20px; background: #f9f9f9;">
            <h3 style="margin:0 0 10px 0; color:#333;">Accreditation</h3>
            <p style="font-size:30px; font-weight:bold; margin:0; color:#dc2626;">Pending</p>
        </div>
    </div>

    <div style="border:1px solid #ddd; border-radius:10px; padding:20px; background: white;">
        <h2 style="margin-top:0; margin-bottom:12px; color:#333;">Next steps</h2>
        <p style="margin:0; color:#666;">Complete your company profile and accreditation to start posting jobs.</p>
    </div>
</div>
@endsection

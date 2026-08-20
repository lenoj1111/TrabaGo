@extends('layouts.jobseeker')

@section('title', 'Jobseeker Homepage')

@section('content')
<div style="max-width: 1100px; margin: 40px auto; padding: 20px;">
    <h1 style="font-size: 32px; margin-bottom: 10px;">
        Welcome to TrabaGo
    </h1>

    <p style="color: #666; margin-bottom: 30px;">
        This is the Jobseeker Homepage.
    </p>

    <!-- Stats Cards -->
    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px;">
        <div style="flex:1; min-width:200px; border:1px solid #ddd; border-radius:10px; padding:20px; background: #f9f9f9;">
            <h3 style="margin:0 0 10px 0; color:#333;">Available Jobs</h3>
            <p style="font-size:30px; font-weight:bold; margin:0; color:#2563eb;">25</p>
        </div>

        <div style="flex:1; min-width:200px; border:1px solid #ddd; border-radius:10px; padding:20px; background: #f9f9f9;">
            <h3 style="margin:0 0 10px 0; color:#333;">Applications</h3>
            <p style="font-size:30px; font-weight:bold; margin:0; color:#16a34a;">4</p>
        </div>

        <div style="flex:1; min-width:200px; border:1px solid #ddd; border-radius:10px; padding:20px; background: #f9f9f9;">
            <h3 style="margin:0 0 10px 0; color:#333;">Trainings</h3>
            <p style="font-size:30px; font-weight:bold; margin:0; color:#dc2626;">3</p>
        </div>
    </div>

    <!-- Recent Job Openings -->
    <div style="border:1px solid #ddd; border-radius:10px; padding:20px; background: white;">
        <h2 style="margin-top:0; margin-bottom:20px; color:#333;">Recent Job Openings</h2>

        <div style="padding: 15px 0; border-bottom: 1px solid #eee;">
            <p style="margin:0 0 5px 0; font-weight:bold; font-size:18px;">Web Developer</p>
            <p style="margin:0; color:#666;">ABC Company • Cebu City</p>
        </div>

        <div style="padding: 15px 0; border-bottom: 1px solid #eee;">
            <p style="margin:0 0 5px 0; font-weight:bold; font-size:18px;">Graphic Designer</p>
            <p style="margin:0; color:#666;">XYZ Solutions • Mandaue City</p>
        </div>

        <div style="padding: 15px 0;">
            <p style="margin:0 0 5px 0; font-weight:bold; font-size:18px;">Office Staff</p>
            <p style="margin:0; color:#666;">City Government • Cebu City</p>
        </div>
    </div>
</div>
@endsection
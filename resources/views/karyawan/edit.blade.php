@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 30px auto; padding: 0 15px; font-family: sans-serif;">
    <div style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        
        <!-- Header Hijau -->
        <div style="background-color: #6c9364; color: white; padding: 16px 24px; display: flex; align-items: center; gap: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
            </svg>
            <h3 style="margin: 0; font-size: 20px; font-weight: 600;">Edit Data Karyawan</h3>
        </div>

        <!-- Form Body -->
        <div style="padding: 24px;">
            <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('karyawan._form')

            </form>
        </div>

    </div>
</div>
@endsection
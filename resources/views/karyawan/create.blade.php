@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
    <div style="width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 75vh; padding: 20px 0;">
        <form action="{{ route('karyawan.store') }}" method="POST" style="width: 100%; display: flex; justify-content: center;">
            @csrf
            @include('karyawan._form')
        </form>
    </div>
@endsection
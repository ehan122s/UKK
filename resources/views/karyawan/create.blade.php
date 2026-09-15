@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('karyawan.store') }}">
            @csrf
            @php($karyawan = null)
            @include('karyawan._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection

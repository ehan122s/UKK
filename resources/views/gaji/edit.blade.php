@extends('layouts.app')

@section('title', 'Edit Data Gaji')

@section('content')
    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('gaji.update', $gaji) }}">
            @csrf
            @method('PUT')
            @include('gaji._form')
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
@endsection

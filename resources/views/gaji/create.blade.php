@extends('layouts.app')

@section('title', 'Input Gaji')

@section('content')
    <div class="card" style="max-width:480px;">
        <form method="POST" action="{{ route('gaji.store') }}">
            @csrf
            @php($gaji = null)
            @include('gaji._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection

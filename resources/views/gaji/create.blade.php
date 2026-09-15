@extends('layouts.app')

@section('title', 'Input Data Gaji')

@section('content')
    <div style="max-width: 550px; margin: 20px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden;">
        
        <div style="background-color: #198754; color: white; padding: 18px 24px; font-weight: bold; font-size: 18px;">
            <span>➕ Generate Slip Gaji Karyawan</span>
        </div>

        <form action="{{ route('gaji.store') }}" method="POST" style="padding: 24px;">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="karyawan_id" style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Pilih Karyawan</label>
                <select id="karyawan_id" name="karyawan_id" required style="width: 100%; padding: 10px 14px; border: 1px solid #e0e0e0; border-radius: 6px; background-color: #f9f9f9; font-size: 14px;">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawans as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->nik }}) - {{ $k->jabatan }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <a href="{{ route('gaji.index') }}" class="btn" style="background-color: #2b3035; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">&larr; Batal</a>
                <button type="submit" class="btn" style="background-color: #198754; color: white; padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; font-size: 14px;">💾 Generate Gaji</button>
            </div>
        </form>
    </div>
@endsection
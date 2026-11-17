@extends('layouts.auth')

@section('title', 'Login Siswa')

@section('content')
    @if(session('timeout_message'))
        <div class="mb-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            {{ session('timeout_message') }}
        </div>
    @endif

    <livewire:auth.login-siswa />
@endsection

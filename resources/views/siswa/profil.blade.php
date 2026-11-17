@extends('siswa.layouts.app')

@section('title', 'Profil Siswa')
@section('page-title', 'Profil')

@section('content')
<div class="max-w-2xl">
    @livewire('siswa.profil-siswa')
</div>
@endsection

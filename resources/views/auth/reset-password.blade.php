@extends('layouts.app')

@section('content')
    <livewire:reset-password :token="$token" :email="$email" />
@endsection


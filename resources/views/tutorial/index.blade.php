@extends('app', [
    'section_title' => "Cara Penggunaan Aplikasi Tracking",
    'section_lead' => "Cara Penggunaan Aplikasi Tracking",
    'section_headers' => [
        ["menu" => "Tutorial", "is_active" => true, 'href' => route('tutorial')],
    ]
])

@section('content')
    @include('tutorial.section.format-excel')
    @include('tutorial.section.create-version')
@endsection
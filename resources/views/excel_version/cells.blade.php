@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Cell", "is_active" => true, 'href' => ""],
    ]
])

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success mb-2">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger mb-2">{{ Session::get('error') }}</div>
    @endif
    <form action="{{ route('version.update-cell-name', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'type' => $type]) }}" method="post">
        @csrf
        @if(URLHelper::has('edit'))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header row">
                <h4>
                    @if(URLHelper::has('input'))
                        Cell Input Data
                    @else 
                        Cell Output Data
                    @endif
                </h4>
            </div>
            <div class="card-body">
                @php
                    $pattern = '/^[A-Za-z]+[0-9]+$/';
                @endphp
                @foreach ($cells as $value)
                    <div class="form-group row">
                        <input name="{{ $value->id }}" type="text" class="form-control col-2 text-center @if(!preg_match($pattern, $value->cell)) text-danger is-invalid @endif" value="{{ $value->cell }}">
                        <input name="name-{{ $value->id }}" type="text" class="form-control col" placeholder="Nama Cell {{ $value->cell }}" value="{{ $value->cell_name ?? '' }}">
                    </div>
                @endforeach
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Simulasi
                </button>
            </div>
        </div>
    </form>
@endsection
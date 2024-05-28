@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => true, 'href' => route('home')],
    ]
])

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success mb-2">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger mb-2">{{ Session::get('error') }}</div>
    @endif
    @php
        $url = '';
        if(URLHelper::has('edit')){
            $url = route('excel.update', ['alkes_id' => $alkesId]);
        }else{
            $url = route('excel.store');
        }
    @endphp

    <form action="{{ $url }}" method="post" enctype="multipart/form-data">
        @csrf
        @if(URLHelper::has('edit'))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header row">
                <h4>Form Alkes</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col">
                        <label for=""><b>Nama Alkes</b></label>
                        <input
                            name="name"
                            type="text"
                            class="form-control"
                            placeholder="Masukkan Nama Alkes"
                            value="{{ $alkes->name ?? '' }}">
                    </div>
                </div>
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-save mr-1"></i>
                    Simpan
                </button>
            </div>
        </div>
    </form>
@endsection

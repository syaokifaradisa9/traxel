@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Tambah", "is_active" => true, 'href' => ""],
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
            $url = route('version.update', ['alkes_id' => $alkesId, 'version_id' => $version->id]);
        }else{
            $url = route('version.store', ['alkes_id' => $alkesId]);
        }
    @endphp
    <form action="{{ $url }}" method="post" enctype="multipart/form-data">
        @csrf
        @if(URLHelper::has('edit'))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header row">
                <h4>Nama Skema Simulasi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col">
                        <label for=""><b>Nama Versi Excel</b></label>
                        <input name="version_name" type="text" class="form-control" placeholder="Masukkan Nama Versi Excel" value="{{ $version->version_name ?? '' }}">
                        <small class="text-danger">
                            Format versi mohon spasi dipisah dengan tanda -
                        </small>
                    </div>
                    <div class="form-group col">
                        <label for=""><b>File Excel</b></label>
                        <input name="file" type="file" class="form-control">
                        @if(URLHelper::has('edit'))
                            <small class="text-danger">
                                Excel sebelumnya ada <a class="text-danger font-weight-bold" href="{{ asset('excel/' . $version->alkes->excel_name."-".$version->version_name.".xlsx") }}">disini</a> (kosongkan jika tidak ingin mengganti file excel)
                            </small>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for=""><b>Cell Input</b></label>
                    <input name="input_cell" type="text" class="form-control" placeholder="Masukkan Cell Input Excel (Pisahkan dengan tanda koma)" value="{{ $input_cells ?? '' }}">
                </div>
                <div class="form-group">
                    <label for=""><b>Cell Output</b></label>
                    <input name="output_cell" type="text" class="form-control" placeholder="Masukkan Cell Output Excel (Pisahkan dengan tanda koma)"  value="{{ $output_cells ?? '' }}">
                </div>
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Simulasi
                </button>
            </div>
        </div>
    </form>
@endsection
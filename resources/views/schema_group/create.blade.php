@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Group Simulasi", "is_active" => false, 'href' => route('version.schema_group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])],
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
            $url = route('version.schema.update-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'schema_id' => $schema->id]);
        }else{
            $url = route('version.schema_group.store-schemagroup', ['alkes_id' => $alkesId, 'version_id' => $versionId]);
        }
    @endphp
    <form action="{{ $url }}" method="post">
        @csrf
        @if(URLHelper::has('edit'))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header row">
                <h4>Nama Skema Simulasi</h4>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input name="simulation_name" type="text" class="form-control" placeholder="Masukkan Nama Simulasi Excel" value="{{ old('simulation_name') ?? $schema->name ?? '' }}">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header row">
                <h4>Input Data</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($inputCells as $inputCell)
                        @php
                            $value = $inputCellValues->where('cell', $inputCell->cell)->first()['value'] ?? '';
                            $form_name = "input-".$inputCell->id;
                        @endphp
                        <div class="form-group col-3">
                            <label><b>{{ $inputCell->cell_name . " (" . $inputCell->cell .")" }}</b></label>
                            <input name="{{ $form_name }}" type="text" class="form-control" placeholder="NIlai Excel Cell {{ $inputCell->cell }}" value="{{ old($form_name) ?? $value }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <button class="btn btn-primary w-100" type="submit">
                <i class="fas fa-save mr-1"></i>
                Simpan Simulasi
              </button>
        </div>
    </form>
@endsection
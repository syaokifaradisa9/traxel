@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Group Simulasi", "is_active" => false, 'href' => route('version.schema_group.index',  ['alkes_id' => $alkesId, 'version_id' => $versionId])],
        ["menu" => "Simulasi", "is_active" => false, 'href' => route('version.schema_group.schema.index', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId])],
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
        $url = route('version.schema_group.schema.update-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId,'schema_id' => $schema->id]);
    @endphp
    <form action="{{ $url }}" method="post">
        @csrf
        @if(URLHelper::has('edit'))
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header row">
                <h4>Ekspektasi Output Data</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($outputCells as $outputCell)
                        @php
                            if($outputCellValues ?? []){
                                $value = $outputCellValues->where("cell", $outputCell->cell)->first()['expected_value'] ?? '';
                            }else{
                                $value = '';
                            }

                            $form_name = "output-".$outputCell->id;
                        @endphp
                        <div class="form-group col-3">
                            <label><b>{{ $outputCell->cell_name . " (" . $outputCell->cell . ")" }}</b></label>
                            <input name="{{ $form_name }}" type="text" class="form-control" placeholder="Nilai Excel Cell {{ $outputCell->cell }}" value="{{ old($form_name) ?? $value }}">
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
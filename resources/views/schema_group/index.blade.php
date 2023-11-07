@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Group Simulasi", "is_active" => true, 'href' => route('version.calibrator-group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])],
    ]
])

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success mb-2">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger mb-2">{{ Session::get('error') }}</div>
    @endif
    <div class="card">
        <div class="card-header row">
            <div class="col-6">
                <h4>Tabel Group Skema Percobaan</h4>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('version.schema_group.create-schemagroup', ['alkes_id' => $alkesId, 'version_id' => $versionId]) }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Group Skema percobaan
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table-sm table-striped w-100" id="order-table">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px">No.</th>
                    <th class="text-center">Nama Group <br>Skema Percobaan</th>
                    <th class="text-center" style="width: 300px">Aksi</th>
                  </tr>
                </thead>
                @php
                    $percentages = [];
                @endphp
                <tbody>
                  @foreach ($test_schema_groups as $index => $value)
                      <tr>
                          <td class="text-center align-middle" style="width: 50px">{{ $index + 1 }}</td>
                          <td class="text-center align-middle">
                              {{ $value->name }}
                          </td>
                          <td class="text-center align-middle">
                              <div class="row">
                                  <a href="{{ route('version.schema_group.schema.index', ['alkes_id' => $alkesId, 'version_id' => $versionId, "group_id" => $value->id]) }}" class="btn btn-info col">
                                    <i class="fas fa-search mr-1"></i>
                                    Tracking
                                  </a>
                                    <a href="{{ route('version.schema_group.delete-schemagroup', [
                                        'alkes_id' => $alkesId,
                                        'version_id' => $versionId,
                                        "group_id" => $value->id]
                                    ) }}" class="btn btn-danger col">
                                        <i class="fas fa-trash-alt mr-1"></i>
                                        Hapus
                                  </a>
                              </div>
                          </td>
                      </tr>
                  @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Group Kalibrator", "is_active" => false, 'href' => route('version.calibrator-group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])],
        ["menu" => "Kalibrator", "is_active" => true, 'href' => ''],
    ]
])

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success mb-2">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger mb-2">{{ Session::get('error') }}</div>
    @endif
    
    <div class="card">
        <div class="card-body">
           <form action="{{ isset($calibrator) ? route('version.calibrator-group.calibrator.update', [
                'alkes_id' => $alkesId, 
                'version_id' => $versionId, 
                'group_id' => $groupId,
                'id' => $calibrator->id
            ]) : route('version.calibrator-group.calibrator.store', [
                'alkes_id' => $alkesId, 
                'version_id' => $versionId,
                'group_id' => $groupId
            ]) }}" method="post">
                @csrf
                @if(isset($calibrator))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="form-group col">
                        <label for=""><b>Nama Kalibrator</b></label>
                        <input name="name" type="text" class="form-control" value="{{ $calibrator->name ?? '' }}">
                    </div>
                    <div class="form-group col">
                        <label for=""><b>Merek</b></label>
                        <input name="merk" type="text" class="form-control" value="{{ $calibrator->merk ?? '' }}">
                    </div>
                    <div class="form-group col">
                        <label><b>Model/Tipe</b></label>
                        <select class="form-control select2 category-select" name="model_type" id="service_type">
                          <option value="" selected hidden>Pilih Model/Tipe</option>
                          <option value="Model" @if(($calibrator->model_type ?? '') == "Model") selected @endif>Model</option>
                          <option value="Tipe"  @if(($calibrator->model_type ?? '') == "Tipe")  selected @endif>Tipe</option>
                        </select>
                      </div>
                    <div class="form-group col">
                        <label for=""><b>Nama Model/Tipe</b></label>
                        <input name="model_type_name" type="text" class="form-control" value="{{ $calibrator->model_type_name ?? '' }}">
                    </div>
                    <div class="form-group col">
                        <label for=""><b>SN</b></label>
                        <input name="serial_number" type="text" class="form-control" value="{{ $calibrator->serial_number ?? '' }}">
                    </div>
                </div>
                <button class="btn btn-primary w-100" type="submit">
                    @if(isset($calibrator_group))
                        Ubah
                    @else
                        Simpan
                    @endif
                </button>
           </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header row">
            <div class="col">
                <h4>Tabel Kalibrator</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-4">
                <table class="table-sm table-striped w-100" id="order-table">
                  <thead>
                    <tr>
                        <th class="text-center" style="width: 50px">No.</th>
                        <th class="text-center">Nama Kalibrator</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($calibrators as $index => $value)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                               {{ $value->name. ", Merek : " . $value->merk . ", " . $value->model_type . " : " . $value->model_type_name. ", SN : " . $value->serial_number }}
                            </td>
                            <td class="text-center align-middle">
                                <div class="row mx-0 px-0">
                                    <a href="{{ route('version.calibrator-group.calibrator.edit', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId, 'id' => $value->id]) }}" class="btn btn-warning col">
                                        <i class="fas fa-edit"></i>
                                        Edit Kalibrator
                                    </a>
                                    <a href="{{ route('version.calibrator-group.calibrator.delete', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId, 'id' => $value->id]) }}" class="btn btn-danger btn-delete col">
                                        <i class="fas fa-trash-alt"></i>
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
    </div>
@endsection
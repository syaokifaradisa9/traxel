@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Group Kalibrator", "is_active" => true, 'href' => ''],
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
           <form action="{{ route('version.calibrator-group.import', ['alkes_id' => $alkesId, 'version_id' => $versionId]) }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for=""><b>File Json Kalibrator</b></label>
                    <input name="calibrator_file" type="file" class="form-control">
                    <p class="mt-0 text-danger">
                        * File Json dapat didownload
                        <a href="https://drive.google.com/drive/folders/11eJDzRD32TRjQP1S6NskOXnKbrZmFg2C?usp=drive_link" target="_blank">
                            <b>Disini</b>
                        </a>. Jika tidak ada, Mohon input manual dan jika selesai input manual mohon export dan upload ke link agar data dapat di gunakan lagi.
                    </p>
                </div>

                <button class="btn btn-primary w-100" type="submit">
                    Import
                </button>
           </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
           <form action="{{ isset($calibrator_group) ? route('version.calibrator-group.update', [
                'alkes_id' => $alkesId,
                'version_id' => $versionId,
                'group_id' => $calibrator_group->id
            ]) : route('version.calibrator-group.store', [
                'alkes_id' => $alkesId,
                'version_id' => $versionId
            ]) }}" method="post">
                @csrf
                @if(isset($calibrator_group))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="form-group col">
                        <label for=""><b>Nama Group Kalibrator</b></label>
                        <input name="name" type="text" class="form-control" placeholder="Masukkan Nama Group Kalibrator" value="{{ $calibrator_group->name ?? '' }}">
                    </div>
                    <div class="form-group col-2">
                        <label for=""><b>Cell ID</b></label>
                        <input name="cell_id" type="text" class="form-control" placeholder="Posisi pada Sheet ID" value="{{ $calibrator_group->cell_ID ?? '' }}">
                    </div>
                    <div class="form-group col-2">
                        <label for=""><b>Cell LH</b></label>
                        <input name="cell_lh" type="text" class="form-control" placeholder="Posisi Pada Sheet LH" value="{{ $calibrator_group->cell_LH ?? '' }}">
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
                <h4>Tabel Group Kalibrator</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-4">
                <table class="table-sm table-striped w-100" id="order-table">
                  <thead>
                    <tr>
                        <th class="text-center" style="width: 50px">No.</th>
                        <th class="text-center">Nama Group</th>
                        <th class="text-center">Cell ID</th>
                        <th class="text-center">Cell LH</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($group_calibrators as $index => $value)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                               {{ $value->name }}
                            </td>
                            <td class="text-center">
                                {{ $value->cell_ID }}
                            </td>
                            <td class="text-center">
                                {{ $value->cell_LH }}
                            </td>
                            <td class="text-center align-middle">
                                <div class="row mx-0 px-0">
                                    <a href="{{ route('version.calibrator-group.calibrator.index', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $value->id]) }}" class="btn btn-primary col">
                                        <i class="fas fa-search mr-1"></i>
                                        Kalibrator
                                    </a>
                                    <a href="{{ route('version.calibrator-group.edit', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $value->id]) }}" class="btn btn-warning col">
                                        <i class="fas fa-edit"></i>
                                        Edit Kalibrator
                                    </a>
                                </div>
                                <div class="row mx-0 px-0">
                                    <a href="{{ route('version.calibrator-group.delete', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $value->id]) }}" class="btn btn-danger btn-delete col">
                                        <i class="fas fa-trash-alt"></i>
                                        Hapus
                                    </a>
                                    <a href="{{ route('version.calibrator-group.export', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $value->id]) }}" class="btn btn-danger btn-info col">
                                        <i class="fas fa-file-export"></i>
                                        Export
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

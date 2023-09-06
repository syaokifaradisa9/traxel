@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => true, 'href' => route('version.index', ['alkes_id' => $alkesId])],
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
            <div class="col">
                <h4>Tabel Versi Excel</h4>
            </div>
            <div class="col text-right">
                <a href="{{ route('version.create', ['alkes_id' => $alkesId]) }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Versi Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-sm table-striped w-100" id="order-table">
                  <thead>
                    <tr>
                        <th class="text-center" style="width: 50px">No.</th>
                        <th class="text-center" style="width: 150px">
                            Versi Excel <br>
                            <small>Klik untuk mendownload</small>
                        </th>
                        <th class="text-center">
                            Input Cell <br>
                            <small>Klik untuk mengubah nama cell</small>
                        </th>
                        <th class="text-center">
                            Output Cell <br>
                            <small>Klik untuk mengubah nama cell</small>
                        </th>
                        <th class="text-center" style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($versions as $index => $value)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle text-center">
                                <a href="{{ asset('excel/' . $excel_name . "-" . $value->version_name . ".xlsx") }}">
                                    {{ $value->version_name }}
                                </a>
                            </td>
                            <td class="text-justify">
                                @php
                                    $input_cells_str = '';
                                    foreach ($value->input_cell as $index => $input_cell) {
                                        if($index == 0){
                                            $input_cells_str = $input_cell->cell;
                                        }else{
                                            $input_cells_str = $input_cells_str . ", " . $input_cell->cell;
                                        }
                                    }
                                @endphp
                                <a href="{{ route('version.set-cell-name', ['alkes_id' => $alkesId, 'version_id' => $value->id, "type" => "input"]) }}">
                                    {{ $input_cells_str }}
                                </a>
                            </td>
                            <td class="text-justify">
                                @php
                                    $output_cell_str = '';
                                    foreach ($value->output_cell as $index => $output_cell) {
                                        if($index == 0){
                                            $output_cell_str = $output_cell->cell;
                                        }else{
                                            $output_cell_str = $output_cell_str . ", " . $output_cell->cell;
                                        }
                                    }
                                @endphp
                                <a href="{{ route('version.set-cell-name', ['alkes_id' => $alkesId, 'version_id' => $value->id, "type" => "output"]) }}">
                                    {{ $output_cell_str }}
                                </a>
                            </td>
                            <td class="text-center align-middle" style="width: 300px">
                                <a href="{{ route('version.edit', ['alkes_id' => $alkesId, 'version_id' => $value->id]) }}" class="btn btn-warning">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                <a href="{{ route("version.schema.index", ['alkes_id' => $alkesId, 'version_id' => $value->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Tracking
                                </a>
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
        </div>
    </div>
@endsection
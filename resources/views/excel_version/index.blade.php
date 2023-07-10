@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => true, 'href' => route('version.index', ['alkes_id' => $alkesId])],
    ]
])

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Tabel Excel</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-sm table-striped w-100" id="order-table">
                  <thead>
                    <tr>
                      <th class="text-center" style="width: 50px">No.</th>
                      <th class="text-center" style="width: 150px">Versi</th>
                      <th class="text-center">Input Cell</th>
                      <th class="text-center">Output Cell</th>
                      <th class="text-center" style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($versions as $index => $value)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle text-center">
                                {{ $value->version_name }}
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
                                {{ $input_cells_str }}
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
                                {{ $output_cell_str }}
                            </td>
                            <td class="text-center align-middle" style="width: 150px">
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
@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Simulasi", "is_active" => false, 'href' => route('version.schema.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])],
        ["menu" => "Detail", "is_active" => true, 'href' => ''],
    ]
])

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success mb-2">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger mb-2">{{ Session::get('error') }}</div>
    @endif
    <div class="card">
        <div class="card-header">
            <h4>Tabel Output</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-sm table-striped w-100" id="order-table">
                  <thead>
                    <tr>
                      <th class="text-center" style="width: 50px">No.</th>
                      <th class="text-center" style="width: 50px">Cell Output</th>
                      <th class="text-center" style="width: 25%">Nilai<br>Ekspektasi</th>
                      <th class="text-center" style="width: 25%">Nilai<br>Aktual</th>
                      <th class="text-center" style="width: 50px">Status</th>
                      <th class="text-center" style="width: 25%">Deskripsi</th>
                    </tr>
                  </thead>
                  @php
                      $verify_count  = 0;
                  @endphp
                  <tbody>
                    @foreach ($output_cell_value as $index => $output_value)
                        <tr>
                            <td class="text-center align-middle" style="width: 50px">{{ $index + 1 }}</td>
                            <td class="text-center align-middle">
                                {{ $output_value->cell }}
                            </td>
                            <td class="text-center align-middle">
                                {{ $output_value->expected_value }}
                            </td>
                            <td class="text-center align-middle">
                                {{ $output_value->actual_value }}
                            </td>
                            <td class="text-center align-middle">
                                @if($output_value->is_verified)
                                    @php
                                        $verify_count++;
                                    @endphp
                                    <span class="badge badge-success px-3">
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="badge badge-danger px-3">
                                        Error
                                    </span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                {{ $output_value->error_description ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                        <td colspan="6" class="text-center bg-success pt-3">
                            <h5 class="text-white">
                                {{ $verify_count }}/{{ count($output_cell_value) }} TERVERIFIKASI ({{ number_format(($verify_count/count($output_cell_value)), 4) * 100 }} %)
                            </h5>
                        </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Tabel Input</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-sm table-striped w-100" id="order-table">
                  <thead>
                    <tr>
                      <th class="text-center" style="width: 50px">No.</th>
                      <th class="text-center">Cell Input</th>
                      <th class="text-center">Nilai</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($input_cell_value as $index => $cell_input)
                        <tr>
                            <td class="text-center align-middle" style="width: 50px">{{ $index + 1 }}</td>
                            <td class="text-center align-middle">
                                {{ $cell_input->cell }}
                            </td>
                            <td class="text-center align-middle">
                                {{ $cell_input->value }}
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
        </div>
    </div>
@endsection
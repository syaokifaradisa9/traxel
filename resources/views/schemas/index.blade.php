@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Simulasi", "is_active" => true, 'href' => route('version.schema.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])],
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
                <h4>Tabel Skema Percobaan</h4>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('version.schema.all-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId]) }}" class="btn btn-success">
                    <i class="fas fa-play-circle mr-1"></i>
                    Simulasikan Semua
                </a>
                <a href="{{ route('version.schema.create-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId]) }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Skema percobaan
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table-sm table-striped w-100" id="order-table">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px">No.</th>
                    <th class="text-center">Nama Skema<br>Percobaan</th>
                    <th class="text-center">Simulasi<br>terakhir</th>
                    <th class="text-center">Persentase<br>Terverifikasi</th>
                    <th class="text-center" style="width: 300px">Aksi</th>
                  </tr>
                </thead>
                @php
                    $percentages = [];
                @endphp
                <tbody>
                  @foreach ($schemas as $index => $value)
                      <tr>
                          <td class="text-center align-middle" style="width: 50px">{{ $index + 1 }}</td>
                          <td class="text-center align-middle">
                              {{ $value->name }}
                          </td>
                          <td class="text-center">
                              @if($value->simulation_date)
                                    {{ date("d-m-Y", strtotime($value->simulation_date)) . "   " . $value->simulation_time }} <br>
                                    <small>
                                        <b>
                                            {{ "(" . $value->simulation_days_ago . " Hari yang lalu)" }}
                                        </b>
                                    </small>
                              @else
                                  <small class="text-secondary">
                                      Belum Pernah Melakukan Simulasi
                                  </small>
                              @endif
                          </td>
                          <td class="text-center font-weight-bold 
                            @if($value->percentage == 100)
                                text-success
                            @elseif($value->percentage > 50)
                                text-warning
                            @else
                                text-danger
                            @endif">
                              @if($value->simulation_date)
                                  @php
                                      $percentages[] = $value->percentage;
                                  @endphp
                                  {{ $value->percentage . " %" }}
                              @else
                                  -
                                  @php
                                      $percentages[] = 0;
                                  @endphp
                              @endif
                          </td>
                          <td class="text-center align-middle">
                              <div class="row">
                                  <a href="{{ route('version.schema.simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, "schema_id" => $value->id]) }}" class="btn btn-success col mr-1">
                                      <i class="fas fa-play-circle mr-1"></i>
                                      Simulasikan
                                  </a>
                                  <a href="{{ route('version.schema.detail-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, "schema_id" => $value->id]) }}" class="btn btn-info col ml-1">
                                      <i class="fas fa-search mr-1"></i>
                                      Detail Tracking
                                  </a>
                              </div>
                              <div class="row mt-1">
                                  <a href="{{ route('version.schema.edit-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'schema_id' => $value->id]) }}" class="btn btn-primary col mr-1">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                  </a>
                                  <a href="{{ route('version.schema.duplicate-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'schema_id' => $value->id]) }}" class="btn btn-secondary col ml-1">
                                        <i class="fas fa-clone"></i>
                                        Duplikat
                                  </a>
                              </div>
                          </td>
                      </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                      <td colspan="3" class="text-center font-weight-bold">
                          Rata-Rata Terverifikasi
                      </td>
                      <td class="text-center font-weight-bold 
                        @if((array_sum($percentages)/count($percentages)) == 100)
                            text-success
                        @elseif((array_sum($percentages)/count($percentages)) > 50)
                            text-warning
                        @else
                            text-danger
                        @endif">
                          {{ array_sum($percentages)/count($percentages) }} %
                      </td>
                      <td>

                      </td>
                  </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Group Simulasi", "is_active" => false, 'href' => route('version.schema_group.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])],
        ["menu" => "Simulasi", "is_active" => true, 'href' => ''],
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
        </div>
        <div class="card-body">
            <div class="text-center">
                @foreach([10, 20, 30, 35] as $value)
                    <a href="{{ route('version.schema_group.schema.generates', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId, 'num' => $value]) }}" class="btn btn-warning">
                        <i class="fas fa-robot mr-1"></i>
                        Generate Aktual per {{ $value }} Skema
                    </a>
                @endforeach
            </div>
            <div class="text-center mt-2">
                @foreach([10, 20, 30, 35] as $value)
                    <a href="{{ route('version.schema_group.schema.all-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'group_id' => $groupId, 'num' => $value]) }}" class="btn btn-success">
                        <i class="fas fa-play-circle mr-1"></i>
                        Simulasi per {{ $value }} Skema
                    </a>
                @endforeach
            </div>
            <table class="table-sm table-striped w-100 mt-5" id="order-table">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px">No.</th>
                    <th class="text-center">Nama Skema<br>Percobaan</th>
                    <th class="text-center">Simulasi<br>terakhir</th>
                    <th class="text-center">Persentase<br>Terverifikasi</th>
                    <th class="text-center" style="width: 170px">Aksi</th>
                  </tr>
                </thead>
                @php
                    $percentages = [];
                @endphp
                <tbody>
                  @foreach ($schemas as $index => $value)
                      <tr>
                          <td class="text-center align-middle" style="width: 50px">{{ $index + 1 }}</td>
                          <td class="align-middle">
                            @php
                                $names = explode("|", $value->name);
                                unset($names[0]);
                            @endphp
                            @foreach ($names as $name)
                                {{ $name }} <br>
                            @endforeach
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
                                    <a href="{{ route('version.schema_group.schema.simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, "group_id" => $groupId, 'schema_id' => $value->id]) }}" class="btn btn-success col-12">
                                        <i class="fas fa-play-circle mr-1"></i>
                                        Simulasikan
                                    </a>
                                    @if($value->can_generate)
                                        <a href="{{ route('version.schema_group.schema.generate-actual-value', ['alkes_id' => $alkesId, 'version_id' => $versionId, "group_id" => $groupId, 'schema_id' => $value->id]) }}" class="btn btn-warning col-12">
                                            <i class="fas fa-robot mr-1"></i>
                                            Generate Nilai
                                        </a>
                                    @endif
                                    <a href="{{ route('version.schema_group.schema.detail-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, "group_id" => $groupId, 'schema_id' => $value->id]) }}" class="btn btn-info col-12">
                                        <i class="fas fa-search mr-1"></i>
                                        Tracking
                                    </a>
                                    <a href="{{ route('version.schema_group.schema.edit-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, "group_id" => $groupId, 'schema_id' => $value->id]) }}" class="btn btn-primary col-12 mr-1">
                                        <i class="fas fa-edit"></i>
                                        Edit
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
                        @php
                            $sum = array_sum($percentages);
                            $count = count($percentages);

                            $percetage = $count == 0 ? 0 : ($sum/$count);
                        @endphp
                        @if($percetage == 100)
                            text-success
                        @elseif($percetage > 50)
                            text-warning
                        @else
                            text-danger
                        @endif">
                          {{ $percetage }} %
                      </td>
                      <td>

                      </td>
                  </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
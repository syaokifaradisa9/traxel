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
            @if($isAutoGenerate || $isAutoSimulation)
                <div class="col-6 text-right">
                    <b class="text-danger">
                        Auto {{ $isAutoGenerate ? "Generate" : "Simulasi" }} Dalam
                        <span id="counter">
                            1
                        </span>
                    </b>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="text-center">
                <span class="text-warning">Unverified : {{ $schemaCounter['unverified'] }}</span> |
                <span class="text-success">Verified : {{ $schemaCounter['verified'] }}</span> |
                <span class="text-primary">Total : {{ $schemaCounter['total'] }}</span>
            </div>
            <p class="text-danger">
                <b>Catatan :</b> <br>
                Setelah melakukan generate nilai mohon untuk memeriksa apakah data ekspektasi nilai sudah sesuai yang diinginkan terutama pada cell yang memiliki nilai numerik agar tidak terjadi perbedaan nilai aktual dan ekpektasi sebenarnya!
            </p>
            <div class="text-right">
                @if($isAutoGenerate || $isAutoSimulation)
                    <a id="stop-autopilot" href="{{ route('version.schema_group.schema.index', [
                        "alkes_id" => $alkesId,
                        "group_id" => $groupId,
                        "version_id" => $versionId,
                        'is_show_done' => $isShowDone
                    ]) }}" class="btn btn-danger">
                        <i class="fas fa-stop mr-1"></i>
                        Stop Autopilot {{ $isAutoSimulation ? "Simulasi " : "Generate" }}
                    </a>
                @else
                    <a href="{{ route('version.schema_group.schema.index', [
                        "alkes_id" => $alkesId,
                        "group_id" => $groupId,
                        "version_id" => $versionId,
                        'is_show_done' => $isShowDone,
                        'is_auto_generate' => true,
                    ]) }}" class="btn btn-primary">
                        <i class="fas fa-robot mr-1"></i>
                        Autopilot Generate
                    </a>
                    <a href="{{ route('version.schema_group.schema.index', [
                        "alkes_id" => $alkesId,
                        "group_id" => $groupId,
                        "version_id" => $versionId,
                        'is_show_done' => $isShowDone,
                        'is_auto_simulation' => true
                    ]) }}" class="btn btn-primary">
                        <i class="fas fa-robot mr-1"></i>
                        Autopilot Simulasi
                    </a>
                @endif
                <a href="{{ route('version.schema_group.schema.index', [
                    "alkes_id" => $alkesId,
                    "group_id" => $groupId,
                    "version_id" => $versionId,
                    'is_show_done' => !$isShowDone
                ]) }}" class="btn btn-info">
                    <i class="fas fa-clipboard-list mr-1"></i>
                    List Simulasi {{ $isShowDone ? "Belum " : "" }} 100 %
                </a>
            </div>
            <table class="table-sm table-striped w-100 mt-5" id="order-table">
                <thead>
                  <tr>
                    <th class="text-center" style="width: 50px">No.</th>
                    <th class="text-center" style="width: 50px">ID</th>
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
                          <td class="text-center align-middle" style="width: 50px">{{ $value->id }}</td>
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
                          <td id="percentage-{{ $index + 1 }}" class="text-center font-weight-bold
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
                                    <a id="simulation-{{ $index + 1 }}" href="{{ route('version.schema_group.schema.simulation', [
                                            'alkes_id' => $alkesId,
                                            'version_id' => $versionId,
                                            "group_id" => $groupId,
                                            'schema_id' => $value->id,
                                            "is_auto" => $isAutoSimulation,
                                            'is_show_done' => $isShowDone
                                        ]) }}" class="btn btn-success col-12">
                                        <i class="fas fa-play-circle mr-1"></i>
                                        Simulasikan
                                    </a>
                                    @if($value->can_generate)
                                        <a id="generate-{{ $index + 1 }}" href="{{ route('version.schema_group.schema.generate-actual-value', [
                                                'alkes_id' => $alkesId,
                                                'version_id' => $versionId,
                                                "group_id" => $groupId,
                                                'schema_id' => $value->id,
                                                "is_auto" => $isAutoGenerate,
                                                'is_show_done' => $isShowDone
                                            ]) }}" class="btn btn-warning col-12">
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
                      <td colspan="4" class="text-center font-weight-bold">
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

@section("js-extends")
    @if($isAutoGenerate)
        <script>
            document.addEventListener("DOMContentLoaded", function(){
                var percentage = document.getElementById("percentage-1").innerText;

                if(percentage != '-' && percentage != '100 %'){
                    alert("Mohon Perbaiki Nomor 1 Terlebih Dahulu Terlebih Dahulu!");
                }else{
                    var counterInterval = setInterval(() => {
                        var count = parseInt(document.getElementById("counter").innerText);
                        if(count == 0){
                            document.getElementById("counter").innerText = `0 (Harap Tunggu ...)`;
                            document.getElementById("stop-autopilot").classList.add("d-none");
                            document.getElementById("generate-1").click();
                            clearInterval(counterInterval);
                        }else{
                            document.getElementById("counter").innerText = count - 1;
                        }
                    }, 1000);
                }
            });
        </script>
    @endif

    @if($isAutoSimulation)
        <script>
            var counterInterval = setInterval(() => {
                var count = parseInt(document.getElementById("counter").innerText);
                console.log(count);
                if(count == 0){
                    document.getElementById("counter").innerText = `0 (Harap Tunggu ...)`;
                    document.getElementById("simulation-1").click();
                    clearInterval(counterInterval);
                }else{
                    document.getElementById("counter").innerText = count - 1;
                }
            }, 1000);
        </script>
    @endif
@endsection
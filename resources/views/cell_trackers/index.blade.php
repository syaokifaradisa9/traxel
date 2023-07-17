@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => false, 'href' => route('home')],
        ["menu" => "Versi Excel", "is_active" => false, 'href' => route('version.index', ['alkes_id' => $alkesId])],
        ["menu" => "Simulasi", "is_active" => false, 'href' => route('version.schema.index', ['alkes_id' => $alkesId, 'version_id' => $versionId])],
        ["menu" => "Detail", "is_active" => false, 'href' => route('version.schema.detail-simulation', ['alkes_id' => $alkesId, 'version_id' => $versionId, 'schema_id' => $schemaId])],
        ["menu" => "Tracker", "is_active" => true, 'href' => ""],
    ]
])

@section('content')
    @if (Session::has('success'))
        <div class="alert alert-success mb-2">{{ Session::get('success') }}</div>
    @elseif(Session::has('error'))
        <div class="alert alert-danger mb-2">{{ Session::get('error') }}</div>
    @endif
    
    @foreach ($excel_values as $sheet_name => $values)
        <div class="card">
            <div class="card-body">
                <div id="accordion">
                    <div class="accordion">
                        <div class="accordion-header" role="button" data-toggle="collapse" data-target="#panel-body-{{ $loop->index }}">
                          <h4>
                            {{ $sheet_name }}
                          </h4>
                        </div>
                        <div class="accordion-body collapse" id="panel-body-{{ $loop->index  }}" data-parent="#accordion">
                            @php
                                $halfSize = ceil(count($values) / 2);
                                $arrayPart1 = array_slice($values, 0, $halfSize);
                                $arrayPart2 = array_slice($values, $halfSize);
                            @endphp
            
                            <table class="table-sm table-striped w-100">
                                <tr>
                                    <th class="text-center" style="width: 5%">Cell</th>
                                    <th class="text-center" style="width: 45%">Nilai</th>
                                    <th class="text-center" style="width: 5%">Cell</th>
                                    <th class="text-center" style="width: 45%">NIlai</th>
                                </tr>
                                @php
                                    $rowCount = max(count($arrayPart1), count($arrayPart2));
                                @endphp
                                @for ($i = 0; $i < $rowCount; $i++)
                                    @php
                                        $cell1 = $i < count($arrayPart1) ? $arrayPart1[$i]['cell'] : '';
                                        $cell2 = $i < count($arrayPart2) ? $arrayPart2[$i]['cell'] : '';
                                        $value1 = $i < count($arrayPart1) ? $arrayPart1[$i]['value'] : '';
                                        $value2 = $i < count($arrayPart2) ? $arrayPart2[$i]['value'] : '';

                                        $text_class1 = str_contains($value1, "#VALUE!") ||  str_contains($value1, "#REF!") || str_contains($value1, "#DIV/0!") || preg_match('/=[A-Z]+\d+/', $value1, $_) ? 'text-danger' : '';
                                        $text_class2 = str_contains($value2, "#VALUE!") ||  str_contains($value2, "#REF!") || str_contains($value2, "#DIV/0!") || preg_match('/=[A-Z]+\d+/', $value2, $_) ? 'text-danger' : '';

                                        if($sheet_name == "LH"){
                                            $foundItemIndex1 = $error_result_cells->search(function ($item) use ($cell1) {
                                                return $item['cell'] == $cell1;
                                            });

                                            if(is_numeric($foundItemIndex1)){
                                                $value1 = $value1 . " (" . $error_result_cells[$foundItemIndex1]['expected_value'] . ")";
                                                $text_class1 = "text-danger";
                                            }

                                            $foundItemIndex2 = $error_result_cells->search(function ($item) use ($cell2) {
                                                return $item['cell'] == $cell2;
                                            });

                                            if(is_numeric($foundItemIndex2)){
                                                $value2 = $value2 . " (" . $error_result_cells[$foundItemIndex2]['expected_value'] . ")";
                                                $text_class2 = "text-danger";
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle {{ $text_class1 }}">
                                            {{ $cell1 }}
                                        </td>
                                        <td class="text-center align-middle {{ $text_class1 }}">
                                            {{ $value1 }}
                                        </td>
                                        <td class="text-center align-middle {{ $text_class2 }}">
                                            {{ $cell2 }}
                                        </td>
                                        <td class="text-center align-middle {{ $text_class2 }}">
                                            {{ $value2 }}
                                        </td>
                                    </tr>
                                @endfor
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
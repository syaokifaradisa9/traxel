@extends('app', [
    'section_title' => "Halaman Manajemen Daftar Excel",
    'section_lead' => "Manajemen Data Daftar Excel",
    'section_headers' => [
        ["menu" => "Home", "is_active" => true, 'href' => route('home')],
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
                      <th class="text-center">Alat Kesehatan</th>
                      <th class="text-center" style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($alkes as $index => $value)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                {{ $value->name }}
                            </td>
                            <td>
                                <a href="{{ route('version.index', ['alkes_id' => $value->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>
                                    Detail
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
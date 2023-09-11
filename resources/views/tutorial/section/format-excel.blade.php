<div class="card">
    <div class="card-body">
        <div id="accordion">
            <div class="accordion">
                <div class="accordion-header" role="button" data-toggle="collapse" data-target="#panel-body-1">
                  <h4>Format Excel Baru Sebelum di Tracking</h4>
                </div>
                <div class="accordion-body collapse" id="panel-body-1" data-parent="#accordion">
                    <ul>
                        <li class="mt-4">
                            Silahkan ubah nama <span class="text-danger font-weight-bold">sheet input data menjadi ID</span>.
                            <img src="{{ asset('tutorial-img/format-excel/ID.png') }}" width="100%" class="ml-1 border">
                        </li>
                        <li class="mt-4">
                            Silahkan ubah nama <span class="text-danger font-weight-bold">sheet Lembar Hasil menjadi LH</span>.
                            <img src="{{ asset('tutorial-img/format-excel/LH.png') }}" width="100%" class="ml-1 border">
                        </li>
                        <li class="mt-4">
                            <span class="text-danger font-weight-bold">Tambahkan sheet SERTIFIKAT</span>, 
                            referensi bisa dilihat <a href="{{ asset('excel/ECG_Recorder-07-07-2023.xlsx') }}">disini</a>. 
                            Jangan lupa untuk <span class="text-danger font-weight-bold">menambahkan cell untuk LAIK dan TIDAK LAIK pada cell H1</span>.
                            <img src="{{ asset('tutorial-img/format-excel/SERTIFIKAT.png') }}" width="100%" class="ml-1 border">
                        </li>
                        <li class="mt-4">
                            Pindah <span class="text-danger font-weight-bold">posisi cell nomor sertifikat pada ID ke posisi cell I2</span>
                            <img src="{{ asset('tutorial-img/format-excel/Nomor Sertifikat.png') }}" width="100%" class="ml-1 border">
                        </li>
                        <li class="mt-4">
                            Untuk satu cell yang memiliki banyak nilai, <span class="text-danger font-weight-bold">buat nilai menjadi terpisah sehingga 1 cell maksimal mendapatkan 1 nilai saja</span>.
                            <img src="{{ asset('tutorial-img/format-excel/Pemisahan.png') }}" width="100%" class="ml-1 border">
                        </li>
                        <li class="mt-4">
                            Untuk mendeteksi cell input dan output secara otomatis, silahkan <span class="text-danger font-weight-bold">hitamkan semua teks dengan cara ctrl + a lalu pilih warna hitam untuk membuat teks menjadi hitam</span> pada ID dan LH lalu 
                            <span class="text-danger font-weight-bold">beri warna merah pada cell yang ingin dijadikan cell input dan output</span>.
                            <img src="{{ asset('tutorial-img/format-excel/input-cell-1.png') }}" width="100%" class="ml-1 border">
                            <img src="{{ asset('tutorial-img/format-excel/input-cell-2.png') }}" width="100%" class="ml-1 mt-2 border">
                            <img src="{{ asset('tutorial-img/format-excel/output-cell-1.png') }}" width="100%" class="ml-1 mt-2 border">
                            <img src="{{ asset('tutorial-img/format-excel/output-cell-2.png') }}" width="100%" class="ml-1 mt-2 border">
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
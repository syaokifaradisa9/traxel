<?php

namespace Database\Seeders;

use App\Models\ExcelVersion;
use App\Models\InputCell;
use App\Models\InputCellValue;
use App\Models\OutputCell;
use App\Models\OutputCellValue;
use App\Models\TestSchema;
use Illuminate\Database\Seeder;

class ExcelVersionSeeder extends Seeder
{
    public function input_output_cell_seeder($excel_version_id){
        $input_cells = [
            ["cell" => "E14", "cell_name" => "Suhu Awal"],
            ["cell" => "F14", "cell_name" => "Suhu Akhir"],
            ["cell" => "E15", "cell_name" => "Kelembapan Awal"],
            ["cell" => "F15", "cell_name" => "Kelembapan Akhir"],
            ["cell" => "E16", "cell_name" => "Tegangan Jala-jala"],
            ["cell" => "E19", "cell_name" => "Fisik Alat"],
            ["cell" => "E20", "cell_name" => "Fungsi Alat"],
            ["cell" => "I25", "cell_name" => "Hasil Ukur Resistansi Isolasi"],
            ["cell" => "C26", "cell_name" => "Parameter Resistansi Pembumian Protektif"],
            ["cell" => "I26", "cell_name" => "Hasil Ukur Resistansi Pembumian Protektif"],
            ["cell" => "C27", "cell_name" => "Paramater Arus Bocor Peralatan Untuk Peralatan Elektromedik"],
            ["cell" => "I27", "cell_name" => "Hasil Ukur Arus Bocor Peralatan Untuk Peralatan Elektromedik"],
            ["cell" => "I28", "cell_name" => "Hasil Ukur Arus bocor peralatan yang diaplikasikan"],
            ["cell" => "T27", "cell_name" => "NC"],
            ["cell" => "F34", "cell_name" => "Hasil Pengamatan Visual 12 Lead"],
            ["cell" => "G40", "cell_name" => "Pembacaan Standar Amplitudo 5 I"],
            ["cell" => "H40", "cell_name" => "Pembacaan Standar Amplitudo 5 II"],
            ["cell" => "I40", "cell_name" => "Pembacaan Standar Amplitudo 5 III"],
            ["cell" => "J40", "cell_name" => "Pembacaan Standar Amplitudo 5 IV"],
            ["cell" => "K40", "cell_name" => "Pembacaan Standar Amplitudo 5 V"],
            ["cell" => "G41", "cell_name" => "Pembacaan Standar Amplitudo 10 I"],
            ["cell" => "H41", "cell_name" => "Pembacaan Standar Amplitudo 10 II"],
            ["cell" => "I41", "cell_name" => "Pembacaan Standar Amplitudo 10 III"],
            ["cell" => "J41", "cell_name" => "Pembacaan Standar Amplitudo 10 IV"],
            ["cell" => "K41", "cell_name" => "Pembacaan Standar Amplitudo 10 V"],
            ["cell" => "G42", "cell_name" => "Pembacaan Standar Amplitudo 20 I"],
            ["cell" => "H42", "cell_name" => "Pembacaan Standar Amplitudo 20 II"],
            ["cell" => "I42", "cell_name" => "Pembacaan Standar Amplitudo 20 III"],
            ["cell" => "J42", "cell_name" => "Pembacaan Standar Amplitudo 20 IV"],
            ["cell" => "K42", "cell_name" => "Pembacaan Standar Amplitudo 20 V"],
            ["cell" => "F47", "cell_name" => "Pembacaan Standar Laju Rekaman 25 I"],
            ["cell" => "G47", "cell_name" => "Pembacaan Standar Laju Rekaman 25 II"],
            ["cell" => "H47", "cell_name" => "Pembacaan Standar Laju Rekaman 25 III"],
            ["cell" => "F51", "cell_name" => "Pembacaan Standar Laju Rekaman 50 I"],
            ["cell" => "G51", "cell_name" => "Pembacaan Standar Laju Rekaman 50 II"],
            ["cell" => "H51", "cell_name" => "Pembacaan Standar Laju Rekaman 50 III"],
            ["cell" => "H56", "cell_name" => "Pembacaan Standar Sinusoida I"],
            ["cell" => "I56", "cell_name" => "Pembacaan Standar Sinusoida II"],
            ["cell" => "J56", "cell_name" => "Pembacaan Standar Sinusoida III"],
            ["cell" => "K56", "cell_name" => "Pembacaan Standar Sinusoida IV"],
            ["cell" => "L56", "cell_name" => "Pembacaan Standar Sinusoida V"],
            ["cell" => "H61", "cell_name" => "Pembacaan Standar ECG I"],
            ["cell" => "I61", "cell_name" => "Pembacaan Standar ECG II"],
            ["cell" => "J61", "cell_name" => "Pembacaan Standar ECG III"],
            ["cell" => "K61", "cell_name" => "Pembacaan Standar ECG IV"],
            ["cell" => "L61", "cell_name" => "Pembacaan Standar ECG V"],
            ["cell" => "B72", "cell_name" => "Alat Ukur 1"],
            ["cell" => "B73", "cell_name" => "Alat Ukur 2"],
            ["cell" => "B74", "cell_name" => "Alat Ukur 3"],
            ["cell" => "B75", "cell_name" => "Alat Ukur 4"],
        ];

        foreach($input_cells as $input_cell){
            InputCell::create([
                'cell' => $input_cell['cell'],
                "cell_name" => $input_cell['cell_name'],
                'excel_version_id' => $excel_version_id
            ]);
        }

        $output_cells = [
            ["cell" => "E14", "cell_name" => "Suhu"],
            ["cell" => "G14", "cell_name" => "Suhu"],
            ["cell" => "E15", "cell_name" => "Kelembaban"],
            ["cell" => "G15", "cell_name" => "Kelembaban"],
            ["cell" => "E16", "cell_name" => "Tegangan Jala-jala"],
            ["cell" => "D19", "cell_name" => "Fisik Alat"],
            ["cell" => "D20", "cell_name" => "Fungsi Alat"],
            ["cell" => "C26", "cell_name" => "Parameter Resistansi Pembumian Protektif"],
            ["cell" => "C27", "cell_name" => "Parameter Arus Bocor Peralatan Untuk Peralatan Elektromedik"],
            ["cell" => "G25", "cell_name" => "Hasil Ukur Resistansi Isolasi"],
            ["cell" => "G26", "cell_name" => "Hasil Ukur Resistansi Pembumian Protektif"],
            ["cell" => "G27", "cell_name" => "Hasil Ukur Arus Bocor Peralatan Untuk Peralatan Elektromedik"],
            ["cell" => "G28", "cell_name" => "Hasil Ukur Arus Bocor Peralatan yang diaplikasikan"],
            ["cell" => "F34", "cell_name" => "Hasil Pengamatan Visual 12 Lead"],
            ["cell" => "G40", "cell_name" => "Pembacaan Standar tinggi Amplitudo 5"],
            ["cell" => "G41", "cell_name" => "Pembacaan Standar tinggi Amplitudo 10"],
            ["cell" => "G42", "cell_name" => "Pembacaan Standar tinggi Amplitudo 20"],
            ["cell" => "H40", "cell_name" => "Koreksi tinggi Amplitudo 5"],
            ["cell" => "H41", "cell_name" => "Koreksi tinggi Amplitudo 10"],
            ["cell" => "H42", "cell_name" => "Koreksi tinggi Amplitudo 20"],
            ["cell" => "I40", "cell_name" => "Koreksi Relatif tinggi Amplitudo 5"],
            ["cell" => "I41", "cell_name" => "Koreksi Relatif tinggi Amplitudo 10"],
            ["cell" => "I42", "cell_name" => "Koreksi Relatif tinggi Amplitudo 20"],
            ["cell" => "L40", "cell_name" => "Ketidakpastian Pengukuran tinggi Amplitudo 5"],
            ["cell" => "L41", "cell_name" => "Ketidakpastian Pengukuran tinggi Amplitudo 10"],
            ["cell" => "L42", "cell_name" => "Ketidakpastian Pengukuran tinggi Amplitudo 20"],
            ["cell" => "F47", "cell_name" => "Lebar Pulsa Laju Rekaman 25"],
            ["cell" => "F48", "cell_name" => "Lebar Pulsa Laju Rekaman 50"],
            ["cell" => "G47", "cell_name" => "Pembacaan Standar Laju Rekaman 25"],
            ["cell" => "G48", "cell_name" => "Pembacaan Standar Laju Rekaman 50"],
            ["cell" => "H47", "cell_name" => "Koreksi Laju Rekaman 25"],
            ["cell" => "H48", "cell_name" => "Koreksi Laju Rekaman 50"],
            ["cell" => "I47", "cell_name" => "Koreksi Relatif Laju Rekaman 25"],
            ["cell" => "I48", "cell_name" => "Koreksi Relatif Laju Rekaman 50"],
            ["cell" => "L47", "cell_name" => "ketidakpastian Laju Rekaman 25"],
            ["cell" => "L48", "cell_name" => "ketidakpastian Laju Rekaman 50"],
            ["cell" => "G53", "cell_name" => "Pembacaan Standar Sinyal Sinusoida"],
            ["cell" => "H53", "cell_name" => "Koreksi Sinyal Sinusoida"],
            ["cell" => "I53", "cell_name" => "Koreksi Relatif Sinyal Sinusoida"],
            ["cell" => "L53", "cell_name" => "Ketidakpastian Pengukuran Sinyal Sinusoida"],
            ["cell" => "G58", "cell_name" => "Pembacaan Standar Sinyal ECG Normal"],
            ["cell" => "H58", "cell_name" => "Koreksi Sinyal ECG Normal"],
            ["cell" => "I58", "cell_name" => "Koreksi Relatif Sinyal ECG Normal"],
            ["cell" => "L58", "cell_name" => "Ketidakpastian Pengukuran Sinyal ECG Normal"],
            ["cell" => "B71", "cell_name" => "Keterangan 1"],
            ["cell" => "B72", "cell_name" => "Keterangan 2"],
            ["cell" => "B73", "cell_name" => "Keterangan 3"],
            ["cell" => "B74", "cell_name" => "Keterangan 4"],
            ["cell" => "B75", "cell_name" => "Keterangan 5"],
            ["cell" => "B79", "cell_name" => "Alat Ukur 1"],
            ["cell" => "B80", "cell_name" => "Alat Ukur 2"],
            ["cell" => "B81", "cell_name" => "Alat Ukur 3"],
            ["cell" => "B84", "cell_name" => "kesimpulan"],
        ];

        foreach($output_cells as $output_cell){
            OutputCell::create([
                'cell' => $output_cell['cell'],
                'cell_name' => $output_cell['cell_name'],
                'excel_version_id' => $excel_version_id
            ]);
        }
    }

    public function input_output_cell_values_seeder($test_schema_id, $excel_version){
        $input_cell_values = [
            ["cell" => "E14", "value" => "25.0"],
            ["cell" => "F14", "value" => "25.3"],
            ["cell" => "E15", "value" => "65.1"],
            ["cell" => "F15", "value" => "65.2"],
            ["cell" => "E16", "value" => "-"],
            ["cell" => "E19", "value" => "Baik"],
            ["cell" => "E20", "value" => "Baik"],
            ["cell" => "I25", "value" => "20.0"],
            ["cell" => "C26", "value" => "Resistansi Pembumian Protektif (kabel dapat dilepas)"],
            ["cell" => "I26", "value" => "OL"],
            ["cell" => "C27", "value" => "Arus bocor peralatan untuk peralatan elektromedik kelas I"],
            ["cell" => "I27", "value" => "600.0"],
            ["cell" => "I28", "value" => "26.7"],
            ["cell" => "T27", "value" => "50"],
            ["cell" => "F34", "value" => "Baik"],
            ["cell" => "G40", "value" => "5.00"],
            ["cell" => "H40", "value" => "4.93"],
            ["cell" => "I40", "value" => "4.93"],
            ["cell" => "J40", "value" => "6.00"],
            ["cell" => "K40", "value" => "4.93"],
            ["cell" => "G41", "value" => "9.87"],
            ["cell" => "H41", "value" => "9.92"],
            ["cell" => "I41", "value" => "9.92"],
            ["cell" => "J41", "value" => "9.91"],
            ["cell" => "K41", "value" => "9.91"],
            ["cell" => "G42", "value" => "20.21"],
            ["cell" => "H42", "value" => "20.28"],
            ["cell" => "I42", "value" => "20.29"],
            ["cell" => "J42", "value" => "20.29"],
            ["cell" => "K42", "value" => "20.29"],
            ["cell" => "F47", "value" => "104.00"],
            ["cell" => "G47", "value" => "104.00"],
            ["cell" => "H47", "value" => "104.00"],
            ["cell" => "F51", "value" => "100.12"],
            ["cell" => "G51", "value" => "100.11"],
            ["cell" => "H51", "value" => "100.12"],
            ["cell" => "H56", "value" => "19.87"],
            ["cell" => "I56", "value" => "20.00"],
            ["cell" => "J56", "value" => "20.00"],
            ["cell" => "K56", "value" => "20.00"],
            ["cell" => "L56", "value" => "20.00"],
            ["cell" => "H61", "value" => "20.00"],
            ["cell" => "I61", "value" => "20.00"],
            ["cell" => "J61", "value" => "20.00"],
            ["cell" => "K61", "value" => "20.00"],
            ["cell" => "L61", "value" => "20.00"],
            ["cell" => "B72", "value" => "Vital Sign Simulator, Merek : RIGEL, Model : UNI-SiM, SN : 45K-1059"],
            ["cell" => "B73", "value" => "Electrical Safety Analyzer, Merek : Fluke, Model : ESA 615, SN : 4670010"],
            ["cell" => "B74", "value" => 'Digital Caliper, Merek : Mitutoyo, Model : CD-6"CSX, SN : 07414353'],
            ["cell" => "B75", "value" => "Thermohygrobarometer, Merek : EXTECH, Model : SD700, SN : A.100616"],
        ];

        foreach($input_cell_values as $input_cell_value){
            $inputCell = InputCell::where(['cell' => $input_cell_value['cell'], 'excel_version_id' => $excel_version->id])->first();
            InputCellValue::create([
                'input_cell_id' => $inputCell->id,
                'value' => $input_cell_value['value'],
                'test_schema_id' => $test_schema_id
            ]);
        }

        $output_cell_values = [
            ["cell" => "E14", "expected_value" => "25.3"],
            ["cell" => "G14", "expected_value" => "0.4"],
            ["cell" => "E15", "expected_value" => "63.2"],
            ["cell" => "G15", "expected_value" => "2.2"],
            ["cell" => "E16", "expected_value" => "-"],
            ["cell" => "D19", "expected_value" => "Baik"],
            ["cell" => "D20", "expected_value" => "Baik"],
            ["cell" => "C26", "expected_value" => "Resistansi Pembumian Protektif (kabel dapat dilepas)"],
            ["cell" => "C27", "expected_value" => "Arus bocor peralatan untuk peralatan elektromedik kelas I"],
            ["cell" => "G25", "expected_value" => "20"],
            ["cell" => "G26", "expected_value" => "OL"],
            ["cell" => "G27", "expected_value" => "593.1"],
            ["cell" => "G28", "expected_value" => "31.6"],
            ["cell" => "F34", "expected_value" => "Baik"],
            ["cell" => "G40", "expected_value" => "5.15"],
            ["cell" => "G41", "expected_value" => '9.9'],
            ["cell" => "G42", "expected_value" => "20.27"],
            ["cell" => "H40", "expected_value" => "0.15"],
            ["cell" => "H41", "expected_value" => "-0.1"],
            ["cell" => "H42", "expected_value" => "0.27"],
            ["cell" => "I40", "expected_value" => "2.94"],
            ["cell" => "I41", "expected_value" => "-1.02"],
            ["cell" => "I42", "expected_value" => "1.31"],
            ["cell" => "L40", "expected_value" => "10.68"],
            ["cell" => "L41", "expected_value" => "0.29"],
            ["cell" => "L42", "expected_value" => "0.20"],
            ["cell" => "F47", "expected_value" => "100"],
            ["cell" => "F48", "expected_value" => "100"],
            ["cell" => "G47", "expected_value" => "103.99"],
            ["cell" => "G48", "expected_value" => "100.11"],
            ["cell" => "H47", "expected_value" => "3.99"],
            ["cell" => "H48", "expected_value" => "0.11"],
            ["cell" => "I47", "expected_value" => "3.84"],
            ["cell" => "I48", "expected_value" => "0.11"],
            ["cell" => "L47", "expected_value" => "0.02"],
            ["cell" => "L48", "expected_value" => "0.02"],
            ["cell" => "G53", "expected_value" => "19.97"],
            ["cell" => "H53", "expected_value" => "-0.03"],
            ["cell" => "I53", "expected_value" => "-0.16"],
            ["cell" => "L53", "expected_value" => "0.32"],
            ["cell" => "G58", "expected_value" => "19.99"],
            ["cell" => "H58", "expected_value" => "-0.01"],
            ["cell" => "I58", "expected_value" => "-0.03"],
            ["cell" => "L58", "expected_value" => "0.11"],
            ["cell" => "B71", "expected_value" => "Ketidakpastian pengukuran dilaporkan pada tingkat kepercayaan 95 % dengan faktor cakupan k = 2"],
            ["cell" => "B72", "expected_value" => ""],
            ["cell" => "B73", "expected_value" => "Hasil kalibrasi amplitudo dan laju rekaman tertelusur ke Satuan Internasional (SI) melalui PT. KALIMAN (LK-032-IDN)"],
            ["cell" => "B74", "expected_value" => "Hasil kalibrasi sinyal sinusoida dan ECG normal tertelusur ke Satuan Internasional (SI) melalui PT. KALIMAN (LK-032-IDN)"],
            ["cell" => "B75", "expected_value" => "Catu daya menggunakan baterai"],
            ["cell" => "B79", "expected_value" => "Vital Sign Simulator, Merek : RIGEL, Model : UNI-SiM, SN : 45K-1059"],
            ["cell" => "B80", "expected_value" => ""],
            ["cell" => "B81", "expected_value" => 'Digital Caliper, Merek : Mitutoyo, Model : CD-6"CSX, SN : 07414353'],
            ["cell" => "B84", "expected_value" => "Alat yang dikalibrasi dalam batas toleransi dan dinyatakan LAIK PAKAI, dimana hasil atau skor akhir sama dengan atau melampaui 70 % berdasarkan Keputusan Direktur Jenderal Pelayanan Kesehatan No : HK.02.02/V/0412/2020"],
        ];

        foreach($output_cell_values as $output_cell_value){
            $outputCell = OutputCell::where(['cell' => $output_cell_value['cell'], 'excel_version_id' => $excel_version->id])->first();
            OutputCellValue::create([
                'output_cell_id' => $outputCell->id,
                'expected_value' => $output_cell_value['expected_value'],
                'test_schema_id' => $test_schema_id
            ]);
        }
    }

    public function run(): void
    {
        $excel_version = ExcelVersion::create([
            'alkes_id' => 19,
            'version_name' => '07-07-2023'
        ]);

        $this->input_output_cell_seeder($excel_version->id);

        $testSchema = TestSchema::create([
            'name' => "Simulasi Pertama",
            'excel_version_id' => $excel_version->id
        ]);

        $this->input_output_cell_values_seeder($testSchema->id, $excel_version);
    }
}

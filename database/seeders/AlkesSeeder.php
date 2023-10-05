<?php

namespace Database\Seeders;

use App\Models\Alkes;
use Illuminate\Database\Seeder;

class AlkesSeeder extends Seeder
{
    public function run(): void
    {
        $alkes = [
            [
                'id' => 1,
                'name' => 'Alat Hisap Medik / Suction Pump', 
                'excel_name' => 'Suction_Pump',
            ],[
                'id' => 2,
                'name' => 'Analytical Balance', 
                'excel_name' => 'Analytical_Balance',
            ],[
                'id' => 3,
                'name' => 'Anasthesi Ventilator', 
                'excel_name' => 'Anesthesi_Ventilator',
            ],[
                'id' => 4,
                'name' => 'Audiometer', 
                'excel_name' => 'Audiometer',
            ],[
                'id' => 5,
                'name' => 'Autoclave',
                'excel_name' => 'Autoclave',
            ],[
                'id' => 6,
                'name' => 'Baby Incubator / Inkubator Perawatan', 
                'excel_name' => 'baby_incubator',
            ],[
                'id' => 7,
                'name' => 'Bedside Monitor / Patient Monitor', 
                'excel_name' => 'Patient_Monitor',
            ],[
                'id' => 8,
                'name' => 'Blood Bank', 
                'excel_name' => 'Blood_bank',
            ],[
                'id' => 9,
                'name' => 'Tensimeter Digital / Blood Pressure Monitor (BPM)', 
                'excel_name' => 'BPM',
            ],[
                'id' => 10,
                'name' => 'Centrifuge', 
                'excel_name' => 'Centrifuge',
            ],[
                'id' => 11,
                'name' => 'Centrifuge Refrigerator', 
                'excel_name' => 'Centrifuge_Refrigerator'
            ],[
                'id' => 12,
                'name' => 'CPAP', 
                'excel_name' => 'CPAP'
            ],[
                'id' => 13,
                'name' => 'Cardiotocograph (CTG)', 
                'excel_name' => 'Cardiotocograph'
            ],[
                'id' => 14,
                'name' => 'Defibrillator', 
                'excel_name' => 'Defibrillator'
            ],[
                'id' => 15,
                'name' => 'Defibrillator Monitor (AED)', 
                'excel_name' => 'Defibrillator_Monitor'
            ],[
                'id' => 16,
                'name' => 'Defibrillator With ECG', 
                'excel_name' => 'Defibrillator_With_ECG'
            ],[
                'id' => 17,
                'name' => 'Dental Unit', 
                'excel_name' => 'Dental_unit'
            ],[
                'id' => 18,
                'name' => 'Doppler / Fetal Detector', 
                'excel_name' => 'Doppler'
            ],[
                'id' => 19,
                'name' => 'ECG Recorder', 
                'excel_name' => 'ECG_Recorder'
            ],[
                'id' => 20,
                'name' => 'EEG', 
                'excel_name' => 'EEG'
            ],[
                'id' => 21,
                'name' => 'Elektro Stimulator / EST', 
                'excel_name' => 'Elektro_Stimulator'
            ],[
                'id' => 22,
                'name' => 'ESU', 
                'excel_name' => 'ESU'
            ],[
                'id' => 23,
                'name' => 'Examination lamp (Lampu Tindakan)', 
                'excel_name' => 'Examination_Lamp'
            ],[
                'id' => 24,
                'name' => 'Flowmeter (regulator oksigen)', 
                'excel_name' => 'Flowmeter'
            ],[
                'id' => 25,
                'name' => 'Haemodialisa', 
                'excel_name' => 'Haemodialisa'
            ],[
                'id' => 26,
                'name' => 'Head Lamp', 
                'excel_name' => 'Head_Lamp'
            ],[
                'id' => 27,
                'name' => 'Heart Rate Monitor', 
                'excel_name' => 'Heart_Rate_Monitor'
            ],[
                'id' => 28,
                'name' => 'Infant Warmer', 
                'excel_name' => 'Infant_Warmer'
            ],[
                'id' => 29,
                'name' => 'Infusion Pump', 
                'excel_name' => 'Infusion_Pump',
            ],[
                'id' => 30,
                'name' => 'Laboratorium Incubator', 
                'excel_name' => 'Laboratorium_Incubator'
            ],[
                'id' => 31,
                'name' => 'Laboratorium Refrigerator', 
                'excel_name' => 'LAB_Refrigerator'
            ],[
                'id' => 32,
                'name' => 'Laboratorium Rotator', 
                'excel_name' => 'Laboratorium_Rotator'
            ],[
                'id' => 33,
                'name' => 'Lampu Operasi Ceiling Type', 
                'excel_name' => 'Lampu_Operasi_Ceiling_Type'
            ],[
                'id' => 34,
                'name' => 'Lampu Operasi Mobile Type', 
                'excel_name' => 'Lampu_Operasi_Mobile_Type'
            ],[
                'id' => 35,
                'name' => 'Mesin Anaesthesi tanpa Vaporizer tanpa Ventilator', 
                'excel_name' => 'Mesin_Anestesi'
            ],[
                'id' => 36,
                'name' => 'Mikropipet Fix', 
                'excel_name' => 'Mikropipet_Fix'
            ],[
                'id' => 37,
                'name' => 'Mikropipet Variabel', 
                'excel_name' => 'Mikropipet_Variable'
            ],[
                'id' => 38,
                'name' => 'Nebulizer', 
                'excel_name' => 'Nebulizer'
            ],[
                'id' => 39,
                'name' => 'O2 Concentrator', 
                'excel_name' => 'Oxygen_Concentrator'
            ],[
                'id' => 40,
                'name' => 'Oven', 
                'excel_name' => 'Oven'
            ],[
                'id' => 41,
                'name' => 'PhotoTherapy Unit / Blue Light', 
                'excel_name' => 'PhotoTherapy_Unit'
            ],[
                'id' => 42,
                'name' => 'Pulse Oximetri/ SPO2 Monitor', 
                'excel_name' => 'Pulse_Oxymetry'
            ],[
                'id' => 43,
                'name' => 'Short Wave Diathermi',
                'excel_name' => 'Short_Wave_Diathermi',
            ],[
                'id' => 44,
                'name' => 'Sphygmomanometer', 
                'excel_name' => 'Sphygmomanometer'
            ],[
                'id' => 45,
                'name' => 'Spirometer', 
                'excel_name' => 'Spirometer'
            ],[
                'id' => 46,
                'name' => 'Sterilisator Basah', 
                'excel_name' => 'Sterilisator_Basah'
            ],[
                'id' => 47,
                'name' => 'Sterilisator Kering', 
                'excel_name' => 'Sterilisator_Kering'
            ],[
                'id' => 48,
                'name' => 'Suction Thorax',
                'excel_name' => 'Suction_Thorax',
            ],[
                'id' => 49,
                'name' => 'Syringe Pump', 
                'excel_name' => 'Syringe_Pump'
            ],[
                'id' => 50,
                'name' => 'Thermometer Klinik', 
                'excel_name' => 'Thermometer_Klinik'
            ],[
                'id' => 51,
                'name' => 'Timbangan Bayi', 
                'excel_name' => 'Timbangan_Bayi'
            ],[
                'id' => 52,
                'name' => 'Traksi', 
                'excel_name' => 'Traksi'
            ],[
                'id' => 53,
                'name' => 'Treadmill', 
                'excel_name' => 'Treadmill'
            ],[
                'id' => 54,
                'name' => 'Tredmill With ECG', 
                'excel_name' => 'Tredmill_ECG'
            ],[
                'id' => 55,
                'name' => 'Ultrasound therapy', 
                'excel_name' => 'Ultrasound_Therapy'
            ],[
                'id' => 56,
                'name' => 'USG', 
                'excel_name' => 'USG'
            ],[
                'id' => 57,
                'name' => 'UV Lamp',
                'excel_name' => 'UV_Lamp',
            ],[
                'id' => 58,
                'name' => 'UV Sterilizer', 
                'excel_name' => 'UV_Sterilizer'
            ],[
                'id' => 59,
                'name' => 'Vacuum Extractor', 
                'excel_name' => 'Vacuum_Extractor'
            ],[
                'id' => 60,
                'name' => 'Vaporizer', 
                'excel_name' => 'Vaporizer'
            ],[
                'id' => 61,
                'name' => 'Ventilator', 
                'excel_name' => 'Ventilator'
            ],[
                'id' => 62,
                'name' => 'Suction Wall', 
                'excel_name' => 'Wall_Suction'
            ],[
                'id' => 63,
                'name' => 'Water Bath', 
                'excel_name' => 'Water_Bath'
            ],[
                'id' => 64,
                'name' => 'Mikropipet Multi Channel',
                'excel_name' => 'Mikropipet_Multi_Channel',
            ],[
                'id' => 65,
                'name' => 'Paraffin Bath',
                'excel_name' => 'Paraffin_Bath',
            ],[
                'id' => 66,
                'name' => 'Stirer', 
                'excel_name' => 'Stirer'
            ],[
                'id' => 67,
                'name' => 'Medical Freezer',
                'excel_name' => 'Medical_Freezer',
            ],[
                'id' => 68,
                'name' => 'Blood Warmer',
                'excel_name' => 'Blood_Warmer',
            ],[
                'id' => 69,
                'name' => 'Blood Solution Warmer', 
                'excel_name' => 'Blood_Solution_Warmer'
            ],[
                'id' => 70,
                'name' => 'Bedside With Defibilator', 
                'excel_name' => 'Bedside_With_Defibrillator'
            ],[
                'id' => 71,
                'name' => 'Defibrillator With ECG With SPO2', 
                'excel_name' => 'Defibrillator_With_ECG_With_SPO2'
            ],[
                'id' => 72,
                'name' => 'Thoracic Aspirator',
                'excel_name' => 'Thoracic_Aspirator',
            ],[
                'id' => 73,
                'name' => 'Tracheal Aspirator',
                'excel_name' => 'Tracheal_Aspirator',
            ],[
                'id' => 74,
                'name' => 'Suction Pump Saliva',
                'excel_name' => 'Suction_Pump_Saliva'
            ],[
                'id' => 75,
                'name' => 'Ventilator Transport',
                'excel_name' => 'Ventilator_Transport'
            ],[
                'id' => 76,
                'name' => 'High Flow Nasal Cannula', 
                'excel_name' => 'High_Flow_Nasal_Cannula'
            ],[
                'id' => 77,
                'name' => 'Cold Flow',
                "excel_name" => 'Cold_Flow'
            ],[
                'id' => 78,
                'name' => 'Timbangan Dewasa', 
                'excel_name' => 'Timbangan_Dewasa'
            ],[
                'id' => 79,
                'name' => 'Thermohygrometer', 
                'excel_name' => 'Thermohygrometer_Digital'
            ],[
                'id' => 80,
                'name' => 'Tachometer', 
                'excel_name' => "Tachometer"
            ],[
                'id' => 81,
                'name' => 'Thermometer Digital', 
                'excel_name' => 'Thermometer_Digital'
            ],[
                'id' => 82,
                'name' => 'BPAP',
                "excel_name" => "BPAP"
            ],[
                'id' => 83,
                'name' => 'Resusitasi paru',
                "excel_name" => "Resusitasi_Paru"
            ],[
                "id" => 84,
                "name" => 'Electrical Safety Analyzer',
                'excel_name' => "Electrical_Safety_Analyzer"
            ],[
                "id" => 85,
                "name" => "Otoscope",
                "excel_name" => "Otoscope"
            ],[
                "id" => 86,
                "name" => "Laryngoscope",
                "excel_name" => "Laryngoscope"
            ]
        ];

        foreach($alkes as $data){
            Alkes::insert($data);
        }
    }
}

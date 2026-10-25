<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LegacyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo ber-password publik ("Admin@123"): JANGAN dijalankan di produksi.
        if (app()->environment('production')) {
            return;
        }

        // Users (skip if already exist)
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                ['name'=>'Administrator',    'email'=>'admin@mcu.id',  'password'=>Hash::make('Admin@123'), 'role'=>'admin',  'active'=>1, 'created_at'=>now(),'updated_at'=>now()],
                ['name'=>'Dr. Budi Santoso', 'email'=>'budi@mcu.id',   'password'=>Hash::make('Admin@123'), 'role'=>'doctor', 'active'=>1, 'created_at'=>now(),'updated_at'=>now()],
                ['name'=>'Siti Rahayu',      'email'=>'siti@mcu.id',   'password'=>Hash::make('Admin@123'), 'role'=>'staff',  'active'=>1, 'created_at'=>now(),'updated_at'=>now()],
                ['name'=>'Pemilik Klinik',   'email'=>'owner@mcu.id',  'password'=>Hash::make('Admin@123'), 'role'=>'owner',  'active'=>1, 'created_at'=>now(),'updated_at'=>now()],
            ]);
        }

        // Packages
        if (DB::table('packages')->count() === 0) {
            DB::table('packages')->insert([
                ['id'=>1,'name'=>'Basic MCU',         'code'=>'PKG-BASIC', 'description'=>'Pemeriksaan dasar meliputi vital sign, darah rutin, dan urin rutin.',                                                             'price'=>350000,  'active'=>1,'created_at'=>now(),'updated_at'=>now()],
                ['id'=>2,'name'=>'Comprehensive MCU', 'code'=>'PKG-COMP',  'description'=>'Pemeriksaan lengkap termasuk lab kimia darah, rontgen thorax, dan EKG.',                                                          'price'=>850000,  'active'=>1,'created_at'=>now(),'updated_at'=>now()],
                ['id'=>3,'name'=>'Executive MCU',     'code'=>'PKG-EXEC',  'description'=>'Pemeriksaan executive lengkap termasuk USG abdomen, spirometri, dan audiometri.','price'=>1750000, 'active'=>1,'created_at'=>now(),'updated_at'=>now()],
            ]);

            DB::table('package_items')->insert([
                // Basic
                ['package_id'=>1,'exam_category'=>'Vital Sign',  'exam_name'=>'Tekanan Darah',  'unit'=>'mmHg','normal_range'=>'<140/90','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Vital Sign',  'exam_name'=>'Denyut Nadi',    'unit'=>'x/mnt','normal_range'=>'60-100','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Vital Sign',  'exam_name'=>'Suhu Tubuh',     'unit'=>'°C',  'normal_range'=>'36-37.5','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Vital Sign',  'exam_name'=>'Berat Badan',    'unit'=>'kg',  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Vital Sign',  'exam_name'=>'Tinggi Badan',   'unit'=>'cm',  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Vital Sign',  'exam_name'=>'IMT',            'unit'=>'kg/m²','normal_range'=>'18.5-24.9','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Darah Rutin', 'exam_name'=>'Hemoglobin',     'unit'=>'g/dL','normal_range'=>'12-18','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Darah Rutin', 'exam_name'=>'Leukosit',       'unit'=>'ribu/µL','normal_range'=>'4-11','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Darah Rutin', 'exam_name'=>'Trombosit',      'unit'=>'ribu/µL','normal_range'=>'150-450','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Darah Rutin', 'exam_name'=>'Hematokrit',     'unit'=>'%',   'normal_range'=>'37-54','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Urin Rutin',  'exam_name'=>'Warna Urin',     'unit'=>null,  'normal_range'=>'Kuning jernih','default_value'=>'Kuning jernih'],
                ['package_id'=>1,'exam_category'=>'Urin Rutin',  'exam_name'=>'pH Urin',        'unit'=>null,  'normal_range'=>'4.5-8','default_value'=>null],
                ['package_id'=>1,'exam_category'=>'Urin Rutin',  'exam_name'=>'Protein Urin',   'unit'=>null,  'normal_range'=>'Negatif','default_value'=>'Negatif'],
                ['package_id'=>1,'exam_category'=>'Urin Rutin',  'exam_name'=>'Glukosa Urin',   'unit'=>null,  'normal_range'=>'Negatif','default_value'=>'Negatif'],
                // Comprehensive
                ['package_id'=>2,'exam_category'=>'Vital Sign',  'exam_name'=>'Tekanan Darah',  'unit'=>'mmHg','normal_range'=>'<140/90','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Vital Sign',  'exam_name'=>'Denyut Nadi',    'unit'=>'x/mnt','normal_range'=>'60-100','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Vital Sign',  'exam_name'=>'Suhu Tubuh',     'unit'=>'°C',  'normal_range'=>'36-37.5','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Vital Sign',  'exam_name'=>'Berat Badan',    'unit'=>'kg',  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Vital Sign',  'exam_name'=>'Tinggi Badan',   'unit'=>'cm',  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Vital Sign',  'exam_name'=>'IMT',            'unit'=>'kg/m²','normal_range'=>'18.5-24.9','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Darah Rutin', 'exam_name'=>'Hemoglobin',     'unit'=>'g/dL','normal_range'=>'12-18','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Darah Rutin', 'exam_name'=>'Leukosit',       'unit'=>'ribu/µL','normal_range'=>'4-11','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Darah Rutin', 'exam_name'=>'Trombosit',      'unit'=>'ribu/µL','normal_range'=>'150-450','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Darah Rutin', 'exam_name'=>'Hematokrit',     'unit'=>'%',   'normal_range'=>'37-54','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'Gula Darah Puasa','unit'=>'mg/dL','normal_range'=>'70-126','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'Kolesterol Total','unit'=>'mg/dL','normal_range'=>'<200','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'Trigliserida',   'unit'=>'mg/dL','normal_range'=>'<150','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'HDL',            'unit'=>'mg/dL','normal_range'=>'>40','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'LDL',            'unit'=>'mg/dL','normal_range'=>'<130','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'Asam Urat',      'unit'=>'mg/dL','normal_range'=>'2.4-7.0','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'Kreatinin',      'unit'=>'mg/dL','normal_range'=>'0.6-1.2','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'SGOT',           'unit'=>'U/L', 'normal_range'=>'<40','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Kimia Darah', 'exam_name'=>'SGPT',           'unit'=>'U/L', 'normal_range'=>'<40','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Urin Rutin',  'exam_name'=>'Warna Urin',     'unit'=>null,  'normal_range'=>'Kuning jernih','default_value'=>'Kuning jernih'],
                ['package_id'=>2,'exam_category'=>'Urin Rutin',  'exam_name'=>'pH Urin',        'unit'=>null,  'normal_range'=>'4.5-8','default_value'=>null],
                ['package_id'=>2,'exam_category'=>'Urin Rutin',  'exam_name'=>'Protein Urin',   'unit'=>null,  'normal_range'=>'Negatif','default_value'=>'Negatif'],
                ['package_id'=>2,'exam_category'=>'Urin Rutin',  'exam_name'=>'Glukosa Urin',   'unit'=>null,  'normal_range'=>'Negatif','default_value'=>'Negatif'],
                ['package_id'=>2,'exam_category'=>'Radiologi',   'exam_name'=>'Rontgen Thorax', 'unit'=>null,  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>2,'exam_category'=>'EKG',         'exam_name'=>'Elektrokardiogram','unit'=>null, 'normal_range'=>null,'default_value'=>null],
                // Executive
                ['package_id'=>3,'exam_category'=>'Vital Sign',  'exam_name'=>'Tekanan Darah',  'unit'=>'mmHg','normal_range'=>'<140/90','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Vital Sign',  'exam_name'=>'Denyut Nadi',    'unit'=>'x/mnt','normal_range'=>'60-100','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Vital Sign',  'exam_name'=>'Suhu Tubuh',     'unit'=>'°C',  'normal_range'=>'36-37.5','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Vital Sign',  'exam_name'=>'Berat Badan',    'unit'=>'kg',  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Vital Sign',  'exam_name'=>'Tinggi Badan',   'unit'=>'cm',  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Vital Sign',  'exam_name'=>'IMT',            'unit'=>'kg/m²','normal_range'=>'18.5-24.9','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Darah Rutin', 'exam_name'=>'Hemoglobin',     'unit'=>'g/dL','normal_range'=>'12-18','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Darah Rutin', 'exam_name'=>'Leukosit',       'unit'=>'ribu/µL','normal_range'=>'4-11','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Darah Rutin', 'exam_name'=>'Trombosit',      'unit'=>'ribu/µL','normal_range'=>'150-450','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Darah Rutin', 'exam_name'=>'Hematokrit',     'unit'=>'%',   'normal_range'=>'37-54','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'Gula Darah Puasa','unit'=>'mg/dL','normal_range'=>'70-126','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'Kolesterol Total','unit'=>'mg/dL','normal_range'=>'<200','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'Trigliserida',   'unit'=>'mg/dL','normal_range'=>'<150','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'HDL',            'unit'=>'mg/dL','normal_range'=>'>40','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'LDL',            'unit'=>'mg/dL','normal_range'=>'<130','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'Asam Urat',      'unit'=>'mg/dL','normal_range'=>'2.4-7.0','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'Kreatinin',      'unit'=>'mg/dL','normal_range'=>'0.6-1.2','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'SGOT',           'unit'=>'U/L', 'normal_range'=>'<40','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'SGPT',           'unit'=>'U/L', 'normal_range'=>'<40','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Kimia Darah', 'exam_name'=>'HbA1c',          'unit'=>'%',   'normal_range'=>'<6.5','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Urin Rutin',  'exam_name'=>'Warna Urin',     'unit'=>null,  'normal_range'=>'Kuning jernih','default_value'=>'Kuning jernih'],
                ['package_id'=>3,'exam_category'=>'Urin Rutin',  'exam_name'=>'pH Urin',        'unit'=>null,  'normal_range'=>'4.5-8','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Urin Rutin',  'exam_name'=>'Protein Urin',   'unit'=>null,  'normal_range'=>'Negatif','default_value'=>'Negatif'],
                ['package_id'=>3,'exam_category'=>'Urin Rutin',  'exam_name'=>'Glukosa Urin',   'unit'=>null,  'normal_range'=>'Negatif','default_value'=>'Negatif'],
                ['package_id'=>3,'exam_category'=>'Radiologi',   'exam_name'=>'Rontgen Thorax', 'unit'=>null,  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>3,'exam_category'=>'EKG',         'exam_name'=>'Elektrokardiogram','unit'=>null, 'normal_range'=>null,'default_value'=>null],
                ['package_id'=>3,'exam_category'=>'USG',         'exam_name'=>'USG Abdomen',    'unit'=>null,  'normal_range'=>null,'default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Spirometri',  'exam_name'=>'FVC',            'unit'=>'L',   'normal_range'=>null,'default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Spirometri',  'exam_name'=>'FEV1',           'unit'=>'L',   'normal_range'=>null,'default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Audiometri',  'exam_name'=>'Audiometri Kanan','unit'=>'dB', 'normal_range'=>'<25','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Audiometri',  'exam_name'=>'Audiometri Kiri', 'unit'=>'dB', 'normal_range'=>'<25','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Visus',       'exam_name'=>'Visus Mata Kanan','unit'=>null, 'normal_range'=>'6/6','default_value'=>null],
                ['package_id'=>3,'exam_category'=>'Visus',       'exam_name'=>'Visus Mata Kiri', 'unit'=>null, 'normal_range'=>'6/6','default_value'=>null],
            ]);
        }

        // Sample patients
        if (DB::table('patients')->count() === 0) {
            DB::table('patients')->insert([
                ['patient_no'=>'P-00001','name'=>'Ahmad Fauzi', 'gender'=>'Male',  'birth_date'=>'1985-03-15','nik'=>'3175012345678901','phone'=>'081234567890','email'=>'ahmad@email.com','company'=>'PT Maju Bersama',   'created_at'=>now(),'updated_at'=>now()],
                ['patient_no'=>'P-00002','name'=>'Dewi Lestari','gender'=>'Female','birth_date'=>'1990-07-22','nik'=>'3175098765432109','phone'=>'082345678901','email'=>'dewi@email.com', 'company'=>'PT Sejahtera Abadi','created_at'=>now(),'updated_at'=>now()],
            ]);
        }

        // Sample registration
        if (DB::table('registrations')->count() === 0) {
            DB::table('registrations')->insert([
                ['reg_no'=>'REG-2026-0001','patient_id'=>1,'package_id'=>2,'doctor_id'=>2,'reg_date'=>'2026-06-27','exam_date'=>'2026-06-27','status'=>'in_progress','created_by'=>1,'created_at'=>now(),'updated_at'=>now()],
            ]);
        }
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaKelas7to9Seeder extends Seeder
{
    public function run(): void
    {
        $tempatLahir = ['Bandung', 'Jakarta', 'Bekasi', 'Bogor', 'Depok', 'Tangerang', 'Cirebon'];
        $namaAyah = ['Ahmad', 'Budi', 'Dedi', 'Eko', 'Hendra', 'Irfan', 'Joko'];
        $namaIbu = ['Ani', 'Dewi', 'Erni', 'Fitri', 'Indah', 'Kartika', 'Sari'];

        $siswaData = [
            // 7A (27 siswa)
            ['252607001', 'Adhi Sopyandhi Abdul Rohim', '7A'], ['252607002', 'Aditya Pratama', '7A'],
            ['252607003', 'Adiwa Irmansyah', '7A'], ['252607004', 'Adrian Trias Winata', '7A'],
            ['252607025', 'Bari Arya Putra', '7A'], ['252607027', 'Darvesh Muhammad Nizam', '7A'],
            ['252607033', 'Faeyza Nur Putra Prasetya', '7A'], ['252607035', 'Fakhri Fayyadh Deandhy', '7A'],
            ['252607041', 'Gavin Farhan Sudianto', '7A'], ['252607047', 'Irsyad Dzaky Wijaya', '7A'],
            ['252607051', 'Keanu Azifan Alfaiq', '7A'], ['252607058', 'M Altaf Zulfiqar', '7A'],
            ['252607059', 'M Rafa Al Fatih', '7A'], ['252607064', 'Maheer Hazami Al Hariery', '7A'],
            ['252607065', 'Moch Agna Hasan Muztaba', '7A'], ['252607070', 'Muhamad Alkendreva Muharian', '7A'],
            ['252607071', 'Muhamad Ilyasa Haerullah', '7A'], ['252607074', 'Muhammad Adli Faeyza Ali', '7A'],
            ['252607075', 'Muhammad Agler Zeroun', '7A'], ['252607076', 'Muhammad Alghifari Taftazani', '7A'],
            ['252607088', 'Muhammad Fikri Al Qodiri', '7A'], ['252607089', 'Muhammad Hafidz Kadaffi', '7A'],
            ['252607094', 'Muhammad Nafis Ariyan', '7A'], ['252607097', 'Muhammad Radith Prasetya', '7A'],
            ['252607103', 'Muhammad Syauqi Hajianto Zain', '7A'], ['252607104', 'Muhammad Tegar Susanto', '7A'],
            ['252607131', 'Raffa Khairan Nst', '7A'],

            // 7B (25 siswa)
            ['252607005', 'Ahlam Dhiya\'ul Tsany', '7B'], ['252607006', 'Ahmad Ihsan Suwargana', '7B'],
            ['252607007', 'Alfath Nurirsyad', '7B'], ['252607011', 'Andery Aprilian Ricardo', '7B'],
            ['252607028', 'Deffa Hauzan Irhab Nabil', '7B'], ['252607037', 'Fathan Ahmad Athallah Ramadhan', '7B'],
            ['252607042', 'Gazwan Mighfar Ganiya', '7B'], ['252607048', 'Isyraq Fauzansyah', '7B'],
            ['252607052', 'Kelvin Alwan Nasirin', '7B'], ['252607060', 'M Zio Nugraha Al\'khalifi', '7B'],
            ['252607061', 'M Nugraha Farzadaq Al-Jabbar', '7B'], ['252607067', 'Mochammad Raihan Putra Karyadi Al-Fayyadh', '7B'],
            ['252607072', 'Muhamad Willy', '7B'], ['252607073', 'Muhammad Abdulhafiz', '7B'],
            ['252607078', 'Muhammad Ali Akbar', '7B'], ['252607079', 'Muhammad Altaf El Zahrawi', '7B'],
            ['252607080', 'Muhammad Azmi Fadhilah', '7B'], ['252607081', 'Muhammad Azril Alghani', '7B'],
            ['252607090', 'Muhammad Hafiz Arrahman', '7B'], ['252607091', 'Muhammad Hudzaifah Alyamani', '7B'],
            ['252607095', 'Muhammad Nizar Hafidzullah', '7B'], ['252607105', 'Muhammad Tsabit Qeis Al Ma\'soem', '7B'],
            ['252607133', 'Raizan Anby Alkazan', '7B'], ['252607136', 'Rizqia Langit Saputra', '7B'],
            ['252607147', 'Syahdad Fariz Arasya', '7B'],

            // 7C (30 siswa)
            ['252607016', 'Ardhana Kahlan Khalif', '7C'], ['252607018', 'Arsyad Tangguh Prayata', '7C'],
            ['252607021', 'Athila Deren Rusmiadi', '7C'], ['252607022', 'Azmi Fajar Royhan', '7C'],
            ['252607024', 'Azri Nun Cahya Daffa', '7C'], ['252607031', 'Dirga Zachary Raditya', '7C'],
            ['252607036', 'Farzan Ahza Al-Ghani', '7C'], ['252607039', 'Fayzan Afzal Fyansyah', '7C'],
            ['252607040', 'Fiil Ibrahim', '7C'], ['252607056', 'Kindi Al Raziq Mughnil Arifin', '7C'],
            ['252607062', 'M Raihan Rahmat Akbar', '7C'], ['252607063', 'M Ziqri Nurfaeyza Usman', '7C'],
            ['252607068', 'Muhamad Agam Abdillah Ardhani', '7C'], ['252607069', 'Muhamad Alawil Hadad', '7C'],
            ['252607082', 'Muhammad Chaviez Wahyudi', '7C'], ['252607083', 'Muhammad Daffa Khairi', '7C'],
            ['252607084', 'Muhammad Dafi Salman Zulkarnain', '7C'], ['252607085', 'Muhammad Faiz Rafif Firmansyah', '7C'],
            ['252607086', 'Muhammad Farhan', '7C'], ['252607087', 'Muhammad Fatih Syanovin', '7C'],
            ['252607092', 'Muhammad Kenzie Redhaputera', '7C'], ['252607093', 'Muhammad Kevin Lazuardi Hidayat', '7C'],
            ['252607096', 'Muhammad Padli Abdillah', '7C'], ['252607101', 'Muhammad Shidqi Al Khalifi', '7C'],
            ['252607102', 'Muhammad Syailendra Hilbran', '7C'], ['252607107', 'Muhammad Zibril Al Ghifari', '7C'],
            ['252607114', 'Najmi Syauqi', '7C'], ['252607123', 'Prabu Arya Pramabirru', '7C'],
            ['252607139', 'Safaraz Fadhlurahman Faeyza', '7C'], ['252607143', 'Shofwan Khairul Hakim Sugondho', '7C'],

            // 8A (25 siswa)
            ['242507004', 'Ahmad Zaki Sya\'bana', '8A'], ['242507015', 'Athfan Musaffa', '8A'],
            ['242507020', 'Azky Seftianwar', '8A'], ['242507024', 'Dzulfiqor Fathan Mubina', '8A'],
            ['242507026', 'Esa Abdillah Febrian', '8A'], ['242507028', 'Fathir Rizky Widharma', '8A'],
            ['242507032', 'Fhaiz Putra Sobandi', '8A'], ['242507036', 'Haikal Safaraz Nuryana Mansobey', '8A'],
            ['242507038', 'Hastbie Muzaky', '8A'], ['242507039', 'Hikam Arfan Rifa\'i', '8A'],
            ['242507053', 'Muhamad Daffa Fairuz', '8A'], ['242507055', 'Muhamad Kahfi Hartana Buldan', '8A'],
            ['242507129', 'Muhammad Dimas Pratama', '8A'], ['242507072', 'Muhammad Ikbal Albar Sofyan', '8A'],
            ['242507073', 'Muhammad Kenji Aydin Estefan', '8A'], ['242507074', 'Muhammad Khalifa Fadhirrahman Al-Fath', '8A'],
            ['242507075', 'Muhammad Nizar Pratama', '8A'], ['242507076', 'Muhammad Raffi Hilal Zain', '8A'],
            ['242507078', 'Muhammad Rizal Maulana', '8A'], ['242507080', 'Muhammad Zulfikar Maulana Yusuf', '8A'],
            ['242507081', 'Mukafihuddin Ansharullah', '8A'], ['242507088', 'Nicsan Abdilah Yusuf', '8A'],
            ['242507091', 'Parel Prasatria Putra Al Taupik', '8A'], ['242507112', 'Syamie Qolbiya Sidik', '8A'],
            ['242507117', 'Wafy Azka Al Ghassan', '8A'],

            // 8B (26 siswa)
            ['242507002', 'Afwan Abdul Mughny', '8B'], ['242507013', 'Alvis Alfareza Pratama', '8B'],
            ['242507017', 'Azka Faqih Abdul Halim', '8B'], ['242507019', 'Azka Muhammad Dzaky', '8B'],
            ['242507023', 'Dzaki Pratama Ramadhan', '8B'], ['242507030', 'Fatra Aria Rizki Sofyan', '8B'],
            ['242507031', 'Fauzan Azaria', '8B'], ['242507034', 'Hafidzurrahman Al Haidin', '8B'],
            ['242507041', 'Khalid Al Miski', '8B'], ['242507047', 'M Kenzi Khairan Althaf', '8B'],
            ['242507050', 'Mauza Raffa Ilyasa', '8B'], ['242507051', 'Moh Rafi Arraihan', '8B'],
            ['242507052', 'Muhamad Azka Fathulhakim', '8B'], ['242507056', 'Muhamad Riza Fadil Alkadri', '8B'],
            ['242507057', 'Muhammad Abduh Bima Fathan', '8B'], ['242507060', 'Muhammad Alwy Al Mundzir', '8B'],
            ['242507066', 'Muhammad Fathi Nawawi', '8B'], ['242507067', 'Muhammad Fauzan Al Fatih', '8B'],
            ['242507068', 'Muhammad Fauzi Septiana', '8B'], ['242507071', 'Muhammad Hafizh An Nawawi', '8B'],
            ['242507084', 'Naufal Afkar Idian Sugiarto', '8B'], ['242507095', 'Raditya Alfarezel Budiana', '8B'],
            ['242507096', 'Rafan Alman Syafiq Hermawan', '8B'], ['242507099', 'Rafka Mulki Wardana', '8B'],
            ['242507101', 'Raksa Fadilah Hadiana', '8B'], ['242507106', 'Rayendra Kevin Dharmawan', '8B'],

            // 8C (24 siswa)
            ['242507124', 'Ahmad Wasi Mustofa', '8C'], ['242507010', 'Alfatir Arya Pamungkas', '8C'],
            ['242507012', 'Alpi Halfaz Gobel', '8C'], ['242507046', 'M Dimas Aufar Aprilio', '8C'],
            ['242507048', 'M Sahir Nizam Fahreza', '8C'], ['242507054', 'Muhamad Dzakwan Nugraha', '8C'],
            ['242507059', 'Muhammad Al Mizan Ibrahim', '8C'], ['242507061', 'Muhammad Ardio Putra Adhari', '8C'],
            ['242507062', 'Muhammad Ariq Azzamy', '8C'], ['242507063', 'Muhammad Arkan Kurniawan', '8C'],
            ['242507064', 'Muhammad Dzahwan', '8C'], ['242507065', 'Muhammad Fadhil Alkaff', '8C'],
            ['242507069', 'Muhammad Hadziq Zariz Hidayat', '8C'], ['242507070', 'Muhammad Hafizd Rahmadani', '8C'],
            ['242507077', 'Muhammad Raihan Alfarizi', '8C'], ['242507085', 'Naupal Faiq Fauzi', '8C'],
            ['242507089', 'Nizar Hafiz Rahman', '8C'], ['242507094', 'Radinka Mahiradzikra Apriyadi', '8C'],
            ['242507097', 'Raffa Akbar Ramadhan', '8C'], ['242507098', 'Rafiqi Haikal Ramadhan', '8C'],
            ['242507108', 'Rihan Shafeequr Rahman', '8C'], ['242507110', 'Sultoni Rizki Agnia', '8C'],
            ['242507113', 'Taqi Faydh Al Isfahan', '8C'], ['242507116', 'Vino Arkand Al Fathir', '8C'],

            // 9A (28 siswa)
            ['232407002', 'Abdullah Adzka Adila', '9A'], ['232407006', 'Ahmad Fathir Azzamy', '9A'],
            ['232407037', 'Haryo Dwi Kusdimanto', '9A'], ['232407039', 'Ihsan Abdurrahman', '9A'],
            ['232407049', 'Langit Sapta Mahendra', '9A'], ['232407050', 'M Akhyar Ramdani', '9A'],
            ['232407051', 'M Azfar Rafa Rafidan', '9A'], ['232407054', 'M Fazri Rivaldi', '9A'],
            ['232407055', 'M Rafa Al-Rasyid Hidayat', '9A'], ['232407060', 'Maulana Jerry Arrizal', '9A'],
            ['232407063', 'Mochammad Zhofran Al Mughni', '9A'], ['232407064', 'Muhamad Abyan Musama Ramadhan', '9A'],
            ['232407066', 'Muhamad Rigan Sami Akyas', '9A'], ['232407067', 'Muhamad Zaki Algifari', '9A'],
            ['232407068', 'Muhammad Akbaruddin Al Azmi', '9A'], ['232407075', 'Muhammad Eshan Fadlan Prajasasmita', '9A'],
            ['232407078', 'Muhammad Hadzaq Zaidan Hidayat', '9A'], ['232407081', 'Muhammad Hisyam Handayana', '9A'],
            ['232407084', 'Muhammad Luthfi Fauzan', '9A'], ['232407085', 'Muhammad Mikdad', '9A'],
            ['232407086', 'Muhammad Naufal Ardhani', '9A'], ['232407089', 'Muhammad Raihan Arrizky', '9A'],
            ['232407090', 'Muhammad Rayyan Rizqullah', '9A'], ['232407091', 'Muhammad Rezky Fadillah', '9A'],
            ['232407092', 'Muhammad Rifqy Adhitya Munandar', '9A'], ['232407093', 'Muhammad Sabiq Hisyami', '9A'],
            ['232407096', 'Muhammad Syafiq Hasyimi', '9A'], ['232407111', 'Rasyad Syazwan Amzar Hermawan', '9A'],

            // 9B (29 siswa)
            ['232407003', 'Abiyyu Majid', '9B'], ['232407004', 'Achmad Fajrin Santoso', '9B'],
            ['232407005', 'Adam Arkaban Arif', '9B'], ['232407007', 'Ahmad Sobri Nawawi', '9B'],
            ['232407008', 'Ahza Thilal Rizki', '9B'], ['232407009', 'Akbar Fadillah Buchori', '9B'],
            ['232407015', 'Alyasser Muhasbi', '9B'], ['232407016', 'Andika Maulana Mustajab', '9B'],
            ['232407033', 'Habibie Arkan Al Aziz', '9B'], ['232407041', 'Julfan Arumi Suwargana', '9B'],
            ['232407047', 'Kholipiani', '9B'], ['232407052', 'M Gaza Abdulloh', '9B'],
            ['232407053', 'M Amar Al-Mansyuri', '9B'], ['232407056', 'M Rafka Alkhanza', '9B'],
            ['232407061', 'Moch Gyast Algazali', '9B'], ['232407062', 'Moch Fathih Maulidi', '9B'],
            ['232407065', 'Muhamad Perkasa Gunawan', '9B'], ['232407070', 'Muhammad Annizam Iqram', '9B'],
            ['232407071', 'Muhammad Arfan Sya\'Dan Athaillah', '9B'], ['232407072', 'Muhammad Azka Arroyan', '9B'],
            ['232407073', 'Muhammad Azriel Heriyandi', '9B'], ['232407074', 'Muhammad Dzaky Putra Wijanarko', '9B'],
            ['232407079', 'Muhammad Haidar Guna Utama', '9B'], ['232407080', 'Muhammad Hibatullah Shobhi', '9B'],
            ['232407082', 'Muhammad Ihsan Athaya Ferdiansyah', '9B'], ['232407087', 'Muhammad Nazril Adrian Padma', '9B'],
            ['232407088', 'Muhammad Rafli Pratama', '9B'], ['232407095', 'Muhammad Sulthan Maliki', '9B'],
            ['232407114', 'Ruby Aditya', '9B'],
        ];

        DB::beginTransaction();
        try {
            foreach ($siswaData as $index => $d) {
                $kelas = Kelas::where('nama_kelas', $d[2])->first();
                if (!$kelas) continue;

                $lastName = explode(' ', $d[1])[count(explode(' ', $d[1])) - 1];
                $birthYear = $d[2][0] == '7' ? 2013 : ($d[2][0] == '8' ? 2012 : 2011);
                $tanggalMasuk = $d[2][0] == '7' ? '2025-07-01' : ($d[2][0] == '8' ? '2024-07-01' : '2023-07-01');

                Siswa::updateOrCreate(['nis' => $d[0]], [
                    'nisn' => str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT),
                    'nama' => $d[1],
                    'jenis_kelamin' => 'Laki-laki',
                    'tanggal_lahir' => sprintf('%d-%02d-%02d', $birthYear, rand(1,12), rand(1,28)),
                    'tempat_lahir' => $tempatLahir[$index % count($tempatLahir)],
                    'nama_ayah' => $namaAyah[$index % count($namaAyah)] . ' ' . $lastName,
                    'nama_ibu' => $namaIbu[$index % count($namaIbu)] . ' ' . $lastName,
                    'status_akademik' => 'Aktif',
                    'status_tahfidz' => 'Sedang Berjalan',
                    'status_penempatan' => 'Belum Ditempatkan',
                    'status_masuk' => 'Baru',
                    'tanggal_masuk' => $tanggalMasuk,
                    'punya_hafalan_sebelumnya' => false,
                    'perlu_tes_penempatan' => false,
                    'status_aktif' => true,
                    'kelas_id' => $kelas->id,
                ]);
            }
            DB::commit();
            $this->command->info('✅ Berhasil menambahkan ' . count($siswaData) . ' siswa (kelas 7, 8, dan 9)!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}

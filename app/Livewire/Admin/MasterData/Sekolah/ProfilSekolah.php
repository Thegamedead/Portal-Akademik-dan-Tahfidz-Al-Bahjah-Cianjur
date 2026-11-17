<?php

namespace App\Livewire\Admin\MasterData\Sekolah;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\MasterData\SekolahService;

#[Layout('admin.layouts.app')]
class ProfilSekolah extends Component
{
    // Sekolah fields
    public $nama_sekolah, $npsn, $status_sekolah, $akreditasi;
    public $no_telepon, $email, $website;

    // Address fields (using alamat_* convention)
    public $alamat_jalan, $alamat_dusun, $alamat_rt, $alamat_rw;
    public $alamat_desa_kelurahan, $alamat_kecamatan;
    public $alamat_kabupaten_kota, $alamat_provinsi, $alamat_kode_pos;

    public $isExisting = false;

    protected $rules = [
        'nama_sekolah' => 'required|string|max:255',
        'npsn'         => 'required|digits:8',
        'status_sekolah' => 'required|in:Negeri,Swasta',
        'akreditasi'   => 'required|in:A,B,C,Belum Terakreditasi',
        'no_telepon'   => 'required|digits_between:10,13',
        'email'        => 'nullable|email',
        'website'      => 'nullable|url',
        'alamat_jalan' => 'required|string',
        'alamat_rt'    => 'nullable|digits_between:1,3',
        'alamat_rw'    => 'nullable|digits_between:1,3',
        'alamat_desa_kelurahan'  => 'required|string',
        'alamat_kecamatan'       => 'required|string',
        'alamat_kabupaten_kota'  => 'required|string',
        'alamat_provinsi'        => 'required|string',
        'alamat_kode_pos'        => 'nullable|digits:5',
    ];

    protected $messages = [
        'npsn.required'          => 'NPSN wajib diisi.',
        'npsn.digits'            => 'NPSN harus tepat 8 digit angka.',
        'no_telepon.digits_between' => 'No. Telepon harus berupa angka antara 10 hingga 13 digit.',
        'alamat_rt.digits_between'  => 'RT harus berupa angka maksimal 3 digit.',
        'alamat_rw.digits_between'  => 'RW harus berupa angka maksimal 3 digit.',
        'alamat_kode_pos.digits' => 'Kode pos harus tepat 5 digit angka.',
    ];

    protected $validationAttributes = [
        'nama_sekolah'          => 'Nama Sekolah',
        'npsn'                  => 'NPSN',
        'status_sekolah'        => 'Status Sekolah',
        'akreditasi'            => 'Akreditasi',
        'no_telepon'            => 'No. Telepon',
        'email'                 => 'Email',
        'website'               => 'Website',
        'alamat_jalan'          => 'Jalan',
        'alamat_rt'             => 'RT',
        'alamat_rw'             => 'RW',
        'alamat_desa_kelurahan' => 'Desa/Kelurahan',
        'alamat_kecamatan'      => 'Kecamatan',
        'alamat_kabupaten_kota' => 'Kabupaten/Kota',
        'alamat_provinsi'       => 'Provinsi',
        'alamat_kode_pos'       => 'Kode Pos',
    ];

    public function mount(SekolahService $service)
    {
        $sekolah = $service->getOrNew();

        if ($sekolah->exists) {
            $this->fill($sekolah->toArray());
            $this->isExisting = true;
        }
    }

    /**
     * Validasi realtime saat field berubah
     */
    public function updated($field): void
    {
        $this->validateOnly($field);
    }

    public function save(SekolahService $service)
    {
        $this->validate();

        try {
            $service->save($this->all());

            $this->dispatch('toast', variant: 'sukses', message: 'Profil sekolah berhasil disimpan');
            $this->dispatch('sekolah-updated'); // Event untuk refresh sidebar
            $this->isExisting = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal menyimpan profil sekolah: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.master-data.sekolah.profil-sekolah');
    }
}

# Custom Property-Based Testing Framework

## Kenapa bukan `giorgiosironi/eris`?

Package `giorgiosironi/eris` **tidak kompatibel** dengan stack proyek ini:

| | Eris 1.1.0 (latest) | Proyek ini |
|---|---|---|
| PHPUnit | `^10.0.4` | `^12.5.x` ✗ |
| PHP | `^8.1` | `^8.3` ✓ |

Karena Eris hanya mendukung PHPUnit ^10 dan proyek ini menggunakan PHPUnit 12.5 (bundled
bersama Laravel 13), maka dibuatlah framework PBT minimal ini secara native — tanpa
dependensi tambahan.

## Cara Kerja

Framework ini mengimplementasikan pola property-based testing yang sama dengan Eris/QuickCheck:

1. **Generator** — objek yang menghasilkan satu nilai acak dari suatu domain.
2. **PropertyTestRunner** — menjalankan properti (callable PHPUnit assertion) N kali dengan input acak berbeda.
3. **PropertyTestTrait** — mixin untuk test class, menyediakan `forAll()`.
4. **PropertyTestCase** — base class untuk semua PBT test (extends Laravel TestCase + trait).

## Cara Penggunaan

```php
use Tests\Support\PropertyTesting\Generators;
use Tests\Support\PropertyTesting\PropertyTestCase;

class ContohPropertyTest extends PropertyTestCase
{
    public function test_suatu_properti(): void
    {
        $this->forAll(
            Generators::guard(),          // 'admin'|'guru'|'siswa'
            Generators::string(1, 50),    // random string
        )->check(function (string $guard, string $input) {
            // tulis assertion PHPUnit di sini
            $this->assertNotEmpty($guard);
        });
    }

    // Kustomisasi:
    // ->withIterations(50)   // ubah jumlah iterasi (default 100)
    // ->withSeed(42)         // seed untuk repro deterministik
}
```

## Generator yang Tersedia

| Generator | Deskripsi |
|---|---|
| `Generators::string($min, $max)` | Random printable ASCII string |
| `Generators::int($min, $max)` | Random integer dalam range |
| `Generators::bool()` | Random boolean |
| `Generators::choose([...])` | Pilih acak dari array nilai |
| `Generators::date($from, $to)` | Random date string Y-m-d |
| `Generators::guard()` | `'admin'`, `'guru'`, atau `'siswa'` |
| `Generators::username()` | String username 6–20 karakter |
| `Generators::niy()` | Format NIY: `YY-NNN` |
| `Generators::nis()` | Numeric string 5–10 digit |
| `Generators::ipv4()` | Random IPv4 address |
| `Generators::password()` | Password string 8–32 karakter |
| `Generators::nama()` | Nama orang 2–4 kata |

## Menjalankan Test

Karena PHP CLI default Laragon di environment ini menunjuk ke PHP 8.2, gunakan binary PHP 8.3 secara eksplisit:

```powershell
# Jalankan semua PBT
C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe vendor/bin/phpunit tests/Feature/Auth --no-coverage

# Atau tambahkan C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\ ke PATH agar 'php' langsung PHP 8.3
```

> **Catatan**: Laragon memiliki PHP 8.3.30 di `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`.
> Switch PHP CLI di Laragon: klik kanan icon Laragon → PHP → pilih 8.3.

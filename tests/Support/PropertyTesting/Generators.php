<?php

namespace Tests\Support\PropertyTesting;

/**
 * Factory for common generators.
 *
 * Usage:
 *   Generators::string()           → random printable ASCII strings
 *   Generators::int(1, 100)        → random integers in range
 *   Generators::date()             → random Carbon/string dates
 *   Generators::choose([...])      → pick randomly from an array
 *   Generators::username()         → realistic username strings
 *   Generators::niy()              → NIY format strings
 *   Generators::nis()              → NIS format strings
 *   Generators::ipv4()             → random IPv4 addresses
 */
class Generators
{
    // ---------------------------------------------------------------------------
    // Primitives
    // ---------------------------------------------------------------------------

    /**
     * Random printable ASCII string, length between $minLen and $maxLen.
     */
    public static function string(int $minLen = 1, int $maxLen = 20): Generator
    {
        return new class($minLen, $maxLen) extends Generator {
            public function __construct(private int $min, private int $max) {}

            public function generate(): string
            {
                $len = random_int($this->min, $this->max);
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $result = '';
                for ($i = 0; $i < $len; $i++) {
                    $result .= $chars[random_int(0, strlen($chars) - 1)];
                }
                return $result;
            }
        };
    }

    /**
     * Random integer in [$min, $max].
     */
    public static function int(int $min = 0, int $max = PHP_INT_MAX): Generator
    {
        return new class($min, $max) extends Generator {
            public function __construct(private int $min, private int $max) {}

            public function generate(): int
            {
                return random_int($this->min, $this->max);
            }
        };
    }

    /**
     * Pick randomly from a fixed set of values.
     *
     * @param array<mixed> $values
     */
    public static function choose(array $values): Generator
    {
        return new class($values) extends Generator {
            public function __construct(private array $values) {}

            public function generate(): mixed
            {
                return $this->values[array_rand($this->values)];
            }
        };
    }

    /**
     * Random boolean.
     */
    public static function bool(): Generator
    {
        return new class() extends Generator {
            public function generate(): bool
            {
                return (bool) random_int(0, 1);
            }
        };
    }

    // ---------------------------------------------------------------------------
    // Date generators
    // ---------------------------------------------------------------------------

    /**
     * Random date string (Y-m-d) between two dates.
     */
    public static function date(string $from = '1970-01-01', string $to = '2005-12-31'): Generator
    {
        return new class($from, $to) extends Generator {
            public function __construct(private string $from, private string $to) {}

            public function generate(): string
            {
                $min = strtotime($this->from);
                $max = strtotime($this->to);
                $ts  = random_int($min, $max);
                return date('Y-m-d', $ts);
            }
        };
    }

    // ---------------------------------------------------------------------------
    // Domain-specific generators (Sistem Informasi Tahfidz)
    // ---------------------------------------------------------------------------

    /**
     * Username-like string: 6–20 alphanumeric + underscore chars.
     */
    public static function username(): Generator
    {
        return new class() extends Generator {
            public function generate(): string
            {
                $len   = random_int(6, 20);
                $chars = 'abcdefghijklmnopqrstuvwxyz0123456789_';
                $result = '';
                for ($i = 0; $i < $len; $i++) {
                    $result .= $chars[random_int(0, strlen($chars) - 1)];
                }
                return $result;
            }
        };
    }

    /**
     * NIY (Nomor Induk Yayasan) format: YY-NNN, e.g. "23-001".
     */
    public static function niy(): Generator
    {
        return new class() extends Generator {
            public function generate(): string
            {
                $year  = str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT);
                $seq   = str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
                return "{$year}-{$seq}";
            }
        };
    }

    /**
     * NIS (Nomor Induk Siswa) format: 5–10 digit numeric string.
     */
    public static function nis(): Generator
    {
        return new class() extends Generator {
            public function generate(): string
            {
                $len    = random_int(5, 10);
                $result = (string) random_int(1, 9); // no leading zero
                for ($i = 1; $i < $len; $i++) {
                    $result .= (string) random_int(0, 9);
                }
                return $result;
            }
        };
    }

    /**
     * Random IPv4 address string.
     */
    public static function ipv4(): Generator
    {
        return new class() extends Generator {
            public function generate(): string
            {
                return implode('.', [
                    random_int(1, 254),
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(1, 254),
                ]);
            }
        };
    }

    /**
     * Guard name: one of 'admin', 'guru', 'siswa'.
     */
    public static function guard(): Generator
    {
        return static::choose(['admin', 'guru', 'siswa']);
    }

    /**
     * Password string: 8–32 printable chars.
     */
    public static function password(): Generator
    {
        return new class() extends Generator {
            public function generate(): string
            {
                $len = random_int(8, 32);
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
                $result = '';
                for ($i = 0; $i < $len; $i++) {
                    $result .= $chars[random_int(0, strlen($chars) - 1)];
                }
                return $result;
            }
        };
    }

    /**
     * Nama (person name): 2-4 words of capitalised Latin letters.
     */
    public static function nama(): Generator
    {
        return new class() extends Generator {
            private array $syllables = [
                'Ab','Ad','Ah','Al','Am','An','Ar','As','At','Az',
                'Ba','Bi','Bu','Ca','Da','Di','Du','Fa','Fi','Ga',
                'Ha','Hi','Hu','In','Ja','Ji','Ka','Ki','La','Li',
                'Ma','Mi','Na','Ni','Nu','Pa','Ra','Ri','Sa','Si',
                'Su','Ta','Ti','Tu','Wa','Wi','Ya','Yu','Za','Zi',
            ];

            public function generate(): string
            {
                $wordCount = random_int(2, 4);
                $words = [];
                for ($w = 0; $w < $wordCount; $w++) {
                    $sylCount = random_int(2, 3);
                    $word = '';
                    for ($s = 0; $s < $sylCount; $s++) {
                        $word .= $this->syllables[array_rand($this->syllables)];
                    }
                    $words[] = ucfirst(strtolower($word));
                }
                return implode(' ', $words);
            }
        };
    }
}

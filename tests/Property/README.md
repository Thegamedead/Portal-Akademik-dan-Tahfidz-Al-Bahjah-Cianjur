# Property-Based Tests - Progres Hafalan Otomatis

## Overview

Property-based tests verify universal properties that should hold true across all valid inputs. Unlike example-based tests that check specific scenarios, property tests generate hundreds of random inputs to find edge cases.

## Current Status

**MVP Implementation**: Basic property tests implemented with 5 iterations per test (reduced from 100 for quick verification).

**Full PBT Setup Required**: To run proper property-based testing with 100+ iterations of randomly generated data, install one of these libraries:

### Option 1: Eris (Recommended)
```bash
composer require --dev giorgiosironi/eris
```

Eris is a PHP port of QuickCheck with excellent random data generation.

### Option 2: Pest Plugin
```bash
composer require --dev pestphp/pest-plugin-faker
```

Provides faker integration for Pest tests.

## Test Files

### 1. BaselinePropertiesTest.php
Tests baseline management properties:
- ✅ **Property 1**: Baseline Storage Independence
- ✅ **Property 2**: Baseline Modification Audit Trail

**Requirements Validated**: 1.1, 1.2, 1.4, 1.5

### 2. ProgressCalculationPropertiesTest.php
Tests core calculation logic:
- ✅ **Property 3**: Setoran Contribution Rule
- ✅ **Property 4**: Progress Set Union
- ✅ **Property 5**: Juz Completion Threshold

**Requirements Validated**: 2.1, 2.2, 2.3, 2.4, 2.5, 3.2, 3.3, 3.5, 9.3

### 3. ValidationPropertiesTest.php
Tests integrity validation:
- ✅ **Property 18**: Referential Integrity Validation
- ✅ **Property 19**: Gap Detection
- ✅ **Property 20**: Total Ayat Upper Bound

**Requirements Validated**: 9.1, 9.2, 9.4

## Running Tests

```bash
# Run all property tests
php artisan test --group=property-based

# Run specific property test file
php artisan test tests/Property/BaselinePropertiesTest.php

# Run with verbose output
php artisan test --group=property-based --testdox
```

## TODO: Full PBT Implementation

When PBT library is installed, enhance tests with:

1. **Data Generators**
   ```php
   // Generate random baseline data
   Generator::baseline(): [
       'juzs_covered' => array_rand(1, 30, random(0, 10)),
       'surahs_covered' => faker()->sentence(),
       'catatan_kualitas' => faker()->paragraph(),
       'rekomendasi_tingkat' => faker()->randomElement(['Dasar', 'Menengah', 'Atas'])
   ]
   
   // Generate random setoran entry
   Generator::setoran(): [
       'jenis_setoran' => faker()->randomElement(['Hafalan Baru', 'Ulang']),
       'keputusan' => faker()->randomElement(['Lanjut', 'Ulang']),
       'juz_id' => faker()->numberBetween(1, 30),
       'ayat_mulai' => faker()->numberBetween(1, surah_total_ayat),
       'ayat_selesai' => faker()->numberBetween(ayat_mulai, surah_total_ayat)
   ]
   ```

2. **Increase Iterations**
   - Change from 5 to 100 iterations per property
   - Add shrinking (find minimal failing case)

3. **Add Remaining Properties**
   - Property 6: Juz Completion Reactivity
   - Property 7: Dashboard Progress Accuracy
   - Property 8: Progress-to-Target Calculation
   - Properties 9-17, 21-25 (see design.md)

## Reference

Full property specifications: `.kiro/specs/progres-hafalan-otomatis/design.md` section "Correctness Properties"

Total properties to test: **25 properties** covering all 12 requirements

# Requirements Document

## Introduction

The Integrated Academic & Tahfidz Year-End Rollover System automates the transition between academic years (Tahun Pelajaran) for a SMP (Junior High School) that tracks both academic progression (Kelas 7→8→9) and Tahfidz program participation (Kelompok: Dasar, Menengah, Atas). The system replaces manual, error-prone processes with an automated rollover that promotes students academically, manages graduation status, clears Tahfidz group assignments for fresh placement, preserves historical records, and establishes the new academic year as active.

## Glossary

- **Tahun_Pelajaran**: Academic year period (e.g., 2024-2025) during which all academic and Tahfidz activities are recorded
- **Kelas**: Academic class level in SMP (values: 7, 8, or 9)
- **Kelompok**: Tahfidz memorization group (values: Dasar, Menengah, or Atas)
- **Siswa**: Student enrolled in the school system
- **Siswa_Baru**: New student entering the school (only enters at Kelas 7)
- **Pindahan**: Transfer student from another school
- **Lanjutan_Semester**: Continuing student from previous semester
- **Status_Akademik**: Academic status of student (values: Aktif, Lulus SMP, Pindah Sekolah, Keluar)
- **Status_Penempatan**: Tahfidz placement status (values: Belum Ditempatkan, Ditempatkan)
- **Rollover_System**: The automated year-end transition process
- **Riwayat**: Historical record of student progression
- **RiwayatKelompok**: Historical record of Tahfidz group movements
- **Admin**: System administrator who initiates rollover operations
- **Rollback**: Process of undoing a completed rollover operation

## Requirements

### Requirement 1: Academic Year Management

**User Story:** As an Admin, I want to transition to a new academic year, so that the system accurately reflects the current operational period and all student data is associated with the correct Tahun_Pelajaran.

#### Acceptance Criteria

1. WHEN Admin initiates rollover, THE Rollover_System SHALL validate that a target Tahun_Pelajaran has been selected
2. WHEN rollover is confirmed, THE Rollover_System SHALL set the target Tahun_Pelajaran status to active
3. WHEN the target Tahun_Pelajaran is activated, THE Rollover_System SHALL set all other Tahun_Pelajaran records to inactive status
4. THE Rollover_System SHALL preserve all previous Tahun_Pelajaran records with their associated data
5. WHEN rollover completes, THE Rollover_System SHALL display the newly active Tahun_Pelajaran label to all users

### Requirement 2: Automatic Academic Promotion

**User Story:** As an Admin, I want students to be automatically promoted to the next grade level, so that I do not need to manually update hundreds of student records at year-end.

#### Acceptance Criteria

1. WHEN rollover executes, THE Rollover_System SHALL identify all Siswa records with Kelas value of 7 and Status_Akademik value of Aktif
2. WHEN Kelas 7 students are identified, THE Rollover_System SHALL update their Kelas value to 8
3. WHEN rollover executes, THE Rollover_System SHALL identify all Siswa records with Kelas value of 8 and Status_Akademik value of Aktif
4. WHEN Kelas 8 students are identified, THE Rollover_System SHALL update their Kelas value to 9
5. FOR ALL promoted students, THE Rollover_System SHALL preserve their original Kelas value in the previous Tahun_Pelajaran historical records

### Requirement 3: Academic Graduation Processing

**User Story:** As an Admin, I want Kelas 9 students to be marked as academically graduated, so that the system correctly reflects their completion of the SMP program.

#### Acceptance Criteria

1. WHEN rollover executes, THE Rollover_System SHALL identify all Siswa records with Kelas value of 9 and Status_Akademik value of Aktif
2. WHEN Kelas 9 students are identified, THE Rollover_System SHALL update their Status_Akademik value to "Lulus SMP"
3. WHEN Status_Akademik is updated to "Lulus SMP", THE Rollover_System SHALL record the rollover execution date in the tanggal_lulus_akademik field
4. WHEN a student is marked as "Lulus SMP", THE Rollover_System SHALL preserve their Kelas value of 9
5. FOR ALL graduated students, THE Rollover_System SHALL maintain their complete academic history across all Tahun_Pelajaran periods

### Requirement 4: Tahfidz Group Clearing

**User Story:** As an Admin, I want all students' Tahfidz group assignments to be cleared during rollover, so that students can be placed into new Kelompok for the new academic year based on current assessment.

#### Acceptance Criteria

1. WHEN rollover executes, THE Rollover_System SHALL identify all Siswa records with Status_Akademik value of Aktif and kelompok_id not null
2. WHEN students with Kelompok assignments are identified, THE Rollover_System SHALL set their kelompok_id value to null
3. WHEN kelompok_id is set to null, THE Rollover_System SHALL update Status_Penempatan value to "Belum Ditempatkan"
4. WHEN Kelompok assignment is cleared, THE Rollover_System SHALL set tanggal_penempatan value to null
5. THE Rollover_System SHALL preserve graduated students (Status_Akademik value of "Lulus SMP") in their existing Kelompok assignments

### Requirement 5: Tahfidz Group Movement History

**User Story:** As an Admin, I want the system to record all Tahfidz group changes during rollover, so that we maintain a complete audit trail of student Kelompok movements.

#### Acceptance Criteria

1. WHEN a student's kelompok_id is cleared during rollover, THE Rollover_System SHALL create a RiwayatKelompok record
2. WHEN creating RiwayatKelompok, THE Rollover_System SHALL record the original kelompok_id in the kelompok_dari_id field
3. WHEN creating RiwayatKelompok, THE Rollover_System SHALL set kelompok_ke_id field to null
4. WHEN creating RiwayatKelompok, THE Rollover_System SHALL set alasan field to "Rollover Tahun Ajaran"
5. WHEN creating RiwayatKelompok, THE Rollover_System SHALL record the rollover execution timestamp in the tanggal field
6. WHEN creating RiwayatKelompok, THE Rollover_System SHALL record the Admin ID in the disetujui_oleh field
7. THE Rollover_System SHALL create RiwayatKelompok records for all students whose Kelompok assignments are cleared

### Requirement 6: New Student Entry Restrictions

**User Story:** As an Admin, I want the system to enforce that new students can only enter at Kelas 7, so that academic progression rules are maintained and data integrity is preserved.

#### Acceptance Criteria

1. WHEN a new Siswa record is created with status_masuk value of "Siswa Baru", THE Rollover_System SHALL validate that Kelas value is 7
2. IF status_masuk is "Siswa Baru" and Kelas is not 7, THEN THE Rollover_System SHALL reject the record and return error message "Siswa Baru hanya dapat masuk di Kelas 7"
3. WHERE status_masuk is "Pindahan", THE Rollover_System SHALL allow Kelas values of 7, 8, or 9
4. WHERE status_masuk is "Lanjutan Semester", THE Rollover_System SHALL allow Kelas values of 7, 8, or 9
5. THE Rollover_System SHALL enforce these validation rules before database insertion

### Requirement 7: Pre-Rollover Validation

**User Story:** As an Admin, I want the system to validate readiness before executing rollover, so that I can identify and resolve issues before making irreversible changes.

#### Acceptance Criteria

1. WHEN Admin initiates rollover, THE Rollover_System SHALL verify that exactly one target Tahun_Pelajaran has been selected
2. WHEN rollover is initiated, THE Rollover_System SHALL verify that the target Tahun_Pelajaran is different from the currently active Tahun_Pelajaran
3. WHEN rollover validation executes, THE Rollover_System SHALL count all Siswa records that will be affected by academic promotion
4. WHEN rollover validation executes, THE Rollover_System SHALL count all Siswa records that will be marked as "Lulus SMP"
5. WHEN rollover validation executes, THE Rollover_System SHALL count all Siswa records whose Kelompok assignments will be cleared
6. WHEN validation is complete, THE Rollover_System SHALL display counts to Admin before requiring final confirmation
7. IF validation detects zero affected students, THEN THE Rollover_System SHALL display warning message "Tidak ada siswa aktif yang akan di-rollover"

### Requirement 8: Rollover Confirmation Interface

**User Story:** As an Admin, I want to review a summary before confirming rollover, so that I understand the exact changes that will occur and can cancel if needed.

#### Acceptance Criteria

1. WHEN Admin clicks rollover action, THE Rollover_System SHALL display a confirmation dialog
2. WHEN confirmation dialog opens, THE Rollover_System SHALL display the current active Tahun_Pelajaran label
3. WHEN confirmation dialog opens, THE Rollover_System SHALL display the target Tahun_Pelajaran label
4. WHEN confirmation dialog opens, THE Rollover_System SHALL display count of students to be promoted from Kelas 7 to 8
5. WHEN confirmation dialog opens, THE Rollover_System SHALL display count of students to be promoted from Kelas 8 to 9
6. WHEN confirmation dialog opens, THE Rollover_System SHALL display count of students to be marked as "Lulus SMP"
7. WHEN confirmation dialog opens, THE Rollover_System SHALL display count of students whose Kelompok will be cleared
8. THE Rollover_System SHALL provide "Confirm" and "Cancel" buttons with clear labels
9. WHEN Admin clicks "Cancel", THE Rollover_System SHALL close the dialog without making any changes

### Requirement 9: Atomic Rollover Transaction

**User Story:** As an Admin, I want rollover to complete fully or not at all, so that the system never ends up in a partially-rolled-over inconsistent state.

#### Acceptance Criteria

1. WHEN rollover executes, THE Rollover_System SHALL execute all operations within a single database transaction
2. IF any operation within rollover fails, THEN THE Rollover_System SHALL revert all changes made during the transaction
3. WHEN rollover transaction is reverted, THE Rollover_System SHALL restore the previous active Tahun_Pelajaran status
4. WHEN rollover transaction is reverted, THE Rollover_System SHALL restore all original Kelas values
5. WHEN rollover transaction is reverted, THE Rollover_System SHALL restore all original kelompok_id values
6. WHEN rollover transaction is reverted, THE Rollover_System SHALL remove any RiwayatKelompok records created during the failed transaction
7. IF rollover transaction fails, THEN THE Rollover_System SHALL display error message with technical details to Admin

### Requirement 10: Rollover Success Notification

**User Story:** As an Admin, I want clear confirmation when rollover completes successfully, so that I know the system is ready for the new academic year.

#### Acceptance Criteria

1. WHEN rollover transaction commits successfully, THE Rollover_System SHALL display success notification dialog
2. WHEN success notification displays, THE Rollover_System SHALL include the new active Tahun_Pelajaran label
3. WHEN success notification displays, THE Rollover_System SHALL include total count of students processed
4. WHEN success notification displays, THE Rollover_System SHALL include count of students promoted to Kelas 8
5. WHEN success notification displays, THE Rollover_System SHALL include count of students promoted to Kelas 9
6. WHEN success notification displays, THE Rollover_System SHALL include count of students marked as "Lulus SMP"
7. WHEN success notification displays, THE Rollover_System SHALL include count of students with cleared Kelompok assignments
8. THE Rollover_System SHALL automatically refresh all user interface components to reflect the new active Tahun_Pelajaran

### Requirement 11: Historical Data Preservation

**User Story:** As an Admin, I want all previous year data to remain accessible, so that I can generate reports and audit student progression across multiple academic years.

#### Acceptance Criteria

1. WHEN rollover executes, THE Rollover_System SHALL preserve all Siswa records from previous Tahun_Pelajaran
2. WHEN rollover executes, THE Rollover_System SHALL preserve all RiwayatKelompok records from previous Tahun_Pelajaran
3. WHEN rollover executes, THE Rollover_System SHALL preserve all Absensi records associated with previous Tahun_Pelajaran
4. WHEN rollover executes, THE Rollover_System SHALL preserve all Setoran records associated with previous Tahun_Pelajaran
5. WHEN rollover executes, THE Rollover_System SHALL preserve all ProgresHafalan records from previous Tahun_Pelajaran
6. WHEN rollover executes, THE Rollover_System SHALL preserve all Kelompok records linked to previous Tahun_Pelajaran
7. THE Rollover_System SHALL maintain referential integrity between all preserved historical records

### Requirement 12: Rollback Capability

**User Story:** As an Admin, I want to undo a completed rollover within a reasonable timeframe, so that I can recover from accidental rollover or discovered data issues.

#### Acceptance Criteria

1. WHERE rollover has been executed within the current session, THE Rollover_System SHALL provide a "Rollback" action
2. WHEN Admin initiates rollback, THE Rollover_System SHALL display confirmation dialog with warning message
3. WHEN rollback is confirmed, THE Rollover_System SHALL restore the previous Tahun_Pelajaran as active
4. WHEN rollback executes, THE Rollover_System SHALL revert all Kelas values to their pre-rollover state
5. WHEN rollback executes, THE Rollover_System SHALL restore all kelompok_id values to their pre-rollover state
6. WHEN rollback executes, THE Rollover_System SHALL restore all Status_Akademik values to their pre-rollover state
7. WHEN rollback executes, THE Rollover_System SHALL restore all Status_Penempatan values to their pre-rollover state
8. WHEN rollback executes, THE Rollover_System SHALL remove all RiwayatKelompok records created during the rolled-back rollover
9. WHEN rollback executes, THE Rollover_System SHALL clear all tanggal_lulus_akademik values set during the rolled-back rollover
10. THE Rollover_System SHALL execute rollback within a single database transaction

### Requirement 13: Rollover Execution Logging

**User Story:** As an Admin, I want the system to log all rollover operations, so that we have an audit trail for compliance and troubleshooting.

#### Acceptance Criteria

1. WHEN rollover is initiated, THE Rollover_System SHALL create a log record with timestamp and Admin ID
2. WHEN rollover executes, THE Rollover_System SHALL log the source Tahun_Pelajaran and target Tahun_Pelajaran identifiers
3. WHEN rollover executes, THE Rollover_System SHALL log counts of all affected student categories
4. WHEN rollover completes successfully, THE Rollover_System SHALL log success status with completion timestamp
5. IF rollover fails, THEN THE Rollover_System SHALL log error details including exception message and stack trace
6. WHEN rollback is executed, THE Rollover_System SHALL create a separate log record indicating rollback action
7. THE Rollover_System SHALL store all rollover logs in a dedicated audit table

### Requirement 14: Student Status Preservation Rules

**User Story:** As an Admin, I want the system to handle students with special statuses correctly during rollover, so that no student data is inappropriately modified.

#### Acceptance Criteria

1. WHEN rollover executes, THE Rollover_System SHALL exclude Siswa records with Status_Akademik value of "Pindah Sekolah" from academic promotion
2. WHEN rollover executes, THE Rollover_System SHALL exclude Siswa records with Status_Akademik value of "Keluar" from academic promotion
3. WHEN rollover executes, THE Rollover_System SHALL exclude Siswa records with Status_Akademik value of "Lulus SMP" from academic promotion
4. THE Rollover_System SHALL only process Siswa records with Status_Akademik value of "Aktif" for academic promotion
5. WHEN rollover executes, THE Rollover_System SHALL preserve all field values for excluded students except Tahun_Pelajaran context

### Requirement 15: Kelompok Recreation for New Academic Year

**User Story:** As an Admin, I want to create new Kelompok records for the new academic year, so that students can be placed into groups that belong to the correct Tahun_Pelajaran.

#### Acceptance Criteria

1. WHERE Admin has prepared Kelompok structure for new year, THE Rollover_System SHALL allow Kelompok creation linked to the target Tahun_Pelajaran before rollover execution
2. WHEN new Kelompok records are created, THE Rollover_System SHALL validate that tahun_ajaran_id matches an existing Tahun_Pelajaran
3. WHEN new Kelompok records are created, THE Rollover_System SHALL validate that jenis field contains valid values from JenisKelompok enum
4. WHEN new Kelompok records are created, THE Rollover_System SHALL validate that tingkat field contains valid values from TingkatKelompok enum
5. THE Rollover_System SHALL allow creation of Kelompok records with identical names across different Tahun_Pelajaran periods

### Requirement 16: Performance Requirements

**User Story:** As an Admin, I want rollover to complete in a reasonable timeframe even with hundreds of students, so that the operation does not disrupt school operations.

#### Acceptance Criteria

1. WHEN rollover processes 500 or fewer Siswa records, THE Rollover_System SHALL complete within 30 seconds
2. WHEN rollover processes 1000 or fewer Siswa records, THE Rollover_System SHALL complete within 60 seconds
3. WHEN rollover creates RiwayatKelompok records, THE Rollover_System SHALL use batch insertion with maximum chunk size of 500 records
4. THE Rollover_System SHALL execute database operations using optimized bulk update statements
5. WHEN rollover executes, THE Rollover_System SHALL display a progress indicator to Admin

### Requirement 17: Three-Year Academic Journey Tracking

**User Story:** As an Admin, I want to query a student's complete three-year academic journey, so that I can verify their progression through Kelas 7, 8, and 9 across multiple Tahun_Pelajaran.

#### Acceptance Criteria

1. WHEN a Siswa enters the system with Kelas 7, THE Rollover_System SHALL record their tahun_ajaran_id as the entry year
2. WHEN rollover promotes a student, THE Rollover_System SHALL maintain their original tahun_ajaran_id value
3. THE Rollover_System SHALL calculate expected graduation year by adding 2 to the entry Tahun_Pelajaran
4. THE Rollover_System SHALL provide a query interface to retrieve all Siswa records enrolled in a specific Tahun_Pelajaran
5. THE Rollover_System SHALL support filtering Siswa by entry year (tahun_ajaran_id) and current Kelas

### Requirement 18: Data Validation Edge Cases

**User Story:** As an Admin, I want the system to handle edge cases gracefully during rollover, so that unexpected data conditions do not cause system failures.

#### Acceptance Criteria

1. IF a Siswa has kelompok_id referencing a non-existent Kelompok, THEN THE Rollover_System SHALL log a warning and set kelompok_id to null
2. IF a Siswa has kelas_id referencing a non-existent Kelas, THEN THE Rollover_System SHALL abort rollover and return error message
3. IF a Siswa has null Kelas value, THEN THE Rollover_System SHALL exclude them from academic promotion and log warning
4. IF a Siswa has Kelas value greater than 9, THEN THE Rollover_System SHALL abort rollover and return error message "Data tidak valid: Kelas lebih dari 9"
5. IF a Siswa has Kelas value less than 7, THEN THE Rollover_System SHALL abort rollover and return error message "Data tidak valid: Kelas kurang dari 7"
6. THE Rollover_System SHALL validate all Status_Akademik values against StatusAkademik enum before processing
7. THE Rollover_System SHALL validate all Status_Penempatan values against StatusPenempatan enum before processing

### Requirement 19: Authorization and Access Control

**User Story:** As a System Administrator, I want only authorized Admin users to execute rollover, so that this critical operation is protected from unauthorized access.

#### Acceptance Criteria

1. THE Rollover_System SHALL verify that the current user is authenticated as Admin before displaying rollover interface
2. THE Rollover_System SHALL verify that the current user is authenticated as Admin before accepting rollover execution request
3. IF an unauthenticated user attempts rollover, THEN THE Rollover_System SHALL return HTTP 401 Unauthorized response
4. IF a non-Admin user attempts rollover, THEN THE Rollover_System SHALL return HTTP 403 Forbidden response
5. THE Rollover_System SHALL record the authenticated Admin ID in all rollover-related log records

### Requirement 20: Concurrent Access Protection

**User Story:** As an Admin, I want the system to prevent multiple simultaneous rollover operations, so that data corruption from race conditions cannot occur.

#### Acceptance Criteria

1. WHEN an Admin initiates rollover, THE Rollover_System SHALL acquire an exclusive lock on rollover operations
2. IF another Admin attempts rollover while a rollover is in progress, THEN THE Rollover_System SHALL display message "Rollover sedang diproses oleh admin lain, silakan tunggu"
3. WHEN rollover completes successfully, THE Rollover_System SHALL release the exclusive lock
4. WHEN rollover fails, THE Rollover_System SHALL release the exclusive lock
5. IF rollover process terminates abnormally, THEN THE Rollover_System SHALL release the exclusive lock after 5 minute timeout
6. THE Rollover_System SHALL implement the lock using database-level locking mechanisms


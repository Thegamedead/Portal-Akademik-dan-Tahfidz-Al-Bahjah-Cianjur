# Implementation Plan: Integrated Year-End Rollover System

## Overview

This implementation plan creates a comprehensive year-end rollover system that automates academic promotions (Kelas 7→8→9), graduation processing (Kelas 9 → Lulus SMP), Tahfidz group clearing, and historical record keeping. The system uses Laravel's service layer pattern with atomic database transactions and comprehensive audit logging.

## Tasks

- [x] 1. Set up database infrastructure and audit model
  - [x] 1.1 Create RolloverAuditLog migration and model
    - Create migration file for `rollover_audit_logs` table with all fields defined in design
    - Create `RolloverAuditLog` model with fillable fields, casts, relationships, and helper methods
    - Add indexes for performance optimization
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7_
  
  - [x] 1.2 Add new methods to existing models (Siswa, TahunAjaran, Kelas)
    - Add `scopeEligibleForPromotion()` and `scopePendingPlacement()` to Siswa model
    - Add `activate()` and `getActive()` static methods to TahunAjaran model
    - Add `getNextGradeKelas()` helper method to Kelas model
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 1.1, 1.2, 1.3_

- [x] 2. Create exception classes and DTOs
  - [x] 2.1 Create custom exception classes
    - Create `RolloverLockException` for concurrency conflicts
    - Create `RolloverValidationException` for validation failures
    - Add appropriate constructor parameters and error messages
    - _Requirements: 20.2, 7.7, 18.2, 18.4, 18.5_
  
  - [x] 2.2 Create Data Transfer Objects
    - Create `ProgressionResult` DTO with academic progression data
    - Create `TahfidzClearResult` DTO with group clearing data
    - Create `ImpactSummary` DTO for pre-rollover impact calculation
    - Create `RolloverResult` DTO for final execution results
    - _Requirements: 8.4, 8.5, 8.6, 8.7, 10.3, 10.4, 10.5, 10.6, 10.7_

- [x] 3. Implement handler classes
  - [x] 3.1 Create AcademicProgressionHandler
    - Implement `processPromotion()` method for Kelas progression
    - Implement `promoteKelas()` for individual grade level promotions
    - Implement `graduateStudents()` for Kelas 9 graduation processing
    - Implement `canPromote()` validation method
    - Use bulk update queries for performance
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5, 14.1, 14.2, 14.3, 14.4_
  
  - [x] 3.2 Create TahfidzGroupHandler
    - Implement `clearGroups()` method to process all active students
    - Implement `getStudentsForClearing()` to identify eligible students
    - Implement `createRiwayatRecords()` for bulk RiwayatKelompok creation
    - Handle kelompok_id nullification and status_penempatan updates
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_
  
  - [x] 3.3 Create RolloverAuditLogger
    - Implement `createInitialLog()` for rollover start logging
    - Implement `markCompleted()` for successful completion logging
    - Implement `markFailed()` for error logging with stack traces
    - Implement `recordRollback()` for rollback operation tracking
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6_

- [x] 4. Checkpoint - Ensure handler classes are complete
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Create main IntegratedRolloverService
  - [x] 5.1 Implement service constructor and dependencies
    - Set up dependency injection for all handler classes
    - Initialize service with proper type hints
    - _Requirements: 9.1_
  
  - [x] 5.2 Implement validation methods
    - Implement `validatePreConditions()` for pre-execution checks
    - Implement `calculateImpactSummary()` for confirmation dialog data
    - Validate target Tahun Ajaran selection and existence
    - Validate active students exist
    - Validate Kelas data integrity
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7, 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 18.1, 18.2, 18.3, 18.4, 18.5, 18.6, 18.7_
  
  - [x] 5.3 Implement database locking mechanism
    - Implement `acquireLock()` using MySQL GET_LOCK()
    - Implement `releaseLock()` using RELEASE_LOCK()
    - Implement `isRolloverInProgress()` to check lock status
    - Add 300-second timeout for lock acquisition
    - _Requirements: 20.1, 20.2, 20.3, 20.4, 20.5, 20.6_
  
  - [x] 5.4 Implement executeRollover() main workflow
    - Wrap entire operation in DB::transaction()
    - Acquire exclusive lock at transaction start
    - Create initial audit log entry
    - Deactivate all Tahun Ajaran records
    - Activate target Tahun Ajaran
    - Call AcademicProgressionHandler to process promotions
    - Call TahfidzGroupHandler to clear groups
    - Update audit log with completion data
    - Release lock in finally block
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 16.1, 16.2, 16.3, 16.4_
  
  - [x] 5.5 Implement rollback() functionality
    - Validate rollback eligibility (within 24 hours, status checks)
    - Restore previous Tahun Ajaran as active
    - Revert all Kelas values to pre-rollover state
    - Restore kelompok_id and status_penempatan values
    - Restore Status_Akademik values
    - Remove RiwayatKelompok records created during rollover
    - Clear tanggal_lulus_akademik values
    - Execute within single transaction
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 12.7, 12.8, 12.9, 12.10_
  
  - [x] 5.6 Add comprehensive error handling
    - Catch and handle RolloverLockException
    - Catch and handle RolloverValidationException
    - Log all errors with context data
    - Update audit log on failure
    - Ensure lock release in all scenarios
    - Add user-friendly error messages
    - _Requirements: 7.7, 9.7, 13.5_

- [~] 6. Checkpoint - Ensure service layer is complete
  - Ensure all tests pass, ask the user if questions arise.

- [x] 7. Create Livewire component UI
  - [x] 7.1 Create IntegratedRolloverModal Livewire component
    - Set up component properties (isOpen, targetTahunAjaranId, impactSummary, isLoading)
    - Implement `mount()` method
    - Implement `openModal()` and `closeModal()` methods
    - Implement `updatedTargetTahunAjaranId()` to load impact summary
    - Inject IntegratedRolloverService dependency
    - _Requirements: 8.1, 8.8_
  
  - [x] 7.2 Implement confirmRollover() method
    - Add loading state management
    - Call IntegratedRolloverService->executeRollover()
    - Handle success with dispatch dialog-open event
    - Handle errors with appropriate error messages
    - Refresh UI components after success
    - _Requirements: 8.9, 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8_
  
  - [x] 7.3 Implement executeRollback() method
    - Get most recent rollover log from audit table
    - Validate rollback eligibility
    - Show confirmation dialog with warning
    - Call IntegratedRolloverService->rollback()
    - Handle success and error scenarios
    - _Requirements: 12.1, 12.2_
  
  - [x] 7.4 Create Blade view template
    - Create modal dialog structure with Livewire Alpine.js integration
    - Add Tahun Ajaran dropdown selector
    - Display impact summary table (Kelas 7→8, 8→9, Lulus, Kelompok cleared counts)
    - Add confirmation buttons (Confirm/Cancel)
    - Add loading spinner overlay
    - Add rollback button if recent rollover exists
    - _Requirements: 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 8.8, 8.9, 16.5_

- [x] 8. Create Artisan command for emergency lock release
  - [x] 8.1 Create ReleaseRolloverLock Artisan command
    - Create command class with signature `rollover:release-lock`
    - Implement lock release using RELEASE_LOCK()
    - Add confirmation prompt for safety
    - Add logging of manual lock releases
    - _Requirements: 20.5_

- [ ] 9. Integration and wiring
  - [x] 9.1 Register routes and middleware
    - Register Livewire component route in web.php
    - Apply admin authentication middleware
    - Apply authorization checks for admin-only access
    - _Requirements: 19.1, 19.2, 19.3, 19.4, 19.5_
  
  - [x] 9.2 Update admin dashboard to include rollover button
    - Add rollover button to admin dashboard blade view
    - Wire button to open IntegratedRolloverModal
    - Add appropriate icon and styling
    - _Requirements: 8.1_
  
  - [ ] 9.3 Run migrations and seed test data
    - Execute rollover_audit_logs migration
    - Verify foreign key constraints work correctly
    - Test with sample Tahun Ajaran data
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [~] 10. Final checkpoint - End-to-end testing
  - Test complete rollover workflow with sample data
  - Verify atomic transaction behavior (all-or-nothing)
  - Test rollback functionality
  - Verify audit logging captures all operations
  - Test concurrent access protection
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- All database operations use Laravel's Eloquent ORM and query builder
- Transaction management uses Laravel's DB::transaction() with automatic rollback on exceptions
- Bulk updates use chunk processing for large datasets (500 records per chunk)
- Concurrency control uses MySQL's GET_LOCK() advisory locking mechanism
- All timestamps use Laravel's Carbon for consistent datetime handling
- Error messages are in Indonesian (Bahasa Indonesia) to match existing codebase conventions
- The design explicitly states this is NOT suitable for property-based testing, so no PBT tasks are included
- Authorization is enforced at middleware level using existing admin authentication system
- Existing RolloverModal.php will be deprecated and replaced by IntegratedRolloverModal

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "2.1", "2.2"] },
    { "id": 1, "tasks": ["1.2", "3.1", "3.2", "3.3"] },
    { "id": 2, "tasks": ["5.1", "5.2", "5.3"] },
    { "id": 3, "tasks": ["5.4", "5.5"] },
    { "id": 4, "tasks": ["5.6", "7.1", "8.1"] },
    { "id": 5, "tasks": ["7.2", "7.3"] },
    { "id": 6, "tasks": ["7.4", "9.1"] },
    { "id": 7, "tasks": ["9.2", "9.3"] }
  ]
}
```

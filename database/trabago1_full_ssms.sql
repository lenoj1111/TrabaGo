-- ============================================================================
-- TrabaGo Full Database Schema & Seed Script for Microsoft SQL Server (SSMS)
-- Database Name: Trabago1
-- Target: SQL Server Management Studio (SSMS) 2017 / 2019 / 2022 / Azure SQL
-- ============================================================================

-- Step 1: Create Database if it does not exist
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = N'Trabago1')
BEGIN
    CREATE DATABASE [Trabago1];
END
GO

USE [Trabago1];
GO

-- ============================================================================
-- DROP EXISTING TABLES (Reverse Dependency Order for clean rerun)
-- ============================================================================
IF OBJECT_ID(N'dbo.sessions', 'U') IS NOT NULL DROP TABLE dbo.sessions;
IF OBJECT_ID(N'dbo.personal_access_tokens', 'U') IS NOT NULL DROP TABLE dbo.personal_access_tokens;
IF OBJECT_ID(N'dbo.audit_logs', 'U') IS NOT NULL DROP TABLE dbo.audit_logs;
IF OBJECT_ID(N'dbo.posting_restrictions', 'U') IS NOT NULL DROP TABLE dbo.posting_restrictions;
IF OBJECT_ID(N'dbo.placement_reports', 'U') IS NOT NULL DROP TABLE dbo.placement_reports;
IF OBJECT_ID(N'dbo.notifications', 'U') IS NOT NULL DROP TABLE dbo.notifications;
IF OBJECT_ID(N'dbo.training_enrollments', 'U') IS NOT NULL DROP TABLE dbo.training_enrollments;
IF OBJECT_ID(N'dbo.training_assessments', 'U') IS NOT NULL DROP TABLE dbo.training_assessments;
IF OBJECT_ID(N'dbo.training_topics', 'U') IS NOT NULL DROP TABLE dbo.training_topics;
IF OBJECT_ID(N'dbo.training_programs', 'U') IS NOT NULL DROP TABLE dbo.training_programs;
IF OBJECT_ID(N'dbo.jpo_assessments', 'U') IS NOT NULL DROP TABLE dbo.jpo_assessments;
IF OBJECT_ID(N'dbo.job_applications', 'U') IS NOT NULL DROP TABLE dbo.job_applications;
IF OBJECT_ID(N'dbo.job_postings', 'U') IS NOT NULL DROP TABLE dbo.job_postings;
IF OBJECT_ID(N'dbo.employer_accreditation', 'U') IS NOT NULL DROP TABLE dbo.employer_accreditation;
IF OBJECT_ID(N'dbo.employers', 'U') IS NOT NULL DROP TABLE dbo.employers;
IF OBJECT_ID(N'dbo.social_status', 'U') IS NOT NULL DROP TABLE dbo.social_status;
IF OBJECT_ID(N'dbo.job_preferences', 'U') IS NOT NULL DROP TABLE dbo.job_preferences;
IF OBJECT_ID(N'dbo.jobseeker_skills', 'U') IS NOT NULL DROP TABLE dbo.jobseeker_skills;
IF OBJECT_ID(N'dbo.jobseeker_details', 'U') IS NOT NULL DROP TABLE dbo.jobseeker_details;
IF OBJECT_ID(N'dbo.jobseekers', 'U') IS NOT NULL DROP TABLE dbo.jobseekers;
IF OBJECT_ID(N'dbo.user_profiles', 'U') IS NOT NULL DROP TABLE dbo.user_profiles;
IF OBJECT_ID(N'dbo.users', 'U') IS NOT NULL DROP TABLE dbo.users;
IF OBJECT_ID(N'dbo.migrations', 'U') IS NOT NULL DROP TABLE dbo.migrations;
GO

-- ============================================================================
-- 1. USERS TABLE
-- ============================================================================
CREATE TABLE dbo.users (
    user_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    email NVARCHAR(150) NOT NULL UNIQUE,
    email_verified_at DATETIME2 NULL,
    password NVARCHAR(255) NOT NULL,
    role NVARCHAR(50) NOT NULL,
    status NVARCHAR(50) NOT NULL DEFAULT 'active',
    is_approved BIT NOT NULL DEFAULT 0,
    remember_token NVARCHAR(100) NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT CK_users_role CHECK ([role] IN ('admin', 'supervisor', 'pesd_supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker')),
    CONSTRAINT CK_users_status CHECK ([status] IN ('active', 'inactive'))
);
GO

-- ============================================================================
-- 2. USER PROFILES TABLE (Admin, JPO, Trainer, Supervisor)
-- ============================================================================
CREATE TABLE dbo.user_profiles (
    profile_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    full_name NVARCHAR(150) NULL,
    phone NVARCHAR(50) NULL,
    position NVARCHAR(100) NULL,
    department NVARCHAR(150) NULL,
    office NVARCHAR(150) NULL,
    specialization NVARCHAR(150) NULL,
    area NVARCHAR(150) NULL,
    bio NVARCHAR(MAX) NULL,
    trainer_type NVARCHAR(50) NOT NULL DEFAULT 'dmdp',
    partner_institution NVARCHAR(255) NULL,
    is_trainer_approved BIT NOT NULL DEFAULT 0,
    trainer_approved_by BIGINT NULL,
    trainer_approved_at DATE NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_user_profiles_users FOREIGN KEY (user_id) REFERENCES dbo.users(user_id) ON DELETE CASCADE,
    CONSTRAINT FK_user_profiles_trainer_approved_by FOREIGN KEY (trainer_approved_by) REFERENCES dbo.users(user_id),
    CONSTRAINT CK_user_profiles_trainer_type CHECK ([trainer_type] IN ('dmdp', 'partner'))
);
GO

-- ============================================================================
-- 3. JOBSEEKERS TABLE
-- ============================================================================
CREATE TABLE dbo.jobseekers (
    jobseeker_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    first_name NVARCHAR(100) NULL,
    last_name NVARCHAR(100) NULL,
    middle_name NVARCHAR(100) NULL,
    birth_date DATE NULL,
    sex NVARCHAR(20) NULL,
    civil_status NVARCHAR(50) NULL,
    citizenship NVARCHAR(100) NULL,
    mobile_number NVARCHAR(50) NULL,
    email NVARCHAR(150) NULL,
    employment_status NVARCHAR(50) NULL,
    hired_company NVARCHAR(150) NULL,
    CONSTRAINT FK_jobseekers_users FOREIGN KEY (user_id) REFERENCES dbo.users(user_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 4. JOBSEEKER DETAILS TABLE (Address, Education, Vault Documents JSON)
-- ============================================================================
CREATE TABLE dbo.jobseeker_details (
    detail_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    jobseeker_id BIGINT NOT NULL UNIQUE,
    address NVARCHAR(MAX) NULL,
    education NVARCHAR(MAX) NULL,
    work_experience NVARCHAR(MAX) NULL,
    eligibility NVARCHAR(MAX) NULL,
    language_proficiency NVARCHAR(MAX) NULL,
    training_certificates NVARCHAR(MAX) NULL,
    CONSTRAINT FK_jobseeker_details_jobseekers FOREIGN KEY (jobseeker_id) REFERENCES dbo.jobseekers(jobseeker_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 5. JOBSEEKER SKILLS TABLE (Skills Matrix for Cosine AI Matching)
-- ============================================================================
CREATE TABLE dbo.jobseeker_skills (
    skill_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    jobseeker_id BIGINT NOT NULL,
    skill_name NVARCHAR(100) NOT NULL,
    skill_type NVARCHAR(50) NOT NULL,
    CONSTRAINT FK_jobseeker_skills_jobseekers FOREIGN KEY (jobseeker_id) REFERENCES dbo.jobseekers(jobseeker_id) ON DELETE CASCADE,
    CONSTRAINT CK_jobseeker_skills_skill_type CHECK ([skill_type] IN ('technical', '21st_century', 'technical_informal', 'soft_skill', 'vocational'))
);
GO

-- ============================================================================
-- 6. JOB PREFERENCES TABLE
-- ============================================================================
CREATE TABLE dbo.job_preferences (
    preference_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    jobseeker_id BIGINT NOT NULL UNIQUE,
    occupation1 NVARCHAR(150) NULL,
    occupation2 NVARCHAR(150) NULL,
    occupation3 NVARCHAR(150) NULL,
    industry1 NVARCHAR(150) NULL,
    industry2 NVARCHAR(150) NULL,
    industry3 NVARCHAR(150) NULL,
    preferred_location NVARCHAR(255) NULL,
    salary_expectation NVARCHAR(100) NULL,
    CONSTRAINT FK_job_preferences_jobseekers FOREIGN KEY (jobseeker_id) REFERENCES dbo.jobseekers(jobseeker_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 7. SOCIAL STATUS TABLE (PWD, 4Ps, OFW)
-- ============================================================================
CREATE TABLE dbo.social_status (
    status_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    jobseeker_id BIGINT NOT NULL UNIQUE,
    is_4ps BIT NULL,
    household_id NVARCHAR(100) NULL,
    is_ofw BIT NULL,
    is_pwd BIT NULL,
    pwd_type NVARCHAR(255) NULL,
    CONSTRAINT FK_social_status_jobseekers FOREIGN KEY (jobseeker_id) REFERENCES dbo.jobseekers(jobseeker_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 8. EMPLOYERS TABLE
-- ============================================================================
CREATE TABLE dbo.employers (
    employer_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    user_id BIGINT NOT NULL UNIQUE,
    company_name NVARCHAR(150) NOT NULL,
    is_accredited BIT NOT NULL DEFAULT 0,
    accredited_at DATE NULL,
    CONSTRAINT FK_employers_users FOREIGN KEY (user_id) REFERENCES dbo.users(user_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 9. EMPLOYER ACCREDITATION TABLE (Workflow & Document Verification)
-- ============================================================================
CREATE TABLE dbo.employer_accreditation (
    accreditation_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    employer_id BIGINT NOT NULL UNIQUE,
    documents NVARCHAR(MAX) NULL,
    ocr_raw_text NVARCHAR(MAX) NULL,
    ocr_classified_document_type NVARCHAR(100) NULL,
    ocr_extracted_fields NVARCHAR(MAX) NULL,
    ocr_confidence_score DECIMAL(5,2) NULL,
    ocr_validation_status NVARCHAR(50) NOT NULL DEFAULT 'pending',
    auto_approved_at DATETIME2 NULL,
    status NVARCHAR(50) NOT NULL DEFAULT 'submitted_to_jpo',
    document_status NVARCHAR(50) NOT NULL DEFAULT 'pending',
    document_incomplete_reason NVARCHAR(MAX) NULL,
    document_verified_at DATETIME2 NULL,
    document_verified_by BIGINT NULL,
    jpo_id BIGINT NULL,
    jpo_reviewed BIT NOT NULL DEFAULT 0,
    jpo_reviewed_at DATETIME2 NULL,
    jpo_remarks NVARCHAR(MAX) NULL,
    supervisor_id BIGINT NULL,
    supervisor_approved BIT NOT NULL DEFAULT 0,
    supervisor_approved_at DATETIME2 NULL,
    supervisor_remarks NVARCHAR(MAX) NULL,
    admin_approved BIT NOT NULL DEFAULT 0,
    admin_approved_at DATE NULL,
    submitted_at DATE NULL,
    approved_at DATE NULL,
    CONSTRAINT FK_employer_accreditation_employers FOREIGN KEY (employer_id) REFERENCES dbo.employers(employer_id) ON DELETE CASCADE,
    CONSTRAINT CK_employer_accreditation_ocr_status CHECK ([ocr_validation_status] IN ('pending', 'auto_approved', 'manual_review', 'rejected')),
    CONSTRAINT CK_employer_accreditation_status CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'admin_approved', 'rejected', 'auto_approved', 'manual_review'))
);
GO

-- ============================================================================
-- 10. JOB POSTINGS TABLE
-- ============================================================================
CREATE TABLE dbo.job_postings (
    job_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    employer_id BIGINT NULL,
    admin_id BIGINT NULL,
    title NVARCHAR(150) NOT NULL,
    description NVARCHAR(MAX) NULL,
    qualifications NVARCHAR(MAX) NULL,
    vacancy_count INT NOT NULL DEFAULT 1,
    valid_until DATE NULL,
    accepts_disability BIT NULL,
    disability_type NVARCHAR(100) NULL,
    status NVARCHAR(50) NOT NULL DEFAULT 'pending',
    created_by NVARCHAR(50) NOT NULL DEFAULT 'employer',
    created_at DATE NULL,
    approved_at DATE NULL,
    CONSTRAINT FK_job_postings_employers FOREIGN KEY (employer_id) REFERENCES dbo.employers(employer_id),
    CONSTRAINT FK_job_postings_admin FOREIGN KEY (admin_id) REFERENCES dbo.user_profiles(profile_id),
    CONSTRAINT CK_job_postings_status CHECK ([status] IN ('pending', 'approved', 'rejected', 'closed')),
    CONSTRAINT CK_job_postings_created_by CHECK ([created_by] IN ('employer', 'admin'))
);
GO

-- ============================================================================
-- 11. JOB APPLICATIONS TABLE (Workflow, Interviews, Offers, Resignation)
-- ============================================================================
CREATE TABLE dbo.job_applications (
    application_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    job_id BIGINT NOT NULL,
    jobseeker_id BIGINT NOT NULL,
    status NVARCHAR(50) NOT NULL DEFAULT 'pending',
    referred_by_jpo BIT NOT NULL DEFAULT 0,
    jpo_id BIGINT NULL,
    jpo_notes NVARCHAR(MAX) NULL,
    jpo_evaluated_at DATETIME2 NULL,
    interview_schedule DATETIME2 NULL,
    interview_mode NVARCHAR(50) NULL,
    interview_location NVARCHAR(255) NULL,
    interview_status NVARCHAR(50) NULL,
    jobseeker_response NVARCHAR(50) NULL,
    hired_date DATE NULL,
    offered_at DATETIME2 NULL,
    offer_salary DECIMAL(10,2) NULL,
    offer_start_date DATE NULL,
    offer_notes NVARCHAR(MAX) NULL,
    declined_at DATETIME2 NULL,
    decline_reason NVARCHAR(MAX) NULL,
    resignation_status NVARCHAR(50) NULL,
    resignation_reason NVARCHAR(MAX) NULL,
    resignation_requested_at DATETIME2 NULL,
    resignation_approved_at DATETIME2 NULL,
    resignation_remarks NVARCHAR(MAX) NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_job_applications_job FOREIGN KEY (job_id) REFERENCES dbo.job_postings(job_id),
    CONSTRAINT FK_job_applications_jobseeker FOREIGN KEY (jobseeker_id) REFERENCES dbo.jobseekers(jobseeker_id),
    CONSTRAINT CK_job_applications_status CHECK ([status] IN ('pending', 'reviewed', 'interview', 'offered', 'hired', 'declined', 'rejected', 'withdrawn', 'cancelled')),
    CONSTRAINT CK_job_applications_interview_mode CHECK ([interview_mode] IS NULL OR [interview_mode] IN ('online', 'onsite')),
    CONSTRAINT CK_job_applications_interview_status CHECK ([interview_status] IS NULL OR [interview_status] IN ('scheduled', 'completed', 'cancelled')),
    CONSTRAINT CK_job_applications_jobseeker_response CHECK ([jobseeker_response] IS NULL OR [jobseeker_response] IN ('pending', 'confirmed', 'declined'))
);
GO

-- ============================================================================
-- 12. JPO ASSESSMENTS TABLE
-- ============================================================================
CREATE TABLE dbo.jpo_assessments (
    assessment_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    application_id BIGINT NOT NULL UNIQUE,
    jpo_id BIGINT NOT NULL,
    recommendation NVARCHAR(50) NOT NULL,
    remarks NVARCHAR(MAX) NULL,
    referral_date DATE NULL,
    referral_notes NVARCHAR(MAX) NULL,
    CONSTRAINT FK_jpo_assessments_application FOREIGN KEY (application_id) REFERENCES dbo.job_applications(application_id),
    CONSTRAINT FK_jpo_assessments_jpo FOREIGN KEY (jpo_id) REFERENCES dbo.user_profiles(profile_id),
    CONSTRAINT CK_jpo_assessments_recommendation CHECK ([recommendation] IN ('refer', 'training'))
);
GO

-- ============================================================================
-- 13. TRAINING PROGRAMS TABLE
-- ============================================================================
CREATE TABLE dbo.training_programs (
    training_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    trainer_id BIGINT NOT NULL,
    title NVARCHAR(150) NOT NULL,
    training_type NVARCHAR(50) NOT NULL DEFAULT 'online',
    duration_months INT NULL,
    description NVARCHAR(MAX) NULL,
    auto_generate_certificate BIT NOT NULL DEFAULT 1,
    skills NVARCHAR(MAX) NULL,
    passing_score INT NOT NULL DEFAULT 80,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_training_programs_trainer FOREIGN KEY (trainer_id) REFERENCES dbo.user_profiles(profile_id),
    CONSTRAINT CK_training_programs_type CHECK ([training_type] IN ('online', 'laboratory_onsite'))
);
GO

-- ============================================================================
-- 14. TRAINING TOPICS TABLE
-- ============================================================================
CREATE TABLE dbo.training_topics (
    topic_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    training_id BIGINT NOT NULL,
    title NVARCHAR(150) NOT NULL,
    video_url NVARCHAR(MAX) NULL,
    topic_order INT NOT NULL DEFAULT 0,
    questions NVARCHAR(MAX) NULL,
    CONSTRAINT FK_training_topics_training FOREIGN KEY (training_id) REFERENCES dbo.training_programs(training_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 15. TRAINING ASSESSMENTS TABLE (Interactive Course Quizzes)
-- ============================================================================
CREATE TABLE dbo.training_assessments (
    assessment_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    training_id BIGINT NOT NULL,
    question NVARCHAR(MAX) NOT NULL,
    question_type NVARCHAR(50) NOT NULL DEFAULT 'multiple_choice',
    options NVARCHAR(MAX) NOT NULL,
    correct_answer INT NOT NULL DEFAULT 0,
    explanation NVARCHAR(MAX) NULL,
    points INT NOT NULL DEFAULT 1,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_training_assessments_training FOREIGN KEY (training_id) REFERENCES dbo.training_programs(training_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 16. TRAINING ENROLLMENTS TABLE (Scores, Progress, Certificates)
-- ============================================================================
CREATE TABLE dbo.training_enrollments (
    enrollment_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    jobseeker_id BIGINT NOT NULL,
    training_id BIGINT NOT NULL,
    training_type NVARCHAR(50) NOT NULL,
    status NVARCHAR(50) NOT NULL DEFAULT 'enrolled',
    current_topic INT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    lab_remarks NVARCHAR(MAX) NULL,
    answers NVARCHAR(MAX) NULL,
    score DECIMAL(5,2) NULL,
    passed BIT NULL DEFAULT 0,
    certificate_no NVARCHAR(100) NULL,
    certificate_issued BIT NULL DEFAULT 0,
    certificate_issued_at DATETIME2 NULL,
    trainer_feedback NVARCHAR(MAX) NULL,
    enrolled_skills NVARCHAR(MAX) NULL,
    CONSTRAINT FK_training_enrollments_jobseeker FOREIGN KEY (jobseeker_id) REFERENCES dbo.jobseekers(jobseeker_id),
    CONSTRAINT FK_training_enrollments_training FOREIGN KEY (training_id) REFERENCES dbo.training_programs(training_id),
    CONSTRAINT CK_training_enrollments_status CHECK ([status] IN ('enrolled', 'in_progress', 'completed', 'failed', 'cancelled')),
    CONSTRAINT CK_training_enrollments_training_type CHECK ([training_type] IN ('online', 'laboratory_onsite'))
);
GO

-- ============================================================================
-- 17. NOTIFICATIONS TABLE
-- ============================================================================
CREATE TABLE dbo.notifications (
    notification_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    title NVARCHAR(150) NOT NULL,
    message NVARCHAR(MAX) NOT NULL,
    type NVARCHAR(50) NOT NULL,
    is_read BIT NOT NULL DEFAULT 0,
    related_id BIGINT NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_notifications_users FOREIGN KEY (user_id) REFERENCES dbo.users(user_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 18. PLACEMENT REPORTS TABLE (DOLE/DMDP Employment Monitoring)
-- ============================================================================
CREATE TABLE dbo.placement_reports (
    report_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    jpo_id BIGINT NULL,
    employer_id BIGINT NOT NULL,
    report_type NVARCHAR(50) NOT NULL,
    report_month DATE NOT NULL,
    report_data NVARCHAR(MAX) NOT NULL,
    status NVARCHAR(50) NOT NULL DEFAULT 'submitted_to_jpo',
    jpo_evaluated BIT NOT NULL DEFAULT 0,
    jpo_evaluated_at DATETIME2 NULL,
    jpo_remarks NVARCHAR(MAX) NULL,
    admin_remarks NVARCHAR(MAX) NULL,
    approved_by BIGINT NULL,
    approved_at DATE NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_placement_reports_jpo FOREIGN KEY (jpo_id) REFERENCES dbo.user_profiles(profile_id),
    CONSTRAINT FK_placement_reports_employer FOREIGN KEY (employer_id) REFERENCES dbo.employers(employer_id),
    CONSTRAINT CK_placement_reports_type CHECK ([report_type] IN ('dmdp', 'employer_monthly')),
    CONSTRAINT CK_placement_reports_status CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_evaluated', 'approved', 'rejected'))
);
GO

-- ============================================================================
-- 19. POSTING RESTRICTIONS TABLE
-- ============================================================================
CREATE TABLE dbo.posting_restrictions (
    restriction_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    employer_id BIGINT NOT NULL,
    restriction_start_date DATE NOT NULL,
    restriction_end_date DATE NOT NULL,
    reason NVARCHAR(MAX) NULL,
    CONSTRAINT FK_posting_restrictions_employer FOREIGN KEY (employer_id) REFERENCES dbo.employers(employer_id)
);
GO

-- ============================================================================
-- 20. AUDIT LOGS TABLE
-- ============================================================================
CREATE TABLE dbo.audit_logs (
    log_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    action NVARCHAR(MAX) NOT NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_audit_logs_users FOREIGN KEY (user_id) REFERENCES dbo.users(user_id) ON DELETE CASCADE
);
GO

-- ============================================================================
-- 21. SESSIONS TABLE (Laravel Web Sessions)
-- ============================================================================
CREATE TABLE dbo.sessions (
    id NVARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT NULL,
    ip_address NVARCHAR(45) NULL,
    user_agent NVARCHAR(MAX) NULL,
    payload NVARCHAR(MAX) NOT NULL,
    last_activity INT NOT NULL
);
CREATE INDEX IX_sessions_user_id ON dbo.sessions(user_id);
CREATE INDEX IX_sessions_last_activity ON dbo.sessions(last_activity);
GO

-- ============================================================================
-- 22. PERSONAL ACCESS TOKENS TABLE (Sanctum Tokens for Mobile App)
-- ============================================================================
CREATE TABLE dbo.personal_access_tokens (
    id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    tokenable_type NVARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name NVARCHAR(MAX) NOT NULL,
    token NVARCHAR(64) NOT NULL UNIQUE,
    abilities NVARCHAR(MAX) NULL,
    last_used_at DATETIME2 NULL,
    expires_at DATETIME2 NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL
);
CREATE INDEX IX_personal_access_tokens_tokenable ON dbo.personal_access_tokens(tokenable_type, tokenable_id);
CREATE INDEX IX_personal_access_tokens_expires_at ON dbo.personal_access_tokens(expires_at);
GO

-- ============================================================================
-- 23. LARAVEL MIGRATIONS TABLE (Marks all migrations as completed)
-- ============================================================================
CREATE TABLE dbo.migrations (
    id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    migration NVARCHAR(255) NOT NULL,
    batch INT NOT NULL
);
GO

INSERT INTO dbo.migrations (migration, batch) VALUES
('2026_08_12_162145_create_sessions_table', 1),
('2026_08_20_085206_create_complete_tables', 1),
('2026_08_22_181511_add_timestamps_to_job_applications_table', 1),
('2026_08_23_000001_enhance_workflow_tables', 1),
('2026_08_23_090121_create_personal_access_tokens_table', 1),
('2026_08_23_095000_ensure_training_enrollments_columns', 1),
('2026_08_23_100000_update_user_role_check_constraint', 1),
('2026_08_23_110000_update_all_workflow_check_constraints', 1),
('2026_08_24_154500_fix_notifications_type_check_constraint', 1),
('2026_09_05_190000_enhance_trainer_system', 1),
('2026_09_05_210000_add_document_status_to_employer_accreditation', 1),
('2026_09_05_220000_add_timestamps_to_placement_reports', 1),
('2026_09_05_230000_add_hired_company_to_jobseekers', 1),
('2026_09_05_240000_add_resignation_columns_to_job_applications', 1),
('2026_09_05_250000_add_offer_acceptance_to_job_applications', 1);
GO

-- ============================================================================
-- INITIAL SEED DATA
-- Passwords below are hashed for 'password123' using bcrypt ($2y$12$)
-- ============================================================================

-- 1. Insert USERS (Password: password123)
-- Real Laravel bcrypt hash for 'password123':
-- $2y$12$UOJiYAM4mEw8mrxE0B47TetoWfLDovZFaiWpA3QRoSzQ5Yf86llrS

-- 1. Admin
INSERT INTO dbo.users (email, password, role, status, is_approved, created_at, updated_at)
VALUES ('admin@trabago.com', '$2y$12$UOJiYAM4mEw8mrxE0B47TetoWfLDovZFaiWpA3QRoSzQ5Yf86llrS', 'admin', 'active', 1, SYSDATETIME(), SYSDATETIME());
DECLARE @AdminId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.user_profiles (user_id, full_name, position, department, office, created_at, updated_at)
VALUES (@AdminId, 'System Administrator', 'Administrator', 'IT & Systems', 'Main Office', SYSDATETIME(), SYSDATETIME());

INSERT INTO dbo.notifications (user_id, title, message, type, is_read, created_at, updated_at)
VALUES (@AdminId, 'System Initialized', 'TrabaGo platform initialized. All systems and core modules operating normally.', 'approval', 0, SYSDATETIME(), SYSDATETIME());

-- 2. Trainer
INSERT INTO dbo.users (email, password, role, status, is_approved, created_at, updated_at)
VALUES ('trainer@trabago.com', '$2y$12$UOJiYAM4mEw8mrxE0B47TetoWfLDovZFaiWpA3QRoSzQ5Yf86llrS', 'trainer', 'active', 1, SYSDATETIME(), SYSDATETIME());
DECLARE @TrainerUserId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.user_profiles (user_id, full_name, position, department, office, specialization, trainer_type, is_trainer_approved, created_at, updated_at)
VALUES (@TrainerUserId, 'Prof. Maria Santos', 'Senior Vocational Trainer', 'DMDP Skills Training Division', 'Cebu City DMDP Center', 'Vocational & Digital Skills', 'dmdp', 1, SYSDATETIME(), SYSDATETIME());
DECLARE @TrainerProfileId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.training_programs (trainer_id, title, training_type, duration_months, description, auto_generate_certificate, passing_score, created_at, updated_at)
VALUES (@TrainerProfileId, 'Workplace Readiness & Soft Skills', 'online', 1, 'Master foundational workplace ethics, professional communication, and interview skills to excel in any industry.', 1, 80, SYSDATETIME(), SYSDATETIME());
DECLARE @TrainingId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.training_topics (training_id, title, video_url, topic_order, questions)
VALUES (@TrainingId, 'Effective Communication in the Workplace', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1, 
'[{"question":"What is the most effective approach when communicating with a team member?","options":["Active listening and clarity","Speaking loudly","Ignoring feedback","Using technical jargon only"],"answer":0}]');

INSERT INTO dbo.training_assessments (training_id, question, question_type, options, correct_answer, explanation, points, created_at, updated_at)
VALUES 
(@TrainingId, 'What is the primary component of active listening in a professional setting?', 'multiple_choice', '["Waiting for your turn to speak","Paying full attention and seeking clarification","Interrupting to correct mistakes","Nodding without understanding"]', 1, 'Active listening involves giving full attention and clarifying points for mutual understanding.', 1, SYSDATETIME(), SYSDATETIME()),
(@TrainingId, 'Which communication channel is best for critical contractual agreements?', 'multiple_choice', '["Casual chat or SMS","Formal written email or signed memo","Water cooler conversation","Social media group post"]', 1, 'Formal written communication provides a verifiable record of official agreements.', 1, SYSDATETIME(), SYSDATETIME()),
(@TrainingId, 'How should constructive feedback be delivered to a colleague?', 'multiple_choice', '["Publicly in front of team members","Privately, respectfully, and focusing on behavior","Through anonymous complaints","Ignored until the annual review"]', 1, 'Constructive feedback is most effective when given privately and focused objectively on actions.', 1, SYSDATETIME(), SYSDATETIME()),
(@TrainingId, 'What does the STAR interview technique stand for?', 'multiple_choice', '["Skill, Task, Attitude, Result","Situation, Task, Action, Result","Strategy, Time, Action, Review","Start, Talk, Answer, Repeat"]', 1, 'STAR stands for Situation, Task, Action, and Result.', 1, SYSDATETIME(), SYSDATETIME()),
(@TrainingId, 'What demonstrates high professional workplace ethics?', 'multiple_choice', '["Punctuality, accountability, and integrity","Leaving early when tasks are incomplete","Blaming others for personal errors","Sharing confidential company data"]', 0, 'Punctuality, accountability, and ethical integrity are pillars of professionalism.', 1, SYSDATETIME(), SYSDATETIME());

INSERT INTO dbo.notifications (user_id, title, message, type, is_read, created_at, updated_at)
VALUES (@TrainerUserId, 'Trainer Portal Active', 'Your vocational training program has been published and is accepting enrollments.', 'approval', 0, SYSDATETIME(), SYSDATETIME());

-- 3. JPO (Job Placement Officer)
INSERT INTO dbo.users (email, password, role, status, is_approved, created_at, updated_at)
VALUES ('jpo@trabago.com', '$2y$12$UOJiYAM4mEw8mrxE0B47TetoWfLDovZFaiWpA3QRoSzQ5Yf86llrS', 'jpo', 'active', 1, SYSDATETIME(), SYSDATETIME());
DECLARE @JpoUserId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.user_profiles (user_id, full_name, position, department, office, created_at, updated_at)
VALUES (@JpoUserId, 'Atty. Roberto Tan', 'Job Placement Officer', 'Employment Facilitation Unit', 'PESD Cebu', SYSDATETIME(), SYSDATETIME());
DECLARE @JpoProfileId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.notifications (user_id, title, message, type, is_read, created_at, updated_at)
VALUES (@JpoUserId, 'JPO Evaluation Desk Ready', 'Your placement officer workspace is ready to review candidate referrals and accreditations.', 'approval', 0, SYSDATETIME(), SYSDATETIME());

-- 4. Employer (TechCorp Solutions Inc.)
INSERT INTO dbo.users (email, password, role, status, is_approved, created_at, updated_at)
VALUES ('employer@techcorp.com', '$2y$12$UOJiYAM4mEw8mrxE0B47TetoWfLDovZFaiWpA3QRoSzQ5Yf86llrS', 'employer', 'active', 1, SYSDATETIME(), SYSDATETIME());
DECLARE @EmployerUserId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.employers (user_id, company_name, is_accredited, accredited_at)
VALUES (@EmployerUserId, 'TechCorp Solutions Inc.', 1, CAST(GETDATE() AS DATE));
DECLARE @EmployerId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.employer_accreditation (
    employer_id, documents, ocr_classified_document_type, ocr_validation_status, 
    status, document_status, jpo_reviewed, jpo_reviewed_at, jpo_remarks, 
    supervisor_approved, supervisor_approved_at, supervisor_remarks, 
    admin_approved, admin_approved_at, submitted_at, approved_at
)
VALUES (
    @EmployerId,
    '{"sec_dti_registration":"documents/demo_sec_dti.pdf","business_permit":"documents/demo_permit.pdf","bir_2303":"documents/demo_bir.pdf","dole_certificate":"documents/demo_dole.pdf"}',
    'Business Permit & SEC Registration',
    'auto_approved',
    'admin_approved',
    'approved',
    1, DATEADD(day, -10, SYSDATETIME()), 'Verified corporate documents and BIR 2303 registration.',
    1, DATEADD(day, -9, SYSDATETIME()), 'Accreditation paperwork validated.',
    1, CAST(DATEADD(day, -8, GETDATE()) AS DATE),
    CAST(DATEADD(day, -12, GETDATE()) AS DATE),
    CAST(DATEADD(day, -8, GETDATE()) AS DATE)
);

INSERT INTO dbo.job_postings (
    employer_id, title, description, qualifications, vacancy_count, 
    valid_until, accepts_disability, disability_type, status, created_by, created_at, approved_at
)
VALUES (
    @EmployerId,
    'Junior Web Developer',
    'We are seeking an enthusiastic Junior Web Developer to assist in building modern web and mobile applications using modern frameworks.',
    '• Bachelor''s degree in IT/CS or vocational certification' + CHAR(10) + '• Basic knowledge of JavaScript, PHP, and SQL' + CHAR(10) + '• Good problem-solving and communication skills',
    3,
    CAST(DATEADD(month, 3, GETDATE()) AS DATE),
    1,
    'Visual/Hearing impaired with accommodations',
    'approved',
    'employer',
    CAST(GETDATE() AS DATE),
    CAST(GETDATE() AS DATE)
);
DECLARE @JobId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.placement_reports (
    employer_id, jpo_id, report_type, report_month, report_data, 
    status, admin_remarks, approved_at, jpo_evaluated, jpo_evaluated_at, jpo_remarks, created_at, updated_at
)
VALUES (
    @EmployerId, @JpoProfileId, 'employer_monthly',
    DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 1),
    '{"hires":1,"positions":["Junior Web Developer"]}',
    'approved', 'Monthly report verified and archived.', CAST(GETDATE() AS DATE),
    1, SYSDATETIME(), 'All placement records verified with payroll.', SYSDATETIME(), SYSDATETIME()
);

INSERT INTO dbo.notifications (user_id, title, message, type, is_read, created_at, updated_at)
VALUES (@EmployerUserId, 'Company Profile Verified', 'TechCorp Solutions Inc. is accredited. Your job opening for Junior Web Developer is published.', 'approval', 0, SYSDATETIME(), SYSDATETIME());

-- 5. Jobseeker (Juan Santos Dela Cruz)
INSERT INTO dbo.users (email, password, role, status, is_approved, created_at, updated_at)
VALUES ('jobseeker@trabago.com', '$2y$12$UOJiYAM4mEw8mrxE0B47TetoWfLDovZFaiWpA3QRoSzQ5Yf86llrS', 'jobseeker', 'active', 1, SYSDATETIME(), SYSDATETIME());
DECLARE @JobseekerUserId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.jobseekers (
    user_id, first_name, last_name, middle_name, birth_date, 
    sex, civil_status, citizenship, mobile_number, email, employment_status, hired_company
)
VALUES (
    @JobseekerUserId, 'Juan', 'Dela Cruz', 'Santos', '1998-05-15',
    'Male', 'Single', 'Filipino', '09123456789', 'jobseeker@trabago.com', 'Unemployed', NULL
);
DECLARE @JobseekerId BIGINT = SCOPE_IDENTITY();

INSERT INTO dbo.jobseeker_details (
    jobseeker_id, address, education, work_experience, eligibility, language_proficiency, training_certificates
)
VALUES (
    @JobseekerId,
    '{"city":"Cebu City","province":"Cebu","barangay":"Lahug"}',
    '["BS Information Technology","University of Cebu"]',
    '[]',
    '[]',
    '["English","Filipino","Cebuano"]',
    '[{"id":"doc_1","name":"Juan_DelaCruz_Resume.pdf","category":"resume","file_url":"documents/resumes/sample.pdf","file_type":"pdf","file_size":245000,"status":"verified","uploaded_at":"2026-09-01T08:30:00Z"}]'
);

INSERT INTO dbo.jobseeker_skills (jobseeker_id, skill_name, skill_type) VALUES
(@JobseekerId, 'PHP', 'technical'),
(@JobseekerId, 'Laravel', 'technical'),
(@JobseekerId, 'JavaScript', 'technical'),
(@JobseekerId, 'HTML/CSS', 'technical'),
(@JobseekerId, 'MySQL', 'technical'),
(@JobseekerId, 'Communication', '21st_century'),
(@JobseekerId, 'Problem Solving', '21st_century');

INSERT INTO dbo.job_preferences (jobseeker_id, occupation1, industry1, preferred_location, salary_expectation)
VALUES (@JobseekerId, 'Junior Web Developer', 'Information Technology', 'Cebu City', '25000');

INSERT INTO dbo.social_status (jobseeker_id, is_4ps, is_ofw, is_pwd)
VALUES (@JobseekerId, 0, 0, 0);

INSERT INTO dbo.job_applications (
    job_id, jobseeker_id, status, referred_by_jpo, created_at, updated_at
)
VALUES (
    @JobId, @JobseekerId, 'pending', 0, SYSDATETIME(), SYSDATETIME()
);

INSERT INTO dbo.training_enrollments (
    jobseeker_id, training_id, training_type, status, start_date
)
VALUES (
    @JobseekerId, @TrainingId, 'online', 'enrolled', CAST(GETDATE() AS DATE)
);

INSERT INTO dbo.notifications (user_id, title, message, type, is_read, created_at, updated_at)
VALUES (@JobseekerUserId, 'Welcome to TrabaGo!', 'Your jobseeker profile is ready. You have applied to Junior Web Developer at TechCorp Solutions Inc.', 'approval', 0, SYSDATETIME(), SYSDATETIME());
GO

-- ============================================================================
-- VERIFICATION QUERY
-- ============================================================================
PRINT 'TrabaGo Database setup complete!'
SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME;
GO

export interface AddressDetails {
  street?: string;
  barangay?: string;
  city?: string;
  province?: string;
  zip?: string;
  full?: string;
}

export interface EducationDetails {
  level?: string;
  school?: string;
  course?: string;
  year_graduated?: string;
}

export interface WorkExperienceDetails {
  company?: string;
  position?: string;
  duration?: string;
  description?: string;
  summary?: string;
}

export interface EligibilityDetails {
  civil_service?: string;
  prc_license?: string;
  tesda_nc?: string;
  driver_license?: string;
}

export interface JobPreferencesDetails {
  occupation1?: string;
  occupation2?: string;
  industry1?: string;
  preferred_location?: string;
  salary_expectation?: string;
}

export interface SocialStatusDetails {
  is_pwd?: boolean;
  pwd_type?: string;
  is_4ps?: boolean;
  household_id?: string;
  is_ofw?: boolean;
}

export interface UserProfile {
  id: number;
  user_id: number;
  email: string;
  firstName: string;
  first_name?: string;
  lastName: string;
  last_name?: string;
  middleName?: string;
  middle_name?: string;
  fullName: string;
  full_name?: string;
  phone?: string;
  mobile_number?: string;
  education?: string;
  education_details?: EducationDetails;
  address?: AddressDetails;
  work_experience?: WorkExperienceDetails;
  eligibility?: EligibilityDetails;
  languages?: string[];
  bio?: string;
  skills: string[];
  sex?: string | null;
  civilStatus?: string | null;
  civil_status?: string | null;
  citizenship?: string;
  birthDate?: string | null;
  birth_date?: string | null;
  employmentStatus?: string;
  employment_status?: string;
  hired_company?: string | null;
  is_employed?: boolean;
  profile_strength?: number;
  profileStrength?: number;
  preferences?: JobPreferencesDetails;
  social_status?: SocialStatusDetails;
  role: string;
  status: string;
  isApproved?: boolean;
  is_approved?: boolean;
  is_verified?: boolean;
  isVerified?: boolean;
  is_contact_verified?: boolean;
  isContactVerified?: boolean;
  account_status?: 'unverified' | 'pending_review' | 'verified' | string;
  accountStatus?: 'unverified' | 'pending_review' | 'verified' | string;
  is_profile_complete?: boolean;
  missing_requirements?: string[];
  avatar?: string | null;
}

export interface JobItem {
  id: number;
  job_id: number;
  title: string;
  company: string;
  company_name: string;
  employer_id?: number;
  location: string;
  type: string;
  employment_type: string;
  salary: string;
  salary_expectation: string;
  description: string;
  qualifications: string;
  requirements: string[];
  skills: string[];
  required_skills?: string[];
  benefits?: string | string[] | null;
  other_instructions?: string | null;
  vacancy_count: number;
  valid_until?: string | null;
  accepts_disability: boolean;
  disability_type?: string | null;
  status: string;
  created_at: string;
  match_percentage?: number | null;
  matched_skills?: string[];
  missing_skills?: string[];
  match_details?: {
    percentage: number;
    tier: string;
    matchedSkills: string[];
    missingSkills: string[];
    matchedCount: number;
    totalRequired: number;
  };
  has_applied?: boolean;
  can_apply?: boolean;
  is_employed?: boolean;
  hired_company?: string | null;
  recommended_trainings?: {
    id: number;
    title: string;
    duration_months: number;
    training_type: string;
  }[];
}

export interface JobApplicationItem {
  id: string;
  application_id: number;
  jobId: number;
  job_id: number;
  jobTitle: string;
  job_title: string;
  company: string;
  company_name: string;
  status: 'pending' | 'reviewed' | 'interview' | 'offered' | 'hired' | 'rejected' | 'withdrawn' | 'declined' | string;
  appliedAt: string;
  applied_at: string;
  interviewSchedule?: string | null;
  interview_schedule?: string | null;
  interviewMode?: string | null;
  interview_mode?: string | null;
  interviewLocation?: string | null;
  interview_location?: string | null;
  interviewStatus?: string | null;
  interview_status?: string | null;
  jobseekerResponse?: string | null;
  jobseeker_response?: string | null;
  match_percentage?: number | null;
  match_details?: {
    score: number;
    matchedSkills: string[];
    missingSkills: string[];
  };
  hired_date?: string | null;
  resignation_status?: string | null;
  resignation_reason?: string | null;
}

export interface ApplicationCounts {
  all: number;
  offered: number;
  pending: number;
  reviewed: number;
  interview: number;
  hired: number;
  rejected: number;
}

export interface QuizQuestion {
  question: string;
  choices: string[];
  options: string[];
  answer: number;
}

export interface TrainingTopic {
  id: number;
  topic_id: number;
  title: string;
  videoUrl?: string | null;
  video_url?: string | null;
  order?: number;
  questions?: QuizQuestion[];
}

export interface TrainingProgramItem {
  id: number;
  training_id: number;
  title: string;
  trainingType: string;
  training_type: string;
  durationMonths: number;
  duration_months: number;
  duration?: string;
  description?: string;
  topics?: TrainingTopic[];
  modulesCount?: number;
  questions?: QuizQuestion[];
  passing_score?: number;
  is_enrolled?: boolean;
  is_completed?: boolean;
  certificate_issued?: boolean;
  certificate_no?: string | null;
  score?: number | null;
  enrollment_id?: number | null;
  skills?: string[];
  status?: 'enrolled' | 'in_progress' | 'completed' | 'failed' | 'not_enrolled' | string;
  passed?: boolean;
  enrolled_skills?: string | null;
}

export interface CertificateItem {
  enrollment_id: number;
  certificate_no: string;
  certificate_issued_at: string;
  score: number;
  course_title: string;
  training_type: string;
  duration_months: number;
}

export interface VaultDocument {
  id: string;
  name: string;
  category: 'resume' | 'valid_id' | 'certificate' | 'pwd_id' | string;
  file_url?: string;
  url?: string;
  file_path?: string;
  file_size?: number;
  mime_type?: string;
  status?: string;
  rejection_reason?: string;
  reviewed_at?: string;
  uploaded_at?: string;
  certificate_no?: string;
  course_title?: string;
}

export interface NotificationItem {
  id: number | string;
  title: string;
  message: string;
  is_read: boolean;
  created_at: string;
  type?: string;
  related_id?: number | null;
}

export interface DashboardData {
  profile_strength: number;
  stats: {
    available_jobs: number;
    active_applications: number;
    available_trainings: number;
    profile_strength: number;
  };
  best_match: JobItem | null;
  ranked_jobs: JobItem[];
  recent_applications: {
    id: string;
    jobTitle: string;
    company: string;
    status: string;
    appliedAt: string;
  }[];
  recommended_trainings: {
    id: number;
    title: string;
    duration_months: number;
    training_type: string;
  }[];
  is_employed: boolean;
  hired_company?: string | null;
}

export interface SkillCatalogItem {
  skill_name: string;
  course_id: number;
  course_title: string;
  course_type: string;
  duration_months: number;
  topics_count: number;
  passing_score: number;
  status: string;
  is_earned: boolean;
  certificate_issued: boolean;
  certificate_no?: string | null;
  enrollment_id?: number | null;
}

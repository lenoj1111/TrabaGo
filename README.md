# 🌴 TrabaGo — Cebu City DMDP Employment & Skills Facilitation Platform

> **Official Capstone Project & Integrated Digital Platform for the City Government of Cebu — Department of Manpower Development and Placement (DMDP) & Public Employment Service Office (PESO)**

---

## 📌 Project Overview

**TrabaGo** is a modern, full-stack multi-role web platform and mobile application designed to modernize public employment facilitation, vocational skills certifications, employer legal accreditations, and placement analytics for the City Government of Cebu.

Built on a unified **Minimalist Green** design language, TrabaGo bridges Cebuano jobseekers with accredited employers through AI-driven skill matching, free vocational training programs, and transparent PESO coordination.

---

## 🏛️ Official DMDP Mandate

TrabaGo embodies the core vision, mission, and values of the Cebu City Department of Manpower Development and Placement:

* **👁️ Vision (`DMDP PESO`):**
  * **D**eveloped **M**eaningful **D**ynamic **P**rograms for **P**eople's **E**mployability **S**kills and **O**pportunities.
* **🎯 Mission (`Advancing AcCESS by 2025`):**
  * **Ac**celerating **C**areer **E**ntrepreneurial and **S**kills development for **S**uccess.
* **💛 Core Values (`We EMPLOY`):**
  * **E** — Excellence
  * **M** — Mission-driven
  * **P** — Peoples-oriented
  * **L** — Limitless
  * **O** — Open-minded
  * **Y** — Yielding results

---

## 🌟 Key Features & Ecosystem Roles

### 1. 🧑‍💼 Jobseeker Portal (Web & Mobile)
- **AI Skill-Match Engine:** Cosine similarity algorithm evaluates candidate skills against job vacancy qualifications to compute real-time compatibility scores.
- **Ranked Vacancy Feed & Search:** Filters by keyword, salary, employment type, and PWD/disability accommodations.
- **Application Lifecycle Tracking:** Full multi-stage pipeline: `Pending Review` $\rightarrow$ `JPO Referred` $\rightarrow$ `Employer Screening` $\rightarrow$ `Interview Scheduled` $\rightarrow$ `Job Offer Accepted` $\rightarrow$ `Hired`.
- **Vocational Training Hub:** Self-service enrollment into DMDP technical/vocational tracks, interactive module syllabus, and knowledge quizzes.
- **Automated Digital Certifications:** Immediate issuance of verified Certificate of Completion credentials (`DMDP-CERT-YYYY-XXXXXX`) with auto-synced profile skills.
- **Document Hub:** Multi-category vault for resumes, government IDs, barangay clearances, PWD IDs, and 4Ps papers.

### 2. 🏢 Corporate Employer Portal
- **Legal Accreditation Pipeline:** Digital upload of SEC/DTI registration, Mayor's Permit, and BIR 2303 for JPO & Admin verification.
- **Job Vacancy Management:** Create, edit, and publish job postings with disability-inclusive tagging and qualification requirements.
- **Candidate Evaluation & Scheduling:** Review JPO-recommended candidates, schedule interviews with venue/online link, send job offers, and record hires.
- **Placement Reporting:** Submit monthly compliance and placement reports.

### 3. 📋 Job Placement Officer (JPO) Portal
- **Jobseeker Evaluations & Referrals:** Screen applicant documents and endorse candidates directly to accredited employers.
- **Accreditation Inspection:** Review corporate document packages and forward recommendations for admin approval.
- **Placement Audit:** Evaluate employer monthly placement logs.

### 4. 🎓 Skills Trainer Portal
- **Course & Module Builder:** Author training curricula, lesson topics, and video lecture materials.
- **Interactive Quiz Management:** Configure question banks and passing score thresholds.
- **Student Progress & Grading:** Track trainee enrollment rosters, score assessments, and issue certificates.

### 5. 👑 System Administrator Portal
- **Central Approvals Queue:** Authorize job postings, verify employer accreditations, and approve placement reports.
- **User Account Management:** Create and manage role access permissions across the platform.
- **Statistical Analytics & Audit Logs:** City-wide placement rates, application influx graphs, and compliance audit trail.

---

## 📱 Cross-Platform Mobile Application (`mobile/`)

TrabaGo includes a cross-platform mobile application built with **React Native, TypeScript, and Expo Go**:

- **Real-Time Database Sync:** 100% synced with the Laravel REST API backend (`/api/*`).
- **Native Document Attachment:** Built-in `expo-document-picker` for attaching PDF and DOCX resumes.
- **In-App Training Quiz Engine:** Multi-step quiz questionnaire with question navigation and instant score computation.
- **Digital Certificate Preview:** Modal credential viewer with verified certification numbers.
- **Configurable API Endpoint:** On-the-fly backend LAN IP switcher in the Profile tab for testing over Wi-Fi.

---

## 🛠️ Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Backend Framework** | Laravel 12 / PHP 8.2+ |
| **Database** | Microsoft SQL Server (SSMS) / MySQL / SQLite |
| **API & Auth** | Laravel Sanctum RESTful API / Session Auth |
| **Web Styling & UI** | TailwindCSS (Pure Minimalist Green `#16a34a`), Alpine.js, Blade Components |
| **Mobile Framework** | React Native, Expo Router, TypeScript |
| **Asset Bundler** | Vite v8 |

---

## 🚀 Installation & Quick Start Guide

### Step 1: Clone the Repository
```bash
git clone https://github.com/lenoj1111/TrabaGo.git
cd TrabaGo
```

### Step 2: Install Backend & Frontend Dependencies
```bash
# Install PHP dependencies
composer install

# Install Web frontend assets
npm install

# Install Mobile dependencies
cd mobile && npm install && cd ..
```

### Step 3: Setup Environment Configuration
```powershell
# Copy template on Windows (PowerShell)
Copy-Item .env.example .env

# Generate encryption key
php artisan key:generate
```

### Step 4: Configure Database Connection
Edit `.env` with your SQL Server or MySQL database credentials:
```env
DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=Trabago1
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Migrations & Seeders
```bash
php artisan migrate --seed
php artisan storage:link
```

---

## 🖥️ Running the Application

### 1. Web Application & Backend API
To allow both your web browser and mobile device on LAN to access the platform, serve Laravel on `0.0.0.0`:

**Terminal 1 (Laravel Server & API):**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Terminal 2 (Vite Frontend Assets):**
```bash
npm run dev
```

* Navigate to: **`http://localhost:8000`**

### 2. Mobile App (Expo Go)
Open a new terminal window:

```bash
cd mobile
npx expo start
```

* **Physical Phone:** Open the **Expo Go** app and scan the QR code displayed in the terminal.
* **Web Browser Preview:** Press **`w`** in the Expo terminal to open `http://localhost:8081`.
* **Android Emulator:** Press **`a`** in the Expo terminal.

---

## 🔑 Demo User Accounts

All demo accounts use the default password: **`password123`**

| Role | Email Address | Default Password | Portal Route |
| :--- | :--- | :--- | :--- |
| 👑 **Administrator** | `admin@trabago.com` | `password123` | `/admin/dashboard` |
| 📋 **Job Placement Officer (JPO)** | `jpo@trabago.com` | `password123` | `/jpo/dashboard` |
| 🎓 **Skills Trainer** | `trainer@trabago.com` | `password123` | `/trainer/dashboard` |
| 🏢 **Accredited Employer** | `employer@techcorp.com` | `password123` | `/employer/homepage` |
| 🧑‍💼 **Jobseeker** | `jobseeker@example.com` | `password123` | `/jobseeker/home` |

---

## 🧪 Testing & Verification

Run the automated PHPUnit test suite:
```bash
php artisan test
```

---

## 👥 Contributors & DMDP Capstone Team

- **Platform:** TrabaGo
- **Agency:** Cebu City Department of Manpower Development and Placement (DMDP) / PESO
- **License:** Open-source under the [MIT License](LICENSE)

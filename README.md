# TrabaGo — Region 7 (Central Visayas) Employment & Skills Facilitation Platform

> **Official Integrated Digital Platform for the Department of Manpower Development and Placement (DMDP) & Public Employment Service Office (PESO) — Region 7 (Central Visayas)**

---

## 📌 Project Overview

**TrabaGo** is an enterprise-grade, multi-role web platform and cross-platform mobile application designed to modernize public employment facilitation, vocational skills certification, employer accreditation, and workforce placement analytics across **Region 7 (Central Visayas — Cebu, Bohol, Negros Oriental, and Siquijor)**.

Built with an **Emerald Green** design system, TrabaGo bridges jobseekers with accredited corporate employers through AI-driven skill matching, DOLE NSRP compliance, self-paced vocational training, and transparent PESO/JPO coordination.

---

## 🏗️ System Architecture & Access Model

The TrabaGo ecosystem consists of two synchronized client interfaces connected to a unified Laravel REST API backend:

```mermaid
graph TD
    subgraph Clients
        Web["💻 Web Portal (Blade + Tailwind + Alpine.js)<br/>Admin, Trainer, JPO, Employer, Jobseeker"]
        Mobile["📱 Mobile App (React Native + Expo)<br/>Exclusively for Jobseekers"]
    end

    subgraph Backend["⚙️ TrabaGo Core Backend (Laravel 12)"]
        Sanctum["Auth Guard & Sanctum Token Engine"]
        API["REST API (/api/*) & Web Routes"]
        Reverb["Realtime WebSockets (Laravel Reverb :8080)"]
    end

    subgraph Storage["🗄️ Persistence Layer"]
        DB[("Database (SQLite / MySQL / SQL Server)")]
        Disk[("Storage Disk (Document Hub & Certs)")]
    end

    Web --> API
    Mobile --> Sanctum
    Sanctum --> API
    API --> DB
    API --> Disk
    API --> Reverb
    Reverb -. Realtime Events .-> Web
    Reverb -. Push Updates .-> Mobile
```

> [!IMPORTANT]
> **Mobile Access Policy**: The mobile application is **strictly reserved for Jobseekers**. Administrative, verification, and facilitation roles (**Admin**, **Trainer**, **JPO**, and **Employer**) access their portals via the **Web Application**.

---

## ✨ Key Capabilities & Region 7 Feature Highlights

### 1. 🏳️‍🌈 User Demographics & Sector Inclusions (PSIC / NSRP Alignment)
- **LGBTQ+ Membership**: Dedicated demographic indicator tracking LGBTQ+ sector representation.
- **Senior Citizens**: Recognition and placement facilitation for senior citizens seeking gainful employment.
- **Immigrant Workers**: Support and tracking for immigrant worker demographics.
- **Region 7 (Central Visayas) Scope**: Registration and address management covers Cebu, Bohol, Negros Oriental, and Siquijor without restrictive city/province locks.

### 2. 🤝 Jobseeker Employment Flexibility & Privacy Rules
- **"Open to All" Flexibility**: Jobseekers remain eligible and open to new opportunities even while currently employed.
- **Status Privacy**: A jobseeker's current employment status is kept private from employers.
- **Formal Offer Acceptance Flow**: When an employer extends an offer, the candidate reviews the package and can **Accept** or **Decline**. The candidate status only transitions to **"Hired"** once the jobseeker explicitly accepts the job offer.

### 3. 🏢 Employer Account Management, Accreditation & Recovery
- **Dual Email Support**: Primary login email + secondary corporate backup email for employer contacts.
- **Authorized Contact Person**: Dedicated fields for company HR officers/representatives.
- **Admin & JPO Account Creation**: Admins and JPOs share authority to provision employer and jobseeker accounts.
- **HR Turnover Credential Recovery ("Update Email/Pass")**: In case an assigned HR representative resigns without handing over credentials, Administrators can directly update the employer login email and reset the account password so new HR personnel regain immediate access.
- **Official Accreditation Certificates**: Admins issue official Certificates of Accreditation (`DMDP-EMP-YYYY-XXXXX`) upon employer verification.
- **JPO Rejection Loop**: If an employer accreditation application is rejected by Admin, it is returned to the JPO queue with mandatory administrative remarks.
- **Direct Accreditation Override**: Admins can directly accredit or disaccredit employers with interactive warning confirmations.

### 4. 📋 Job Postings & DOLE NSRP Form Compliance
- **Strict Skills Standardization**: Required skills in job postings align with the National Skills Registration Program (NSRP Form 1).
- **Skill Input Types**: Standardized skill selections via dropdowns with an **"Others"** free-text input option.
- **AI Skill-Match Engine**: Cosine similarity algorithm scores applicant competencies against vacancy requirements.

### 5. 🎓 Vocational Training & Digital Certifications
- **Course & Module Builder**: Trainers author course syllabi, lesson units, and video lecture materials.
- **Interactive Quizzes**: Trainers configure question banks and passing score thresholds (default: 80%).
- **Digital Certificates**: Automatic generation and issuance of verifiable completion certificates (`DMDP-CERT-YYYY-XXXXXX`).

### 6. 📁 Document Vault & Verification Hub
- Multi-category vault for Resumes, Government IDs, Technical Licensures, PWD IDs, BIR 2303, SEC/DTI registrations, and Mayor's Business Permits with built-in preview and inspection tools.

### 7. 📊 Monthly Placement Reports
- Employers submit monthly placement reports; JPOs inspect and verify hired records; Admins provide final authorization.

---

## 🛠️ Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Backend Framework** | Laravel 12 (PHP 8.2+) |
| **Database** | SQLite / MySQL / Microsoft SQL Server |
| **Authentication** | Laravel Session Guards & Laravel Sanctum Tokens |
| **Realtime WebSockets** | Laravel Reverb (Port 8080) |
| **Web Frontend** | Blade Components, TailwindCSS, Alpine.js, Vite |
| **Mobile Application** | React Native, Expo SDK (Jobseeker interface) |
| **Testing Suite** | PHPUnit (157 Feature & Unit Tests) |

---

## 🚀 Setup & Installation Guide

### Prerequisites
- **PHP 8.2+** with `pdo`, `mbstring`, `openssl`, `curl`, and `fileinfo` extensions
- **Composer** (v2+)
- **Node.js** (v18+) & **npm**
- **Git**

---

### Step 1: Clone & Checkout

```bash
git clone https://github.com/lenoj1111/TrabaGo.git
cd TrabaGo
git checkout jimmy
```

---

### Step 2: Install Dependencies

```bash
# 1. Install Backend PHP dependencies
composer install

# 2. Install Web Frontend dependencies
npm install

# 3. Install Mobile App dependencies (if working on mobile)
cd mobile
npm install
cd ..
```

---

### Step 3: Environment Configuration

```bash
# Copy root .env file
cp .env.example .env

# Generate application encryption key
php artisan key:generate
```

Configure your database in `.env`:
```env
APP_NAME=TrabaGo
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# SQLite (Default / Recommended for Development)
DB_CONNECTION=sqlite

# Or MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=trabago
# DB_USERNAME=root
# DB_PASSWORD=

# Laravel Reverb (WebSockets)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=trabago-app
REVERB_APP_KEY=ckzj1ifqrv7u8cszy3tq
REVERB_APP_SECRET=reverb-secret-key
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME="http"
```

---

### Step 4: Database Migrations, Seeding & Storage Link

```bash
# Run migrations and seed staff accounts
php artisan migrate --seed

# Create storage symbolic link (vital for documents, logos, and certificates)
php artisan storage:link
```

---

## 🖥️ Running the Application

Open separate terminal windows for the required services:

### Terminal 1: Laravel Web & API Server
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
- Web Application: **`http://localhost:8000`**
- REST API Base: **`http://localhost:8000/api`**

### Terminal 2: Web Frontend Assets (Vite)
```bash
npm run dev
# Or create a production build:
npm run build
```

### Terminal 3: Realtime WebSockets (Laravel Reverb)
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### Terminal 4: Mobile Application (Expo Go - Optional)
```bash
cd mobile
npx expo start -c
```
- Press **`w`** for web browser preview (`http://localhost:8081`).
- Scan the terminal QR code with the **Expo Go** mobile app.

---

## 👥 Official Seeded Accounts

All default seeded accounts use the password: **`password123`**

| Role | Email Address | Default Password | Portal URL |
| :--- | :--- | :--- | :--- |
| 🛡️ **System Administrator** | `admin@trabago.com` | `password123` | `/admin/dashboard` |
| 🎓 **Skills Trainer** | `trainer@trabago.com` | `password123` | `/trainer/dashboard` |
| 👔 **Job Placement Officer (JPO)** | `jpo@trabago.com` | `password123` | `/jpo/dashboard` |
| 🏢 **Employer** | *Self-registered / Seeded* | *User defined* | `/employer/home` |
| 💼 **Jobseeker** | *Self-registered* | *User defined* | `/jobseeker/home` or Mobile |

---

## 🧪 Testing & Verification

Run automated test suites with PHPUnit:

```bash
# Run all feature and unit tests
php artisan test

# Run Admin Accreditation, Security & Credential Management tests
php artisan test --filter=AdminAccreditationAndSecurityTest

# Run Offer Acceptance and Demographics tests
php artisan test --filter=OfferAcceptanceFlowTest
```

---

## 📤 How to Push Code to the `jimmy` Branch

If you are working on your local branch (e.g. `jimmy-v2`) and want to push your commits to the remote `jimmy` branch:

### Option A: Push Local `jimmy-v2` directly to Remote `jimmy`
```bash
# Push your current commits to origin/jimmy
git push origin jimmy-v2:jimmy
```

### Option B: Merge into Local `jimmy` Branch and Push
```bash
# 1. Fetch latest changes from remote
git fetch origin

# 2. Switch to or create local jimmy tracking branch
git checkout -B jimmy origin/jimmy

# 3. Merge your updates from jimmy-v2
git merge jimmy-v2

# 4. Push to remote jimmy
git push origin jimmy
```

---

## 📄 License & Credits
- **Project**: TrabaGo
- **Agency**: Department of Manpower Development and Placement (DMDP) / Public Employment Service Office (PESO) — Region 7
- **License**: Open-source under the [MIT License](LICENSE)

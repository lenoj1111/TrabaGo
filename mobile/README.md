# 📱 TrabaGo — Jobseeker Mobile Application

> **Dedicated Cross-Platform Mobile Client for the Cebu City Department of Manpower Development and Placement (DMDP). Built with React Native, Expo Router, TypeScript, and Tailwind tokens.**

---

## 🔒 Access Policy
This mobile application is **exclusively designated for Jobseekers**. Administrative, verification, and facilitation staff (**Admin**, **Trainer**, **JPO**, and **Employer**) are restricted by API guard policies and must access their respective workspaces via the **TrabaGo Web Portal**.

---

## 🌟 Features & Capabilities

- **Live Job Vacancy Feed (`/(tabs)`)**: Real-time listing with keyword search, location filtering, and disability-inclusive badges.
- **AI Skill-Matching Engine**: Cosine similarity algorithm analyzes applicant competencies against requirements and displays match percentages.
- **Application Tracking (`/(tabs)/applications`)**: Track submission status across multi-stage review pipelines (`Pending`, `Screening`, `Interview Scheduled`, `Hired`).
- **DMDP Vocational Hub (`/(tabs)/training`)**: Browse free technical and vocational modules with direct video lectures.
- **In-App Competency Assessments (`/training/[id]`)**: Interactive quiz with dynamic passing score thresholds (80%), question-by-question review, and instant official digital certificate issuance.
- **Credentials Vault (`/profile/vault`)**: Multi-document storage supporting Resumes, Valid Government IDs, Technical Licenses, and PWD IDs with in-app preview and status verification tracking.
- **DOLE NSRP Profile (`/profile/edit`)**: Official DOLE-DMDP NSRP Form 1 fields including education attainment, career preferences, civil service eligibility, and social welfare status.
- **Real-Time Push Updates (`/(tabs)/notifications`)**: Real-time notifications powered by Laravel Reverb WebSockets.

---

## 🚀 Mobile Quick Start

### 1. Install Dependencies
```bash
cd mobile
npm install
```

### 2. Configure Environment
Copy `.env.example` to `.env`:
```powershell
Copy-Item .env.example .env
```

Set your machine's local Wi-Fi / LAN IP in `mobile/.env`:
```env
# Physical Device (Expo Go): Replace with your computer's LAN IPv4 (run 'ipconfig')
EXPO_PUBLIC_API_URL=http://192.168.100.9:8000/api

# Realtime WebSocket Configuration (Laravel Reverb)
EXPO_PUBLIC_REVERB_HOST=192.168.100.9
EXPO_PUBLIC_REVERB_PORT=8080
EXPO_PUBLIC_REVERB_KEY=ckzj1ifqrv7u8cszy3tq
```

### 3. Launch Expo Development Server
```bash
npx expo start -c
```

- **Physical Phone**: Scan the QR code with **Expo Go** (Android) or **Camera** (iOS).
- **Web Browser**: Press `w` in the terminal to launch the mobile web emulator at `http://localhost:8081`.
- **Android Emulator**: Press `a` in the terminal.

---

## 🧪 TypeScript Verification
Ensure all mobile screens and service hooks compile with zero errors:
```bash
npx tsc --noEmit
```

# 📱 TrabaGo — Jobseeker Mobile App (React Native & Expo Go)

> **Dedicated cross-platform mobile application for Cebu City DMDP Jobseekers built with React Native, TypeScript, and Expo Go.**

---

## 🌟 Key Features

1. **Explore Jobs (`/`)**:
   - Live search by job title, company, or skills.
   - Filter pills: All, Full-time, PWD/Disability-friendly.
   - Job cards with vacancy badges, salary display, and posting dates.
   - Job Details view (`/jobs/[id]`) with DMDP verification banner and qualifications checklist.
   - One-tap Application Sheet (`/jobs/apply`) with resume attachment (`expo-document-picker`) and cover notes.

2. **Applications Tracker (`/applications`)**:
   - Filter by status: All, Pending, Under Review, Hired.
   - Interview Schedule alerts (date, venue/location, and meeting mode).
   - Instant application withdrawal with confirmation.

3. **DMDP Training & Courses (`/training`)**:
   - Browse vocational and digital courses offered by the Cebu DMDP.
   - View syllabus, topic modules, and lecture video indicators.
   - Interactive Competency Quiz with real-time scoring and DMDP certificate eligibility verification.

4. **Notifications Center (`/notifications`)**:
   - Real-time updates on application status changes, interview invites, and DMDP bulletins.
   - Unread indicators with tap-to-read functionality.

5. **Profile & DMDP Digital ID (`/profile`)**:
   - Official Cebu City DMDP Jobseeker Digital ID card.
   - Personal information & skills chips manager (`/profile/edit`).
   - Credentials & Document Vault (`/profile/vault`) for uploading resumes and certificates.
   - Live Backend LAN IP Switcher modal to seamlessly connect from physical devices.

---

## 🚀 Quick Start Guide

### Step 1: Start the Laravel Backend

To allow your mobile device on the same local network to connect to the backend, serve Laravel on `0.0.0.0`:

```bash
# In project root: c:\Users\Jimmy\TrabaGo
php artisan serve --host=0.0.0.0 --port=8000
```

> 💡 Your machine's LAN IP is pre-configured as **`192.168.100.9`**.

### Step 2: Start the Expo Development Server

Open a new terminal tab and navigate into the `mobile` directory:

```bash
cd mobile
npx expo start
```

### Step 3: Open in Expo Go

- **Android**: Open the **Expo Go** app on your phone and scan the QR code displayed in the terminal.
- **iOS**: Open the native **Camera** app, point at the QR code, and tap the prompt to open in **Expo Go**.
- **Android Emulator**: Press `a` in the terminal.
- **Web Preview**: Press `w` in the terminal.

---

## 🔑 Demo Jobseeker Account

For instant testing, use the pre-seeded account:

- **Email**: `jobseeker@example.com`
- **Password**: `password123`
- *(Or tap the **"Fill Demo Credentials"** button on the mobile Sign In screen!)*

---

## ⚙️ Network Configuration

If you change Wi-Fi networks, update the IP in `mobile/src/config/api.ts` or directly within the app by navigating to:
**Profile Tab ➔ Configure Backend IP** and entering your new host IP (e.g. `http://192.168.X.X:8000/api`).

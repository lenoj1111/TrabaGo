import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Switch,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { useAuth } from '../../src/context/AuthContext';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';

const normalizePhilippineMobile = (value: string) => {
  const digits = value.replace(/\D/g, '');
  const localNumber = digits.startsWith('63') ? digits.slice(2) : digits.startsWith('0') ? digits.slice(1) : digits;
  return localNumber;
};

export default function RegisterScreen() {
  const router = useRouter();
  const { register, sendOtp, verifyOtp } = useAuth();

  // Role: jobseeker or employer
  const [role, setRole] = useState<'jobseeker' | 'employer'>('jobseeker');

  // Step 1: Form Inputs
  const [firstName, setFirstName] = useState('');
  const [middleName, setMiddleName] = useState('');
  const [lastName, setLastName] = useState('');
  const [companyName, setCompanyName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [birthDate, setBirthDate] = useState('');
  const [sex, setSex] = useState('');
  const [civilStatus, setCivilStatus] = useState('');
  const [citizenship, setCitizenship] = useState('Filipino');
  const [employmentStatus, setEmploymentStatus] = useState('');
  const [street, setStreet] = useState('');
  const [barangay, setBarangay] = useState('');
  const [city, setCity] = useState('Cebu City');
  const [province, setProvince] = useState('Cebu');
  const [postalCode, setPostalCode] = useState('6000');
  const [educationLevel, setEducationLevel] = useState('');
  const [educationCourse, setEducationCourse] = useState('');
  const [educationSchool, setEducationSchool] = useState('');
  const [educationYear, setEducationYear] = useState('');
  const [workCompany, setWorkCompany] = useState('');
  const [workPosition, setWorkPosition] = useState('');
  const [workDuration, setWorkDuration] = useState('');
  const [workDescription, setWorkDescription] = useState('');
  const [bio, setBio] = useState('');
  const [civilService, setCivilService] = useState('');
  const [prcLicense, setPrcLicense] = useState('');
  const [tesdaNc, setTesdaNc] = useState('');
  const [driverLicense, setDriverLicense] = useState('');
  const [languagesStr, setLanguagesStr] = useState('English, Filipino, Cebuano');
  const [skillsStr, setSkillsStr] = useState('');
  const [centurySkillsStr, setCenturySkillsStr] = useState('');
  const [informalSkillsStr, setInformalSkillsStr] = useState('');
  const [occupation1, setOccupation1] = useState('');
  const [occupation2, setOccupation2] = useState('');
  const [occupation3, setOccupation3] = useState('');
  const [industry1, setIndustry1] = useState('');
  const [industry2, setIndustry2] = useState('');
  const [industry3, setIndustry3] = useState('');
  const [preferredLocation, setPreferredLocation] = useState('Cebu City');
  const [salaryMin, setSalaryMin] = useState('');
  const [salaryMax, setSalaryMax] = useState('');
  const [is4ps, setIs4ps] = useState(false);
  const [householdId, setHouseholdId] = useState('');
  const [isOfw, setIsOfw] = useState(false);
  const [isPwd, setIsPwd] = useState(false);
  const [pwdType, setPwdType] = useState('');
  const [isLgbtq, setIsLgbtq] = useState(false);
  const [isSeniorCitizen, setIsSeniorCitizen] = useState(false);
  const [isImmigrant, setIsImmigrant] = useState(false);
  const [immigrantOrigin, setImmigrantOrigin] = useState('');

  // Step 2: OTP Verification
  const [step, setStep] = useState<'form' | 'otp'>('form');
  const [otpCode, setOtpCode] = useState('');
  const [otpPreview, setOtpPreview] = useState<string | null>(null);

  const [loading, setLoading] = useState(false);

  // Trigger duplicate check & OTP dispatch
  const handleProceedToOtp = async () => {
    const trimmedEmail = email.trim();
    const trimmedPhone = phone.trim();

    if (role === 'jobseeker') {
      if (!firstName.trim() || !lastName.trim() || !trimmedEmail || !password.trim() || !phone.trim() || !birthDate.trim() || !sex || !civilStatus || !citizenship.trim() || !employmentStatus || !barangay.trim() || !city.trim() || !province.trim() || !educationLevel) {
        Alert.alert('Missing Fields', 'Please complete the required personal, contact, and address fields from the web registration form.');
        return;
      }
      if (!/^9\d{9}$/.test(normalizePhilippineMobile(trimmedPhone))) {
        Alert.alert('Invalid Mobile Number', 'Enter 10 digits starting with 9, like 9123456789. The +63 prefix is added automatically.');
        return;
      }
      const enteredBirthDate = new Date(`${birthDate}T00:00:00`);
      const latestAllowedBirthDate = new Date();
      latestAllowedBirthDate.setFullYear(latestAllowedBirthDate.getFullYear() - 15);
      if (Number.isNaN(enteredBirthDate.getTime()) || enteredBirthDate > latestAllowedBirthDate) {
        Alert.alert('Invalid Birth Date', 'Jobseekers must be at least 15 years old to register.');
        return;
      }
    } else {
      if (!companyName.trim() || !firstName.trim() || !trimmedEmail || !password.trim()) {
        Alert.alert('Missing Fields', 'Please fill in Company Name, Contact Person, Email, and Password.');
        return;
      }
    }

    if (password !== passwordConfirmation) {
      Alert.alert('Password Mismatch', 'Passwords do not match.');
      return;
    }

    if (password.length < 8) {
      Alert.alert('Weak Password', 'Password must be at least 8 characters long.');
      return;
    }

    setLoading(true);
    try {
      const identifier = trimmedEmail || trimmedPhone;
      const res = await sendOtp(identifier, 'registration');

      if (res.error === 'ACCOUNT_ALREADY_EXISTS' || !res.success) {
        if (res.error === 'ACCOUNT_ALREADY_EXISTS') {
          Alert.alert(
            'Account Already Exists',
            'This email or phone number is already registered. Would you like to retrieve your existing account?',
            [
              { text: 'Cancel', style: 'cancel' },
              {
                text: 'Retrieve Account',
                onPress: () => router.push('/auth/login'),
              },
            ]
          );
        } else {
          Alert.alert('Verification Error', res.message || 'Unable to send verification code.');
        }
        return;
      }

      // OTP sent successfully
      if (res.otp_preview) {
        setOtpPreview(res.otp_preview);
      }
      setStep('otp');
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Network error occurred.');
    } finally {
      setLoading(false);
    }
  };

  // Verify OTP and complete registration
  const handleVerifyOtpAndRegister = async () => {
    if (!otpCode.trim() || otpCode.trim().length < 4) {
      Alert.alert('Invalid Code', 'Please enter the 6-digit verification code.');
      return;
    }

    const identifier = email.trim() || phone.trim();
    setLoading(true);

    try {
      // 1. Verify OTP with server
      const verifyRes = await verifyOtp(identifier, otpCode.trim(), 'registration');
      if (!verifyRes.success) {
        Alert.alert('Verification Failed', verifyRes.message || 'Invalid or expired verification code.');
        setLoading(false);
        return;
      }

      // 2. Complete user registration
      const skillsArray = skillsStr
        .split(',')
        .map((s) => s.trim())
        .filter(Boolean);
      const splitList = (value: string) => value.split(',').map((item) => item.trim()).filter(Boolean);
      const mobileNumber = role === 'jobseeker' ? normalizePhilippineMobile(phone) : phone.trim();

      const regRes = await register({
        first_name: firstName.trim(),
        last_name: lastName.trim(),
        middle_name: middleName.trim(),
        firstName: firstName.trim(),
        lastName: lastName.trim(),
        middleName: middleName.trim(),
        email: email.trim(),
        mobile_number: mobileNumber,
        phone_number: mobileNumber,
        role,
        password,
        password_confirmation: passwordConfirmation,
        skills: skillsArray,
        ...(role === 'jobseeker' ? {
          birth_date: birthDate,
          birthDate,
          sex,
          civil_status: civilStatus,
          civilStatus,
          citizenship: citizenship.trim(),
          employment_status: employmentStatus,
          address: {
            street: street.trim(),
            barangay: barangay.trim(),
            city: city.trim(),
            province: province.trim(),
            zip: postalCode.trim(),
            postal_code: postalCode.trim(),
            country: 'Philippines',
            full: [street.trim(), barangay.trim(), city.trim(), province.trim()].filter(Boolean).join(', '),
          },
          education: {
            level: educationLevel,
            course: educationCourse.trim(),
            school: educationSchool.trim(),
            year_graduated: educationYear.trim(),
          },
          work_experience: {
            company: workCompany.trim(),
            position: workPosition.trim(),
            duration: workDuration.trim(),
            description: workDescription.trim(),
            summary: bio.trim(),
          },
          eligibility: {
            civil_service: civilService.trim(),
            prc_license: prcLicense.trim(),
            tesda_nc: tesdaNc.trim(),
            driver_license: driverLicense.trim(),
          },
          language_proficiency: splitList(languagesStr),
          skills_century: splitList(centurySkillsStr),
          skills_informal_tech: splitList(informalSkillsStr),
          occupation1: occupation1.trim(),
          occupation2: occupation2.trim(),
          occupation3: occupation3.trim(),
          industry1: industry1.trim(),
          industry2: industry2.trim(),
          industry3: industry3.trim(),
          preferred_location: preferredLocation.trim(),
          salary_min: salaryMin.trim(),
          salary_max: salaryMax.trim(),
          is_4ps: is4ps,
          household_id: householdId.trim(),
          is_ofw: isOfw,
          is_pwd: isPwd,
          pwd_type: pwdType.trim(),
          is_lgbtq: isLgbtq,
          is_senior_citizen: isSeniorCitizen,
          is_immigrant: isImmigrant,
          immigrant_origin: immigrantOrigin.trim(),
          region: 'Region VII - Central Visayas',
        } : {}),
        ...(role === 'employer' ? { company_name: companyName.trim() } : {}),
      });

      if (regRes.success) {
        Alert.alert(
          'Welcome to TrabaGo!',
          role === 'employer'
            ? 'Your employer account has been created. Please complete your company profile and accreditation upload.'
            : 'Your profile has been registered and contact verified! Complete your profile setup to unlock job applications.',
          [
            {
              text: 'Setup Profile',
              onPress: () => router.replace('/profile/edit'),
            },
          ]
        );
      } else {
        if (regRes.error === 'ACCOUNT_ALREADY_EXISTS') {
          Alert.alert(
            'Account Already Exists',
            'This email or phone number is already registered. Would you like to retrieve your existing account?',
            [
              { text: 'Cancel', style: 'cancel' },
              { text: 'Log In / Retrieve', onPress: () => router.push('/auth/login') },
            ]
          );
        } else {
          Alert.alert('Registration Failed', regRes.message || 'Could not complete registration.');
        }
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'An unexpected error occurred.');
    } finally {
      setLoading(false);
    }
  };

  const renderField = (
    label: string,
    value: string,
    onChangeText: (text: string) => void,
    placeholder?: string,
    multiline = false
  ) => (
    <View style={styles.fieldGroup}>
      <Text style={styles.label}>{label}</Text>
      <TextInput
        style={[styles.input, multiline && styles.multilineInput]}
        placeholder={placeholder || label}
        placeholderTextColor={COLORS.slate400}
        value={value}
        onChangeText={onChangeText}
        multiline={multiline}
        textAlignVertical={multiline ? 'top' : 'center'}
      />
    </View>
  );

  const renderChoices = (
    label: string,
    options: string[],
    value: string,
    onChange: (choice: string) => void
  ) => (
    <View style={styles.fieldGroup}>
      <Text style={styles.label}>{label}</Text>
      <View style={styles.choiceRow}>
        {options.map((option) => (
          <TouchableOpacity
            key={option}
            style={[styles.choiceButton, value === option && styles.choiceButtonActive]}
            onPress={() => onChange(option)}>
            <Text style={[styles.choiceButtonText, value === option && styles.choiceButtonTextActive]}>
              {option}
            </Text>
          </TouchableOpacity>
        ))}
      </View>
    </View>
  );

  const renderToggle = (label: string, value: boolean, onValueChange: (value: boolean) => void) => (
    <View style={styles.toggleRow}>
      <Text style={styles.toggleLabel}>{label}</Text>
      <Switch value={value} onValueChange={onValueChange} trackColor={{ true: COLORS.primary }} />
    </View>
  );

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.card}>
          
          {/* Header */}
          <View style={styles.headerBox}>
            <View style={styles.logoBadge}>
              <Ionicons name={step === 'otp' ? 'shield-checkmark' : 'person-add'} size={24} color={COLORS.primary} />
            </View>
            <Text style={styles.cardTitle}>
              {step === 'otp' ? 'Verify Contact Info' : 'Create TrabaGo Account'}
            </Text>
            <Text style={styles.cardSubtitle}>
              {step === 'otp'
                ? `Enter the 6-digit verification code sent to ${email.trim() || phone.trim()}`
                : 'Connect with the Cebu City DMDP network for employment matching and corporate accreditation.'}
            </Text>
          </View>

          {/* Role Switcher (Step 1 only) */}
          {step === 'form' && (
            <View style={styles.roleContainer}>
              <TouchableOpacity
                style={[styles.roleTab, role === 'jobseeker' && styles.roleTabActive]}
                activeOpacity={0.88}
                onPress={() => setRole('jobseeker')}>
                <Ionicons
                  name="person"
                  size={16}
                  color={role === 'jobseeker' ? COLORS.primaryDark : COLORS.slate500}
                />
                <Text style={[styles.roleTabText, role === 'jobseeker' && styles.roleTabTextActive]}>
                  Jobseeker
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.roleTab, role === 'employer' && styles.roleTabActive]}
                activeOpacity={0.88}
                onPress={() => setRole('employer')}>
                <Ionicons
                  name="business"
                  size={16}
                  color={role === 'employer' ? COLORS.primaryDark : COLORS.slate500}
                />
                <Text style={[styles.roleTabText, role === 'employer' && styles.roleTabTextActive]}>
                  Employer
                </Text>
              </TouchableOpacity>
            </View>
          )}

          {/* STEP 1: Registration Form */}
          {step === 'form' && (
            <>
              {/* Employer Company Name */}
              {role === 'employer' && (
                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Company / Organization Name *</Text>
                  <TextInput
                    style={styles.input}
                    placeholder="e.g. Cebu Pacific Logistics Corp."
                    placeholderTextColor={COLORS.slate400}
                    value={companyName}
                    onChangeText={setCompanyName}
                  />
                </View>
              )}

              {/* Name Fields */}
              <View style={styles.row}>
                <View style={[styles.fieldGroup, { flex: 1 }]}>
                  <Text style={styles.label}>{role === 'employer' ? 'Contact First Name *' : 'First Name *'}</Text>
                  <TextInput
                    style={styles.input}
                    placeholder="Juan"
                    placeholderTextColor={COLORS.slate400}
                    value={firstName}
                    onChangeText={setFirstName}
                  />
                </View>
                <View style={[styles.fieldGroup, { flex: 1 }]}>
                  <Text style={styles.label}>Last Name *</Text>
                  <TextInput
                    style={styles.input}
                    placeholder="Dela Cruz"
                    placeholderTextColor={COLORS.slate400}
                    value={lastName}
                    onChangeText={setLastName}
                  />
                </View>
              </View>

              {/* Email */}
              <View style={styles.fieldGroup}>
                <Text style={styles.label}>Email Address *</Text>
                <TextInput
                  style={styles.input}
                  placeholder="juan.delacruz@example.com"
                  placeholderTextColor={COLORS.slate400}
                  value={email}
                  onChangeText={setEmail}
                  keyboardType="email-address"
                  autoCapitalize="none"
                />
              </View>

              {/* Mobile Phone */}
              <View style={styles.fieldGroup}>
                <Text style={styles.label}>{role === 'jobseeker' ? '+63 Mobile Phone Number *' : 'Mobile Phone Number'}</Text>
                <TextInput
                  style={styles.input}
                  placeholder={role === 'jobseeker' ? '9123456789' : '09123456789'}
                  placeholderTextColor={COLORS.slate400}
                  value={phone}
                  onChangeText={setPhone}
                  keyboardType="phone-pad"
                />
              </View>

              {/* Jobseeker fields aligned with the web registration form */}
              {role === 'jobseeker' && (
                <View style={styles.profileFields}>
                  <Text style={styles.profileSectionTitle}>Personal Information</Text>
                  {renderField('Middle Name', middleName, setMiddleName, 'Optional')}
                  {renderField('Birth Date * (YYYY-MM-DD)', birthDate, setBirthDate, '1998-05-20')}
                  {renderChoices('Sex *', ['Male', 'Female', 'Prefer not to say'], sex, setSex)}
                  {renderChoices('Civil Status *', ['Single', 'Married', 'Divorced', 'Widowed'], civilStatus, setCivilStatus)}
                  {renderField('Citizenship *', citizenship, setCitizenship, 'Filipino')}
                  {renderChoices('Employment Status *', ['Unemployed', 'Fresh Graduate', 'Employed', 'Underemployed', 'Self-employed'], employmentStatus, setEmploymentStatus)}

                  <Text style={styles.profileSectionTitle}>Current Residence</Text>
                  {renderField('Street / House / Building', street, setStreet, 'Optional')}
                  {renderField('Barangay *', barangay, setBarangay, 'Your barangay')}
                  {renderField('City / Municipality *', city, setCity, 'Cebu City')}
                  {renderField('Province *', province, setProvince, 'Cebu')}
                  {renderField('Postal Code', postalCode, setPostalCode, '6000')}

                  <Text style={styles.profileSectionTitle}>Education & Skills</Text>
                  {renderChoices('Highest Educational Attainment *', ['Elementary', 'High School', 'Senior High School', 'Vocational', 'College', 'Post Graduate'], educationLevel, setEducationLevel)}
                  {renderField('Course / Degree', educationCourse, setEducationCourse, 'e.g. BS Information Technology')}
                  {renderField('School', educationSchool, setEducationSchool, 'School or training center')}
                  {renderField('Year Graduated', educationYear, setEducationYear, '2024')}
                  {renderField('Technical / Vocational Skills (comma-separated)', skillsStr, setSkillsStr, 'Customer Service, Excel, Welding')}
                  {renderField('21st Century Skills (comma-separated)', centurySkillsStr, setCenturySkillsStr, 'Communication, Problem Solving')}
                  {renderField('Informal Technical Skills (comma-separated)', informalSkillsStr, setInformalSkillsStr, 'Skills learned outside formal training')}
                  {renderField('Languages (comma-separated)', languagesStr, setLanguagesStr, 'English, Filipino, Cebuano')}

                  <Text style={styles.profileSectionTitle}>Work Experience & Eligibility</Text>
                  {renderField('Most Recent Company', workCompany, setWorkCompany)}
                  {renderField('Position', workPosition, setWorkPosition)}
                  {renderField('Duration', workDuration, setWorkDuration, 'e.g. 2022-2024')}
                  {renderField('Work Description', workDescription, setWorkDescription, 'Responsibilities and experience', true)}
                  {renderField('Professional Summary', bio, setBio, 'Optional profile summary', true)}
                  {renderField('Civil Service Eligibility', civilService, setCivilService)}
                  {renderField('PRC License', prcLicense, setPrcLicense)}
                  {renderField('TESDA NC', tesdaNc, setTesdaNc)}
                  {renderField("Driver's License", driverLicense, setDriverLicense)}

                  <Text style={styles.profileSectionTitle}>Job Preferences</Text>
                  {renderField('Preferred Occupation 1', occupation1, setOccupation1)}
                  {renderField('Preferred Occupation 2', occupation2, setOccupation2)}
                  {renderField('Preferred Occupation 3', occupation3, setOccupation3)}
                  {renderField('Preferred Industry 1', industry1, setIndustry1)}
                  {renderField('Preferred Industry 2', industry2, setIndustry2)}
                  {renderField('Preferred Industry 3', industry3, setIndustry3)}
                  {renderField('Preferred Work Location', preferredLocation, setPreferredLocation, 'Cebu City')}
                  {renderField('Minimum Monthly Salary (PHP)', salaryMin, setSalaryMin, '15000')}
                  {renderField('Maximum Monthly Salary (PHP)', salaryMax, setSalaryMax, '25000')}

                  <Text style={styles.profileSectionTitle}>Social Support & Classification</Text>
                  {renderToggle('4Ps Beneficiary', is4ps, setIs4ps)}
                  {is4ps && renderField('4Ps Household ID', householdId, setHouseholdId)}
                  {renderToggle('OFW Returnee', isOfw, setIsOfw)}
                  {renderToggle('Person with Disability (PWD)', isPwd, setIsPwd)}
                  {isPwd && renderField('PWD Type', pwdType, setPwdType, 'Optional')}
                  {renderToggle('LGBTQ+ Community Member', isLgbtq, setIsLgbtq)}
                  {renderToggle('Senior Citizen', isSeniorCitizen, setIsSeniorCitizen)}
                  {renderToggle('Immigrant / Migrant Worker', isImmigrant, setIsImmigrant)}
                  {isImmigrant && renderField('Country / Province of Origin', immigrantOrigin, setImmigrantOrigin)}
                </View>
              )}

              {/* Password */}
              <View style={styles.fieldGroup}>
                <Text style={styles.label}>Password (Min 8 chars) *</Text>
                <TextInput
                  style={styles.input}
                  placeholder="••••••••"
                  placeholderTextColor={COLORS.slate400}
                  value={password}
                  onChangeText={setPassword}
                  secureTextEntry
                />
              </View>

              {/* Password Confirmation */}
              <View style={styles.fieldGroup}>
                <Text style={styles.label}>Confirm Password *</Text>
                <TextInput
                  style={styles.input}
                  placeholder="••••••••"
                  placeholderTextColor={COLORS.slate400}
                  value={passwordConfirmation}
                  onChangeText={setPasswordConfirmation}
                  secureTextEntry
                />
              </View>

              {/* Proceed to OTP Button */}
              <TouchableOpacity
                style={styles.submitBtn}
                activeOpacity={0.88}
                onPress={handleProceedToOtp}
                disabled={loading}>
                {loading ? (
                  <ActivityIndicator color={COLORS.white} />
                ) : (
                  <View style={styles.btnRow}>
                    <Text style={styles.submitBtnText}>Verify via OTP</Text>
                    <Ionicons name="arrow-forward" size={16} color={COLORS.white} />
                  </View>
                )}
              </TouchableOpacity>
            </>
          )}

          {/* STEP 2: OTP Verification */}
          {step === 'otp' && (
            <View style={styles.otpContainer}>
              <View style={styles.fieldGroup}>
                <Text style={styles.label}>6-Digit Verification Code *</Text>
                <TextInput
                  style={[styles.input, styles.otpInput]}
                  placeholder="000000"
                  placeholderTextColor={COLORS.slate400}
                  value={otpCode}
                  onChangeText={setOtpCode}
                  keyboardType="number-pad"
                  maxLength={6}
                  autoFocus
                />
              </View>

              {/* Developer / Demo OTP Preview Banner */}
              {otpPreview && (
                <View style={styles.previewBox}>
                  <Ionicons name="information-circle-outline" size={16} color={COLORS.primaryDark} />
                  <Text style={styles.previewText}>
                    Demo Test Code: <Text style={{ fontWeight: '800' }}>{otpPreview}</Text>
                  </Text>
                </View>
              )}

              {/* Submit OTP & Register Button */}
              <TouchableOpacity
                style={styles.submitBtn}
                activeOpacity={0.88}
                onPress={handleVerifyOtpAndRegister}
                disabled={loading}>
                {loading ? (
                  <ActivityIndicator color={COLORS.white} />
                ) : (
                  <View style={styles.btnRow}>
                    <Text style={styles.submitBtnText}>Verify & Create Account</Text>
                    <Ionicons name="checkmark-done" size={16} color={COLORS.white} />
                  </View>
                )}
              </TouchableOpacity>

              {/* Resend / Back Actions */}
              <View style={styles.otpActionsRow}>
                <TouchableOpacity
                  onPress={handleProceedToOtp}
                  disabled={loading}
                  style={styles.resendBtn}>
                  <Text style={styles.resendText}>Resend Code</Text>
                </TouchableOpacity>

                <TouchableOpacity
                  onPress={() => setStep('form')}
                  style={styles.backEditBtn}>
                  <Text style={styles.backEditText}>Edit Information</Text>
                </TouchableOpacity>
              </View>
            </View>
          )}

          {/* Back to Login */}
          <View style={styles.footerRow}>
            <Text style={styles.footerText}>Already have an account? </Text>
            <TouchableOpacity onPress={() => router.push('/auth/login')}>
              <Text style={styles.linkText}>Sign In</Text>
            </TouchableOpacity>
          </View>

        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  scrollContent: {
    padding: SPACING.lg,
    paddingTop: Platform.OS === 'ios' ? SPACING.xxl : SPACING.xl,
    paddingBottom: SPACING.xxl * 2,
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xxl,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.md,
  },
  headerBox: {
    alignItems: 'center',
    marginBottom: SPACING.lg,
  },
  logoBadge: {
    width: 52,
    height: 52,
    borderRadius: BORDER_RADIUS.xl,
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.md,
  },
  cardTitle: {
    fontSize: 20,
    fontWeight: '800',
    color: COLORS.navy,
    letterSpacing: -0.3,
    textAlign: 'center',
  },
  cardSubtitle: {
    fontSize: 12,
    color: COLORS.textSecondary,
    textAlign: 'center',
    marginTop: SPACING.xs,
    lineHeight: 18,
  },
  roleContainer: {
    flexDirection: 'row',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.lg,
    padding: 4,
    marginBottom: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  roleTab: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
    gap: 6,
  },
  roleTabActive: {
    backgroundColor: COLORS.white,
    ...SHADOWS.sm,
  },
  roleTabText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.slate500,
  },
  roleTabTextActive: {
    color: COLORS.primaryDark,
    fontWeight: '800',
  },
  row: {
    flexDirection: 'row',
    gap: SPACING.md,
  },
  fieldGroup: {
    marginBottom: SPACING.md,
  },
  profileFields: {
    marginTop: SPACING.sm,
    paddingTop: SPACING.md,
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
  },
  profileSectionTitle: {
    marginTop: SPACING.md,
    marginBottom: SPACING.sm,
    fontSize: 14,
    fontWeight: '800',
    color: COLORS.primaryDark,
  },
  label: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.navy,
    marginBottom: 6,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  input: {
    borderWidth: 1,
    borderColor: COLORS.border,
    borderRadius: BORDER_RADIUS.lg,
    paddingHorizontal: SPACING.md,
    paddingVertical: 11,
    fontSize: 14,
    color: COLORS.navy,
    backgroundColor: COLORS.surfaceSubtle,
  },
  multilineInput: {
    minHeight: 88,
    paddingTop: SPACING.md,
  },
  choiceRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  choiceButton: {
    borderWidth: 1,
    borderColor: COLORS.border,
    borderRadius: BORDER_RADIUS.md,
    paddingHorizontal: 10,
    paddingVertical: 8,
    backgroundColor: COLORS.surfaceSubtle,
  },
  choiceButtonActive: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.primaryUltraLight,
  },
  choiceButtonText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.slate600,
  },
  choiceButtonTextActive: {
    color: COLORS.primaryDark,
    fontWeight: '800',
  },
  toggleRow: {
    minHeight: 48,
    marginBottom: SPACING.sm,
    paddingHorizontal: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.surfaceSubtle,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  toggleLabel: {
    flex: 1,
    paddingRight: SPACING.sm,
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.navy,
  },
  otpContainer: {
    paddingVertical: SPACING.sm,
  },
  otpInput: {
    fontSize: 24,
    fontWeight: '800',
    letterSpacing: 8,
    textAlign: 'center',
    backgroundColor: COLORS.white,
    borderColor: COLORS.primary,
  },
  previewBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
    padding: SPACING.sm,
    borderRadius: BORDER_RADIUS.md,
    marginBottom: SPACING.md,
  },
  previewText: {
    fontSize: 12,
    color: COLORS.primaryDark,
  },
  otpActionsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: SPACING.md,
  },
  resendBtn: {
    paddingVertical: 6,
  },
  resendText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.primary,
  },
  backEditBtn: {
    paddingVertical: 6,
  },
  backEditText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.slate500,
  },
  btnRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
  },
  submitBtn: {
    backgroundColor: COLORS.primary,
    borderRadius: BORDER_RADIUS.xl,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: SPACING.sm,
    ...SHADOWS.md,
  },
  submitBtnText: {
    color: COLORS.white,
    fontSize: 14,
    fontWeight: '800',
    letterSpacing: 0.2,
  },
  footerRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: SPACING.xl,
  },
  footerText: {
    fontSize: 13,
    color: COLORS.textSecondary,
  },
  linkText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.primary,
  },
});

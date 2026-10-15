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
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { useAuth } from '../../src/context/AuthContext';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';

export default function RegisterScreen() {
  const router = useRouter();
  const { register, sendOtp, verifyOtp } = useAuth();

  // Role: jobseeker or employer
  const [role, setRole] = useState<'jobseeker' | 'employer'>('jobseeker');

  // Step 1: Form Inputs
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [companyName, setCompanyName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [skillsStr, setSkillsStr] = useState('');

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
      if (!firstName.trim() || !lastName.trim() || !trimmedEmail || !password.trim()) {
        Alert.alert('Missing Fields', 'Please fill in all required fields (Name, Email, Password).');
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

      const regRes = await register({
        first_name: firstName.trim(),
        last_name: lastName.trim(),
        email: email.trim(),
        mobile_number: phone.trim(),
        phone_number: phone.trim(),
        role,
        password,
        password_confirmation: passwordConfirmation,
        skills: skillsArray,
        ...(role === 'employer' ? { company_name: companyName.trim() } : {}),
      } as any);

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
                <Text style={styles.label}>Mobile Phone Number</Text>
                <TextInput
                  style={styles.input}
                  placeholder="09123456789"
                  placeholderTextColor={COLORS.slate400}
                  value={phone}
                  onChangeText={setPhone}
                  keyboardType="phone-pad"
                />
              </View>

              {/* Skills (Jobseeker only) */}
              {role === 'jobseeker' && (
                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Skills (Comma-separated)</Text>
                  <TextInput
                    style={styles.input}
                    placeholder="e.g. Customer Service, Excel, Welding"
                    placeholderTextColor={COLORS.slate400}
                    value={skillsStr}
                    onChangeText={setSkillsStr}
                  />
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

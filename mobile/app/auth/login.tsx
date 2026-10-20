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
  Modal,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { useAuth } from '../../src/context/AuthContext';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { getApiBaseUrl, setApiBaseUrl, getDefaultApiUrl, DEFAULT_LAN_IP, DEFAULT_PORT } from '../../src/config/api';

export default function LoginScreen() {
  const router = useRouter();
  const { login } = useAuth();

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  // Server settings modal
  const [settingsModalVisible, setSettingsModalVisible] = useState(false);
  const [customApiUrl, setCustomApiUrl] = useState('');

  const openSettings = async () => {
    const current = await getApiBaseUrl();
    setCustomApiUrl(current);
    setSettingsModalVisible(true);
  };

  const handleSaveApiUrl = async () => {
    if (customApiUrl.trim()) {
      await setApiBaseUrl(customApiUrl.trim());
      Alert.alert('Server Updated', `API Base URL set to: ${customApiUrl.trim()}`);
      setSettingsModalVisible(false);
    }
  };

  const handleResetApiUrl = async () => {
    const defaultUrl = getDefaultApiUrl();
    await setApiBaseUrl(defaultUrl);
    setCustomApiUrl(defaultUrl);
    Alert.alert('Reset to Default', `API Base URL reset to: ${defaultUrl}`);
    setSettingsModalVisible(false);
  };

  const handleLogin = async () => {
    if (!email.trim() || !password.trim()) {
      Alert.alert('Missing Fields', 'Please enter your email and password.');
      return;
    }

    setLoading(true);
    try {
      const res = await login(email.trim(), password);
      if (res.success) {
        router.replace('/(tabs)');
      } else {
        const isRestricted =
          res.message?.toLowerCase().includes('restricted') ||
          res.message?.toLowerCase().includes('jobseeker') ||
          res.message?.toLowerCase().includes('portal');
        Alert.alert(
          isRestricted ? 'Access Restricted' : 'Sign In Failed',
          res.message || 'Invalid email or password.'
        );
      }
    } catch (err: any) {
      Alert.alert('Connection Error', `${err.message || 'Unable to connect to the server.'}\n\nCheck your API Server Settings below.`);
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Header Branding */}
        <View style={styles.header}>
          <View style={styles.logoBadge}>
            <Ionicons name="briefcase" size={32} color={COLORS.primary} />
          </View>
          <Text style={styles.title}>TrabaGo Cebu</Text>
          <Text style={styles.subtitle}>DMDP Jobseeker Employment Portal</Text>
        </View>

        {/* Form Card */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Sign In</Text>
          <Text style={styles.cardSubtitle}>Access your job applications and certifications</Text>

          {/* Email Field */}
          <View style={styles.fieldGroup}>
            <Text style={styles.label}>Email Address</Text>
            <View style={styles.inputContainer}>
              <Ionicons name="mail-outline" size={19} color={COLORS.slate400} style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="jobseeker@example.com"
                placeholderTextColor={COLORS.slate400}
                value={email}
                onChangeText={setEmail}
                keyboardType="email-address"
                autoCapitalize="none"
              />
            </View>
          </View>

          {/* Password Field */}
          <View style={styles.fieldGroup}>
            <Text style={styles.label}>Password</Text>
            <View style={styles.inputContainer}>
              <Ionicons name="lock-closed-outline" size={19} color={COLORS.slate400} style={styles.inputIcon} />
              <TextInput
                style={styles.input}
                placeholder="••••••••"
                placeholderTextColor={COLORS.slate400}
                value={password}
                onChangeText={setPassword}
                secureTextEntry={!showPassword}
              />
              <TouchableOpacity onPress={() => setShowPassword(!showPassword)} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
                <Ionicons
                  name={showPassword ? 'eye-off-outline' : 'eye-outline'}
                  size={19}
                  color={COLORS.slate400}
                />
              </TouchableOpacity>
            </View>
          </View>

          {/* Submit Button */}
          <TouchableOpacity
            style={styles.submitBtn}
            activeOpacity={0.88}
            onPress={handleLogin}
            disabled={loading}>
            {loading ? (
              <ActivityIndicator color={COLORS.white} />
            ) : (
              <Text style={styles.submitBtnText}>Sign In</Text>
            )}
          </TouchableOpacity>

          {/* Jobseeker Exclusive Portal Notice */}
          <View style={styles.roleNoticeBadge}>
            <Ionicons name="information-circle-outline" size={16} color={COLORS.primary} />
            <Text style={styles.roleNoticeText}>
              Jobseeker Portal: Admins, Trainers, and JPOs must sign in via the Web Portal.
            </Text>
          </View>

          {/* Register Link */}
          <View style={styles.footerRow}>
            <Text style={styles.footerText}>Don't have an account? </Text>
            <TouchableOpacity onPress={() => router.push('/auth/register')}>
              <Text style={styles.footerLink}>Register here</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Continue as Guest */}
        <TouchableOpacity style={styles.guestLink} onPress={() => router.replace('/(tabs)')}>
          <Text style={styles.guestLinkText}>Continue as Guest</Text>
          <Ionicons name="arrow-forward" size={14} color={COLORS.slate500} />
        </TouchableOpacity>

        {/* Server / API URL Settings */}
        <TouchableOpacity style={styles.settingsLink} onPress={openSettings} activeOpacity={0.8}>
          <Ionicons name="server-outline" size={14} color={COLORS.slate400} />
          <Text style={styles.settingsLinkText}>API Server Settings</Text>
        </TouchableOpacity>
      </ScrollView>

      {/* API Server Settings Modal */}
      <Modal visible={settingsModalVisible} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.modalCard}>
            <View style={styles.modalHeader}>
              <View style={styles.modalIconBox}>
                <Ionicons name="server-outline" size={22} color={COLORS.primary} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.modalTitle}>API Server Configuration</Text>
                <Text style={styles.modalSubtitle}>Connect the mobile app to your local or staging backend</Text>
              </View>
            </View>

            <View style={styles.modalBody}>
              <Text style={styles.modalLabel}>API Base URL (ends with /api)</Text>
              <TextInput
                style={styles.modalInput}
                value={customApiUrl}
                onChangeText={setCustomApiUrl}
                placeholder="http://192.168.100.9:8000/api"
                placeholderTextColor={COLORS.slate400}
                autoCapitalize="none"
                autoCorrect={false}
              />

              <Text style={styles.presetsLabel}>Quick Presets:</Text>
              <View style={styles.presetsRow}>
                <TouchableOpacity
                  style={styles.presetChip}
                  onPress={() => setCustomApiUrl(`http://${DEFAULT_LAN_IP}:${DEFAULT_PORT}/api`)}>
                  <Text style={styles.presetChipText}>LAN ({DEFAULT_LAN_IP})</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={styles.presetChip}
                  onPress={() => setCustomApiUrl(`http://10.0.2.2:${DEFAULT_PORT}/api`)}>
                  <Text style={styles.presetChipText}>Android Emulator (10.0.2.2)</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={styles.presetChip}
                  onPress={() => setCustomApiUrl(`http://localhost:${DEFAULT_PORT}/api`)}>
                  <Text style={styles.presetChipText}>Localhost</Text>
                </TouchableOpacity>
              </View>
            </View>

            <View style={styles.modalActions}>
              <TouchableOpacity style={styles.resetBtn} onPress={handleResetApiUrl}>
                <Text style={styles.resetBtnText}>Default</Text>
              </TouchableOpacity>
              <View style={styles.modalActionsRight}>
                <TouchableOpacity style={styles.cancelBtn} onPress={() => setSettingsModalVisible(false)}>
                  <Text style={styles.cancelBtnText}>Cancel</Text>
                </TouchableOpacity>
                <TouchableOpacity style={styles.saveBtn} onPress={handleSaveApiUrl}>
                  <Text style={styles.saveBtnText}>Save</Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </View>
      </Modal>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  scrollContent: {
    flexGrow: 1,
    padding: SPACING.xl,
    justifyContent: 'center',
  },
  header: {
    alignItems: 'center',
    marginBottom: SPACING.xl,
  },
  logoBadge: {
    width: 68,
    height: 68,
    borderRadius: BORDER_RADIUS.xl,
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: SPACING.md,
    ...SHADOWS.sm,
  },
  title: {
    fontSize: 26,
    fontWeight: '800',
    color: COLORS.navy,
    letterSpacing: -0.5,
  },
  subtitle: {
    fontSize: 13,
    color: COLORS.slate500,
    marginTop: 4,
    fontWeight: '500',
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xxl,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.md,
  },
  cardTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: COLORS.navy,
  },
  cardSubtitle: {
    fontSize: 13,
    color: COLORS.slate400,
    marginTop: 2,
    marginBottom: SPACING.lg,
  },
  fieldGroup: {
    marginBottom: SPACING.md,
  },
  label: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.navy,
    marginBottom: 6,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.xl,
    paddingHorizontal: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    height: 50,
  },
  inputIcon: {
    marginRight: SPACING.sm,
  },
  input: {
    flex: 1,
    fontSize: 14,
    color: COLORS.text,
  },
  submitBtn: {
    backgroundColor: COLORS.primary,
    height: 50,
    borderRadius: BORDER_RADIUS.xl,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: SPACING.md,
    ...SHADOWS.sm,
  },
  submitBtnText: {
    color: COLORS.white,
    fontSize: 15,
    fontWeight: '700',
  },
  roleNoticeBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: COLORS.primaryUltraLight,
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: BORDER_RADIUS.lg,
    marginTop: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.primaryBorder,
  },
  roleNoticeText: {
    flex: 1,
    fontSize: 11,
    fontWeight: '600',
    color: COLORS.primaryDark,
    lineHeight: 15,
  },
  footerRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: SPACING.lg,
  },
  footerText: {
    fontSize: 13,
    color: COLORS.textSecondary,
  },
  footerLink: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.primary,
  },
  guestLink: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: SPACING.xl,
    gap: 4,
    paddingVertical: 8,
  },
  guestLinkText: {
    fontSize: 13,
    color: COLORS.slate500,
    fontWeight: '600',
  },
  settingsLink: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: SPACING.md,
    gap: 6,
    paddingVertical: 6,
  },
  settingsLinkText: {
    fontSize: 12,
    color: COLORS.slate400,
    fontWeight: '600',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.65)',
    justifyContent: 'flex-end',
  },
  modalCard: {
    backgroundColor: COLORS.white,
    borderTopLeftRadius: BORDER_RADIUS.xxl,
    borderTopRightRadius: BORDER_RADIUS.xxl,
    padding: SPACING.xl,
    ...SHADOWS.lg,
  },
  modalHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.lg,
  },
  modalIconBox: {
    width: 44,
    height: 44,
    borderRadius: BORDER_RADIUS.lg,
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: SPACING.md,
  },
  modalTitle: {
    fontSize: 17,
    fontWeight: '700',
    color: COLORS.navy,
  },
  modalSubtitle: {
    fontSize: 12,
    color: COLORS.slate400,
    marginTop: 2,
  },
  modalBody: {
    marginBottom: SPACING.lg,
  },
  modalLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.navy,
    marginBottom: 6,
  },
  modalInput: {
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.lg,
    paddingHorizontal: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    height: 46,
    fontSize: 13,
    color: COLORS.text,
  },
  presetsLabel: {
    fontSize: 11,
    fontWeight: '600',
    color: COLORS.slate400,
    marginTop: SPACING.md,
    marginBottom: 6,
  },
  presetsRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
  },
  presetChip: {
    backgroundColor: COLORS.surfaceSubtle,
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.borderLight,
  },
  presetChipText: {
    fontSize: 11,
    color: COLORS.slate600,
    fontWeight: '500',
  },
  modalActions: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: SPACING.md,
    borderTopWidth: 1,
    borderTopColor: COLORS.borderLight,
  },
  resetBtn: {
    paddingHorizontal: SPACING.md,
    paddingVertical: 8,
  },
  resetBtnText: {
    fontSize: 13,
    color: COLORS.slate400,
    fontWeight: '600',
  },
  modalActionsRight: {
    flexDirection: 'row',
    gap: SPACING.sm,
  },
  cancelBtn: {
    paddingHorizontal: SPACING.md,
    paddingVertical: 9,
    borderRadius: BORDER_RADIUS.md,
  },
  cancelBtnText: {
    fontSize: 13,
    color: COLORS.slate600,
    fontWeight: '600',
  },
  saveBtn: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.lg,
    paddingVertical: 9,
    borderRadius: BORDER_RADIUS.md,
  },
  saveBtnText: {
    fontSize: 13,
    color: COLORS.white,
    fontWeight: '700',
  },
});

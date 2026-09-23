import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  TextInput,
  Modal,
  ActivityIndicator,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { useAuth } from '../../src/context/AuthContext';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { getApiBaseUrl, setApiBaseUrl, getDefaultApiUrl, getCurrentApiUrlSync } from '../../src/config/api';

export default function ProfileScreen() {
  const router = useRouter();
  const { user, isAuthenticated, logout, addSkill, removeSkill, changePassword } = useAuth();

  // Settings modal
  const [settingsModalVisible, setSettingsModalVisible] = useState(false);
  const [customApiUrl, setCustomApiUrl] = useState('');

  // Add skill modal
  const [skillModalVisible, setSkillModalVisible] = useState(false);
  const [newSkillText, setNewSkillText] = useState('');
  const [addingSkill, setAddingSkill] = useState(false);

  // Change password modal
  const [passwordModalVisible, setPasswordModalVisible] = useState(false);
  const [currentPass, setCurrentPass] = useState('');
  const [newPass, setNewPass] = useState('');
  const [confirmPass, setConfirmPass] = useState('');
  const [changingPass, setChangingPass] = useState(false);

  const openSettings = async () => {
    const current = await getApiBaseUrl();
    setCustomApiUrl(current);
    setSettingsModalVisible(true);
  };

  const handleSaveApiUrl = async () => {
    if (customApiUrl.trim()) {
      await setApiBaseUrl(customApiUrl.trim());
      Alert.alert('API Updated', `Base URL set to: ${customApiUrl.trim()}`);
      setSettingsModalVisible(false);
    }
  };

  const handleResetApiUrl = async () => {
    const defaultUrl = getDefaultApiUrl();
    await setApiBaseUrl(defaultUrl);
    setCustomApiUrl(defaultUrl);
    Alert.alert('API Reset', `Base URL reset to default: ${defaultUrl}`);
    setSettingsModalVisible(false);
  };

  const handleAddSkill = async () => {
    if (!newSkillText.trim()) return;
    setAddingSkill(true);
    const res = await addSkill(newSkillText.trim());
    setAddingSkill(false);
    if (res.success) {
      setNewSkillText('');
      setSkillModalVisible(false);
    } else {
      Alert.alert('Error', res.message || 'Failed to add skill');
    }
  };

  const handleRemoveSkill = (skill: string) => {
    Alert.alert('Remove Skill', `Remove "${skill}" from your profile?`, [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Remove',
        style: 'destructive',
        onPress: async () => {
          await removeSkill(skill);
        },
      },
    ]);
  };

  const handleChangePassword = async () => {
    if (!currentPass || !newPass || !confirmPass) {
      Alert.alert('Required', 'Please fill in all password fields.');
      return;
    }
    if (newPass !== confirmPass) {
      Alert.alert('Mismatch', 'New passwords do not match.');
      return;
    }
    if (newPass.length < 8) {
      Alert.alert('Weak Password', 'Password must be at least 8 characters long.');
      return;
    }

    setChangingPass(true);
    const res = await changePassword(currentPass, newPass, confirmPass);
    setChangingPass(false);

    if (res.success) {
      Alert.alert('Success', 'Password changed successfully!');
      setPasswordModalVisible(false);
      setCurrentPass('');
      setNewPass('');
      setConfirmPass('');
    } else {
      Alert.alert('Error', res.message || 'Failed to change password.');
    }
  };

  const handleLogout = () => {
    Alert.alert('Sign Out', 'Are you sure you want to sign out?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Sign Out',
        style: 'destructive',
        onPress: async () => {
          await logout();
          router.replace('/(tabs)');
        },
      },
    ]);
  };

  if (!isAuthenticated || !user) {
    return (
      <View style={styles.container}>
        <View style={styles.guestCard}>
          <View style={styles.guestAvatar}>
            <Ionicons name="person-circle-outline" size={72} color={COLORS.primary} />
          </View>
          <Text style={styles.guestTitle}>DMDP Jobseeker Portal</Text>
          <Text style={styles.guestSubtitle}>
            Connect with verified employers, earn DMDP certificates, and track hiring placement across Cebu City.
          </Text>

          <TouchableOpacity style={styles.primaryBtn} onPress={() => router.push('/auth/login')}>
            <Text style={styles.primaryBtnText}>Sign In to Account</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.secondaryBtn} onPress={() => router.push('/auth/register')}>
            <Text style={styles.secondaryBtnText}>Create New Jobseeker Profile</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.settingsLink} onPress={openSettings}>
            <Ionicons name="construct-outline" size={16} color={COLORS.textSecondary} />
            <Text style={styles.settingsLinkText}>Configure Backend IP ({getCurrentApiUrlSync()})</Text>
          </TouchableOpacity>
        </View>

        {/* API Settings Modal */}
        <Modal visible={settingsModalVisible} transparent animationType="slide">
          <View style={styles.modalOverlay}>
            <View style={styles.modalContent}>
              <Text style={styles.modalTitle}>Backend API Settings</Text>
              <Text style={styles.modalSubtitle}>
                When using Expo Go on a mobile phone, point to your computer's local IP (e.g. http://192.168.100.9:8000/api).
              </Text>
              <TextInput
                style={styles.input}
                value={customApiUrl}
                onChangeText={setCustomApiUrl}
                placeholder="http://192.168.100.9:8000/api"
                autoCapitalize="none"
              />
              <View style={styles.modalButtons}>
                <TouchableOpacity style={styles.modalResetBtn} onPress={handleResetApiUrl}>
                  <Text style={styles.modalResetBtnText}>Reset Default</Text>
                </TouchableOpacity>
                <TouchableOpacity style={styles.modalSaveBtn} onPress={handleSaveApiUrl}>
                  <Text style={styles.modalSaveBtnText}>Save</Text>
                </TouchableOpacity>
              </View>
              <TouchableOpacity style={styles.modalCloseBtn} onPress={() => setSettingsModalVisible(false)}>
                <Text style={styles.modalCloseText}>Cancel</Text>
              </TouchableOpacity>
            </View>
          </View>
        </Modal>
      </View>
    );
  }

  const skillsList = Array.isArray(user.skills) ? user.skills : [];
  const strength = user.profile_strength ?? user.profileStrength ?? 50;
  const address = user.address || {};
  const education = user.education_details || {};
  const workExp = user.work_experience || {};
  const eligibility = user.eligibility || {};
  const preferences = user.preferences || {};
  const social = user.social_status || {};

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Cebu DMDP Jobseeker Digital ID Card */}
      <View style={styles.idCard}>
        <View style={styles.idCardHeader}>
          <View>
            <Text style={styles.idCardDMDP}>CITY OF CEBU • DMDP & PESD</Text>
            <Text style={styles.idCardCategory}>OFFICIAL JOBSEEKER DIGITAL ID</Text>
          </View>
          <View style={styles.idCardBadge}>
            <Ionicons name="shield-checkmark" size={14} color={COLORS.white} />
            <Text style={styles.idCardBadgeText}>VERIFIED</Text>
          </View>
        </View>

        <View style={styles.idCardBody}>
          <View style={styles.idAvatar}>
            <Text style={styles.idAvatarText}>
              {(user.firstName || user.fullName || 'J').charAt(0).toUpperCase()}
            </Text>
          </View>
          <View style={styles.idInfo}>
            <Text style={styles.idName}>{user.fullName || `${user.firstName} ${user.lastName}`}</Text>
            <Text style={styles.idRole}>
              {user.is_employed ? `Employed at ${user.hired_company || 'Employer'}` : (user.employmentStatus || 'Active Jobseeker')}
            </Text>
            <Text style={styles.idNumber}>ID: CEB-DMDP-{String(user.id || user.user_id).padStart(5, '0')}</Text>
          </View>
        </View>

        <View style={styles.idCardFooter}>
          <Text style={styles.idFooterText}>Citizenship: {user.citizenship || 'Filipino'}</Text>
          <Text style={styles.idFooterText}>Civil Status: {user.civilStatus || 'Single'}</Text>
        </View>
      </View>

      {/* Profile Strength Progress Meter (Web Sync) */}
      <View style={styles.section}>
        <View style={styles.strengthHeader}>
          <View>
            <Text style={styles.strengthLabel}>PROFILE COMPLETION METER</Text>
            <Text style={styles.strengthTitle}>{strength}% Comprehensive Score</Text>
          </View>
          <Ionicons name="speedometer-outline" size={24} color={COLORS.primary} />
        </View>
        <View style={styles.strengthBarBg}>
          <View style={[styles.strengthBarFill, { width: `${strength}%` }]} />
        </View>
      </View>

      {/* Social Status Tags (Web Sync) */}
      {(social.is_pwd || social.is_4ps || social.is_ofw) && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Social Welfare Classifications</Text>
          <View style={styles.socialRow}>
            {social.is_pwd && (
              <View style={[styles.socialBadge, { backgroundColor: COLORS.purpleBg }]}>
                <Ionicons name="accessibility" size={14} color={COLORS.purple} />
                <Text style={[styles.socialBadgeText, { color: COLORS.purple }]}>
                  PWD Registered {social.pwd_type ? `(${social.pwd_type})` : ''}
                </Text>
              </View>
            )}
            {social.is_4ps && (
              <View style={[styles.socialBadge, { backgroundColor: COLORS.infoBg }]}>
                <Ionicons name="people" size={14} color={COLORS.info} />
                <Text style={[styles.socialBadgeText, { color: COLORS.info }]}>4Ps Beneficiary</Text>
              </View>
            )}
            {social.is_ofw && (
              <View style={[styles.socialBadge, { backgroundColor: COLORS.warningBg }]}>
                <Ionicons name="airplane" size={14} color={COLORS.warning} />
                <Text style={[styles.socialBadgeText, { color: COLORS.warning }]}>OFW Returnee</Text>
              </View>
            )}
          </View>
        </View>
      )}

      {/* Personal & Contact Details */}
      <View style={styles.section}>
        <View style={styles.sectionHeaderRow}>
          <Text style={styles.sectionTitle}>Personal & Contact Details</Text>
          <TouchableOpacity onPress={() => router.push('/profile/edit')}>
            <Text style={styles.sectionLink}>Edit</Text>
          </TouchableOpacity>
        </View>
        <View style={styles.detailRow}>
          <Ionicons name="mail-outline" size={16} color={COLORS.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Email</Text>
            <Text style={styles.detailValue}>{user.email}</Text>
          </View>
        </View>
        <View style={styles.detailRow}>
          <Ionicons name="call-outline" size={16} color={COLORS.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Mobile</Text>
            <Text style={styles.detailValue}>{user.phone || user.mobile_number || 'Not provided'}</Text>
          </View>
        </View>
        <View style={styles.detailRow}>
          <Ionicons name="calendar-outline" size={16} color={COLORS.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Birth Date & Gender</Text>
            <Text style={styles.detailValue}>
              {user.birthDate || 'Not specified'} • {user.sex || 'Male'}
            </Text>
          </View>
        </View>
        <View style={styles.detailRow}>
          <Ionicons name="home-outline" size={16} color={COLORS.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Residential Address</Text>
            <Text style={styles.detailValue}>
              {address.full || `${address.street || ''} ${address.barangay || ''}, ${address.city || 'Cebu City'}, ${address.province || 'Cebu'}`.trim() || 'Cebu City, Philippines'}
            </Text>
          </View>
        </View>
      </View>

      {/* Education & Experience */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Education & Work Background</Text>
        <View style={styles.detailRow}>
          <Ionicons name="school-outline" size={16} color={COLORS.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Highest Education</Text>
            <Text style={styles.detailValue}>{education.level || user.education || 'College / Vocational Graduate'}</Text>
            {education.course && (
              <Text style={styles.detailSub}>{education.course} • {education.school} ({education.year_graduated || 'Graduated'})</Text>
            )}
          </View>
        </View>
        <View style={styles.detailRow}>
          <Ionicons name="briefcase-outline" size={16} color={COLORS.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Work Experience</Text>
            <Text style={styles.detailValue}>{workExp.position || 'Registered Jobseeker'}</Text>
            {workExp.company && (
              <Text style={styles.detailSub}>{workExp.company} ({workExp.duration || 'Previous'})</Text>
            )}
          </View>
        </View>
        {(eligibility.civil_service || eligibility.prc_license || eligibility.tesda_nc || eligibility.driver_license) && (
          <View style={styles.detailRow}>
            <Ionicons name="ribbon-outline" size={16} color={COLORS.primary} />
            <View style={styles.detailContent}>
              <Text style={styles.detailLabel}>Professional Licenses & Eligibility</Text>
              <Text style={styles.detailValue}>
                {[
                  eligibility.civil_service ? `Civil Service (${eligibility.civil_service})` : null,
                  eligibility.prc_license ? `PRC License (${eligibility.prc_license})` : null,
                  eligibility.tesda_nc ? `TESDA NC (${eligibility.tesda_nc})` : null,
                  eligibility.driver_license ? `Driver's License (${eligibility.driver_license})` : null,
                ].filter(Boolean).join(' • ')}
              </Text>
            </View>
          </View>
        )}
      </View>

      {/* Career Preferences */}
      {(preferences.occupation1 || preferences.industry1 || preferences.salary_expectation) && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Career & Job Preferences</Text>
          <View style={styles.detailRow}>
            <Ionicons name="star-outline" size={16} color={COLORS.primary} />
            <View style={styles.detailContent}>
              <Text style={styles.detailLabel}>Desired Roles</Text>
              <Text style={styles.detailValue}>
                {[preferences.occupation1, preferences.occupation2].filter(Boolean).join(', ') || 'Any matched role'}
              </Text>
            </View>
          </View>
          {preferences.industry1 && (
            <View style={styles.detailRow}>
              <Ionicons name="business-outline" size={16} color={COLORS.primary} />
              <View style={styles.detailContent}>
                <Text style={styles.detailLabel}>Preferred Industry</Text>
                <Text style={styles.detailValue}>{preferences.industry1}</Text>
              </View>
            </View>
          )}
          {preferences.salary_expectation && (
            <View style={styles.detailRow}>
              <Ionicons name="cash-outline" size={16} color={COLORS.primary} />
              <View style={styles.detailContent}>
                <Text style={styles.detailLabel}>Expected Compensation</Text>
                <Text style={styles.detailValue}>{preferences.salary_expectation}</Text>
              </View>
            </View>
          )}
        </View>
      )}

      {/* Skills Matrix */}
      <View style={styles.section}>
        <View style={styles.sectionHeaderRow}>
          <Text style={styles.sectionTitle}>Skills Matrix ({skillsList.length})</Text>
          <TouchableOpacity onPress={() => setSkillModalVisible(true)}>
            <Text style={styles.sectionLink}>+ Add Skill</Text>
          </TouchableOpacity>
        </View>
        {skillsList.length > 0 ? (
          <View style={styles.chipsContainer}>
            {skillsList.map((skill, index) => (
              <TouchableOpacity
                key={index}
                style={styles.chip}
                onLongPress={() => handleRemoveSkill(skill)}>
                <Ionicons name="checkmark-circle" size={13} color={COLORS.primary} style={{ marginRight: 4 }} />
                <Text style={styles.chipText}>{skill}</Text>
                <TouchableOpacity onPress={() => handleRemoveSkill(skill)} style={{ marginLeft: 4 }}>
                  <Ionicons name="close-circle" size={14} color={COLORS.textMuted} />
                </TouchableOpacity>
              </TouchableOpacity>
            ))}
          </View>
        ) : (
          <Text style={styles.emptySkillsText}>
            No skills added yet. Tap "+ Add Skill" or "Edit Profile" to list your competencies.
          </Text>
        )}
      </View>

      {/* Jobseeker Services & Actions */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Jobseeker Services</Text>

        <TouchableOpacity
          style={styles.menuItem}
          onPress={() => router.push('/profile/vault')}>
          <View style={[styles.menuIcon, { backgroundColor: COLORS.infoBg }]}>
            <Ionicons name="folder-open-outline" size={20} color={COLORS.info} />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.menuTitle}>Document Hub & Verification Vault</Text>
            <Text style={styles.menuSubtitle}>Manage Resumes, Valid IDs, and DMDP Certificates</Text>
          </View>
          <Ionicons name="chevron-forward" size={18} color={COLORS.textMuted} />
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.menuItem}
          onPress={() => router.push('/profile/edit')}>
          <View style={[styles.menuIcon, { backgroundColor: COLORS.purpleBg }]}>
            <Ionicons name="create-outline" size={20} color={COLORS.purple} />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.menuTitle}>Comprehensive Profile Editor</Text>
            <Text style={styles.menuSubtitle}>Edit all 8 personal, education, and career sections</Text>
          </View>
          <Ionicons name="chevron-forward" size={18} color={COLORS.textMuted} />
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.menuItem}
          onPress={() => setPasswordModalVisible(true)}>
          <View style={[styles.menuIcon, { backgroundColor: COLORS.warningBg }]}>
            <Ionicons name="key-outline" size={20} color={COLORS.warning} />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.menuTitle}>Account Security</Text>
            <Text style={styles.menuSubtitle}>Change your account login password</Text>
          </View>
          <Ionicons name="chevron-forward" size={18} color={COLORS.textMuted} />
        </TouchableOpacity>

        <TouchableOpacity style={styles.menuItem} onPress={openSettings}>
          <View style={[styles.menuIcon, { backgroundColor: COLORS.surfaceSubtle }]}>
            <Ionicons name="construct-outline" size={20} color={COLORS.textSecondary} />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.menuTitle}>Backend Connection IP</Text>
            <Text style={styles.menuSubtitle}>{getCurrentApiUrlSync()}</Text>
          </View>
          <Ionicons name="chevron-forward" size={18} color={COLORS.textMuted} />
        </TouchableOpacity>
      </View>

      {/* Sign Out Button */}
      <View style={styles.logoutContainer}>
        <TouchableOpacity style={styles.logoutBtn} onPress={handleLogout}>
          <Ionicons name="log-out-outline" size={18} color={COLORS.danger} />
          <Text style={styles.logoutBtnText}>Sign Out from TrabaGo</Text>
        </TouchableOpacity>
      </View>

      <View style={{ height: 40 }} />

      {/* Add Skill Modal */}
      <Modal visible={skillModalVisible} transparent animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Add Skill to Profile</Text>
            <Text style={styles.modalSubtitle}>Enter a technical or vocational competence:</Text>
            <TextInput
              style={styles.input}
              placeholder="e.g. Graphic Design, Auto Mechanics, Accounting"
              value={newSkillText}
              onChangeText={setNewSkillText}
            />
            <View style={styles.modalButtons}>
              <TouchableOpacity
                style={styles.modalCancelBtn}
                onPress={() => setSkillModalVisible(false)}>
                <Text style={styles.modalCancelText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.modalSaveBtn}
                onPress={handleAddSkill}
                disabled={addingSkill}>
                {addingSkill ? (
                  <ActivityIndicator color={COLORS.white} />
                ) : (
                  <Text style={styles.modalSaveBtnText}>Add Skill</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* Change Password Modal (Exact Web Parity) */}
      <Modal visible={passwordModalVisible} transparent animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Change Account Password</Text>
            <Text style={styles.modalSubtitle}>Update your TrabaGo login credentials:</Text>
            
            <Text style={styles.modalFieldLabel}>Current Password *</Text>
            <TextInput
              style={styles.input}
              placeholder="••••••••"
              value={currentPass}
              onChangeText={setCurrentPass}
              secureTextEntry
            />

            <Text style={styles.modalFieldLabel}>New Password (Min 8 chars) *</Text>
            <TextInput
              style={styles.input}
              placeholder="••••••••"
              value={newPass}
              onChangeText={setNewPass}
              secureTextEntry
            />

            <Text style={styles.modalFieldLabel}>Confirm New Password *</Text>
            <TextInput
              style={styles.input}
              placeholder="••••••••"
              value={confirmPass}
              onChangeText={setConfirmPass}
              secureTextEntry
            />

            <View style={styles.modalButtons}>
              <TouchableOpacity
                style={styles.modalCancelBtn}
                onPress={() => setPasswordModalVisible(false)}>
                <Text style={styles.modalCancelText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.modalSaveBtn}
                onPress={handleChangePassword}
                disabled={changingPass}>
                {changingPass ? (
                  <ActivityIndicator color={COLORS.white} />
                ) : (
                  <Text style={styles.modalSaveBtnText}>Update Password</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  guestCard: {
    margin: SPACING.lg,
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.md,
  },
  guestAvatar: {
    marginBottom: SPACING.md,
  },
  guestTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.navy,
    textAlign: 'center',
  },
  guestSubtitle: {
    fontSize: 13,
    color: COLORS.textSecondary,
    textAlign: 'center',
    marginTop: SPACING.sm,
    lineHeight: 18,
    marginBottom: SPACING.xl,
  },
  primaryBtn: {
    backgroundColor: COLORS.primary,
    width: '100%',
    paddingVertical: 14,
    borderRadius: BORDER_RADIUS.md,
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  primaryBtnText: {
    color: COLORS.white,
    fontSize: 15,
    fontWeight: 'bold',
  },
  secondaryBtn: {
    backgroundColor: COLORS.surfaceSubtle,
    width: '100%',
    paddingVertical: 14,
    borderRadius: BORDER_RADIUS.md,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.border,
    marginBottom: SPACING.lg,
  },
  secondaryBtnText: {
    color: COLORS.navy,
    fontSize: 14,
    fontWeight: '600',
  },
  settingsLink: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingTop: SPACING.sm,
  },
  settingsLinkText: {
    fontSize: 12,
    color: COLORS.textSecondary,
    textDecorationLine: 'underline',
  },
  idCard: {
    backgroundColor: COLORS.navy,
    margin: SPACING.lg,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    ...SHADOWS.lg,
  },
  idCardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255,255,255,0.15)',
    paddingBottom: SPACING.md,
  },
  idCardDMDP: {
    color: COLORS.primaryLight,
    fontSize: 11,
    fontWeight: 'bold',
    letterSpacing: 1,
  },
  idCardCategory: {
    color: COLORS.white,
    fontSize: 13,
    fontWeight: 'bold',
    marginTop: 2,
  },
  idCardBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primary,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
    gap: 4,
  },
  idCardBadgeText: {
    color: COLORS.white,
    fontSize: 10,
    fontWeight: 'bold',
  },
  idCardBody: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: SPACING.lg,
  },
  idAvatar: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: COLORS.primaryLight,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: SPACING.md,
  },
  idAvatarText: {
    color: COLORS.white,
    fontSize: 26,
    fontWeight: 'bold',
  },
  idInfo: {
    flex: 1,
  },
  idName: {
    color: COLORS.white,
    fontSize: 18,
    fontWeight: 'bold',
  },
  idRole: {
    color: COLORS.primaryUltraLight,
    fontSize: 13,
    marginTop: 2,
  },
  idNumber: {
    color: COLORS.textMuted,
    fontSize: 11,
    marginTop: 4,
    fontFamily: 'monospace',
  },
  idCardFooter: {
    borderTopWidth: 1,
    borderTopColor: 'rgba(255,255,255,0.15)',
    paddingTop: SPACING.sm,
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  idFooterText: {
    color: COLORS.textMuted,
    fontSize: 11,
  },
  strengthHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  strengthLabel: {
    fontSize: 10,
    fontWeight: 'bold',
    color: COLORS.primary,
    letterSpacing: 0.5,
  },
  strengthTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginTop: 2,
  },
  strengthBarBg: {
    height: 8,
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: 4,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  strengthBarFill: {
    height: '100%',
    backgroundColor: COLORS.primary,
    borderRadius: 4,
  },
  socialRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.xs,
    marginTop: SPACING.xs,
  },
  socialBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: SPACING.md,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.full,
  },
  socialBadgeText: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  section: {
    backgroundColor: COLORS.white,
    marginHorizontal: SPACING.lg,
    marginBottom: SPACING.md,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: SPACING.sm,
  },
  sectionLink: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    paddingVertical: SPACING.xs,
    gap: SPACING.md,
  },
  detailContent: {
    flex: 1,
  },
  detailLabel: {
    fontSize: 11,
    color: COLORS.textMuted,
  },
  detailValue: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.navy,
    marginTop: 1,
  },
  detailSub: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  chipsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.xs,
    marginTop: SPACING.xs,
  },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: SPACING.md,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
    borderColor: 'rgba(5, 150, 105, 0.2)',
  },
  chipText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.primaryDark,
  },
  emptySkillsText: {
    fontSize: 12,
    color: COLORS.textMuted,
    fontStyle: 'italic',
    marginTop: 4,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: SPACING.sm,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.borderLight,
  },
  menuIcon: {
    width: 36,
    height: 36,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: SPACING.md,
  },
  menuTitle: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  menuSubtitle: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  logoutContainer: {
    marginHorizontal: SPACING.lg,
    marginTop: SPACING.sm,
  },
  logoutBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: COLORS.white,
    paddingVertical: 14,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.dangerBg,
  },
  logoutBtnText: {
    color: COLORS.danger,
    fontSize: 14,
    fontWeight: 'bold',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.xl,
  },
  modalContent: {
    backgroundColor: COLORS.white,
    width: '100%',
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    ...SHADOWS.md,
  },
  modalTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: 4,
  },
  modalSubtitle: {
    fontSize: 12,
    color: COLORS.textSecondary,
    marginBottom: SPACING.md,
    lineHeight: 16,
  },
  modalFieldLabel: {
    fontSize: 11,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: 4,
  },
  input: {
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.md,
    paddingHorizontal: SPACING.md,
    height: 44,
    borderWidth: 1,
    borderColor: COLORS.border,
    fontSize: 13,
    color: COLORS.text,
    marginBottom: SPACING.md,
  },
  modalButtons: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    gap: SPACING.md,
    marginTop: SPACING.sm,
  },
  modalCancelBtn: {
    paddingVertical: 10,
    paddingHorizontal: SPACING.lg,
  },
  modalCancelText: {
    color: COLORS.textSecondary,
    fontSize: 13,
    fontWeight: '600',
  },
  modalResetBtn: {
    paddingVertical: 10,
    paddingHorizontal: SPACING.md,
  },
  modalResetBtnText: {
    color: COLORS.danger,
    fontSize: 13,
    fontWeight: '600',
  },
  modalSaveBtn: {
    backgroundColor: COLORS.primary,
    paddingVertical: 10,
    paddingHorizontal: SPACING.xl,
    borderRadius: BORDER_RADIUS.md,
  },
  modalSaveBtnText: {
    color: COLORS.white,
    fontSize: 13,
    fontWeight: 'bold',
  },
  modalCloseBtn: {
    alignItems: 'center',
    marginTop: SPACING.md,
  },
  modalCloseText: {
    color: COLORS.textSecondary,
    fontSize: 12,
  },
});

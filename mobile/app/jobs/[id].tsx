import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  Platform,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { jobService } from '../../src/services/jobService';
import { JobItem } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function JobDetailsScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const { isAuthenticated, user } = useAuth();

  const isAccountVerified = Boolean(
    user?.account_status === 'verified' ||
    user?.accountStatus === 'verified' ||
    (user?.is_approved && (user?.is_verified ?? user?.isVerified))
  );

  const [job, setJob] = useState<JobItem | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    if (id) {
      setLoading(true);
      jobService.getJobById(id)
        .then((data) => {
          setJob(data);
        })
        .catch((err) => {
          console.warn('[ErrorHandling] Failed to fetch job details:', err);
          setJob(null);
        })
        .finally(() => {
          setLoading(false);
        });
    }
  }, [id]);

  const handleApplyPress = () => {
    if (!isAuthenticated) {
      router.push('/auth/login');
      return;
    }

    const isApproved = Boolean(user?.isApproved ?? user?.is_approved);
    if (!isApproved) {
      Alert.alert(
        'Account Pending Approval',
        'Your jobseeker account is pending approval by DMDP. You cannot apply for job openings until your account is approved.',
        [{ text: 'OK' }]
      );
      return;
    }

    const isVerified = Boolean(user?.is_verified ?? user?.isVerified);
    if (!isVerified) {
      const missing = user?.missing_requirements && user.missing_requirements.length > 0
        ? user.missing_requirements.join('\n• ')
        : 'Resume & Valid ID';

      Alert.alert(
        'Application Locked',
        `Jobseekers cannot apply for a job opening until profile details and required documents are completed.\n\nMissing Requirements:\n• ${missing}`,
        [
          { text: 'Cancel', style: 'cancel' },
          { text: 'Complete Profile', onPress: () => router.push('/profile/edit') },
          { text: 'Document Vault', onPress: () => router.push('/profile/vault') },
        ]
      );
      return;
    }

    router.push({
      pathname: '/jobs/apply',
      params: {
        jobId: job?.id || job?.job_id || id,
        jobTitle: job?.title,
        company: job?.company || job?.company_name,
      },
    });
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading position details...</Text>
      </View>
    );
  }

  if (!job) {
    return (
      <View style={styles.centerContainer}>
        <View style={styles.emptyIconCircle}>
          <Ionicons name="alert-circle-outline" size={36} color={COLORS.danger} />
        </View>
        <Text style={styles.emptyTitle}>Job Not Found</Text>
        <Text style={styles.emptySubtitle}>This job posting may have expired or been removed.</Text>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Text style={styles.backBtnText}>Browse Other Openings</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const requirementsList = Array.isArray(job.requirements) && job.requirements.length > 0
    ? job.requirements
    : (job.qualifications ? job.qualifications.split('\n').filter(Boolean) : []);
  const requiredSkills = Array.isArray(job.required_skills) && job.required_skills.length > 0
    ? job.required_skills
    : (Array.isArray(job.skills) ? job.skills : []);
  const matchDetails = job.match_details;
  const matchedSkills = matchDetails?.matchedSkills ?? job.matched_skills ?? [];
  const missingSkills = matchDetails?.missingSkills ?? job.missing_skills ?? [];
  const benefitsList = Array.isArray(job.benefits)
    ? job.benefits
    : (job.benefits ? job.benefits.split(/[\r\n,;]+/).map((item) => item.trim()).filter(Boolean) : []);

  const companyName = job.company || job.company_name || 'DMDP Partner Employer';
  const companyInitial = companyName.charAt(0).toUpperCase();

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Header Hero Card */}
        <View style={styles.headerCard}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>{companyInitial}</Text>
          </View>
          <Text style={styles.title}>{job.title}</Text>
          <View style={styles.companyRow}>
            <Text style={styles.company}>{companyName}</Text>
            <Ionicons name="shield-checkmark" size={15} color={COLORS.primary} style={{ marginLeft: 4 }} />
          </View>

          <View style={styles.locationRow}>
            <Ionicons name="location-outline" size={15} color={COLORS.slate500} />
            <Text style={styles.locationText}>{job.location || 'Cebu City, Philippines'}</Text>
          </View>

          {/* Quick Metrics Grid */}
          <View style={styles.metricsGrid}>
            <View style={styles.metricCard}>
              <View style={styles.metricIconWrap}>
                <Ionicons name="cash-outline" size={16} color={COLORS.emerald700} />
              </View>
              <Text style={styles.metricLabel}>Salary</Text>
              <Text style={styles.metricValue} numberOfLines={1}>
                {job.salary || job.salary_expectation || 'Not specified'}
              </Text>
            </View>

            <View style={styles.metricCard}>
              <View style={styles.metricIconWrap}>
                <Ionicons name="briefcase-outline" size={16} color={COLORS.navy} />
              </View>
              <Text style={styles.metricLabel}>Type</Text>
              <Text style={styles.metricValue} numberOfLines={1}>
                {job.type || job.employment_type || 'Not specified'}
              </Text>
            </View>

            <View style={styles.metricCard}>
              <View style={styles.metricIconWrap}>
                <Ionicons name="people-outline" size={16} color={COLORS.info} />
              </View>
              <Text style={styles.metricLabel}>Slots</Text>
              <Text style={styles.metricValue} numberOfLines={1}>
                {job.vacancy_count || 1} available
              </Text>
            </View>
          </View>

          {/* PWD Inclusive Banner */}
          {job.accepts_disability && (
            <View style={styles.pwdBanner}>
              <Ionicons name="accessibility" size={18} color={COLORS.purple} />
              <View style={{ flex: 1, marginLeft: 8 }}>
                <Text style={styles.pwdTitle}>PWD & Inclusive Workplace</Text>
                <Text style={styles.pwdDesc}>
                  Employer warmly welcomes applicants with disabilities
                  {job.disability_type ? ` (${job.disability_type})` : ''}.
                </Text>
              </View>
            </View>
          )}
        </View>

        {/* DMDP Accreditation Badge */}
        <View style={styles.verifiedCard}>
          <View style={styles.verifiedIconWrap}>
            <Ionicons name="shield-checkmark" size={20} color={COLORS.emerald700} />
          </View>
          <View style={{ flex: 1, marginLeft: 10 }}>
            <Text style={styles.verifiedTitle}>DMDP Verified Employer</Text>
            <Text style={styles.verifiedText}>
              This opening complies with Cebu City Public Employment Service Division labor standards.
            </Text>
          </View>
        </View>

        {/* Description Section */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Ionicons name="information-circle-outline" size={18} color={COLORS.primary} />
            <Text style={styles.sectionTitle}>Job Overview</Text>
          </View>
          <Text style={styles.bodyText}>
            {job.description || 'No detailed description provided by the employer.'}
          </Text>
        </View>

        {/* Requirements Section */}
        {requirementsList.length > 0 && (
          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Ionicons name="checkbox-outline" size={18} color={COLORS.primary} />
              <Text style={styles.sectionTitle}>Qualifications & Requirements</Text>
            </View>
            {requirementsList.map((req, index) => (
              <View key={index} style={styles.reqRow}>
                <View style={styles.reqCheckCircle}>
                  <Ionicons name="checkmark" size={12} color={COLORS.emerald700} />
                </View>
                <Text style={styles.reqText}>{req}</Text>
              </View>
            ))}
          </View>
        )}

        {requiredSkills.length > 0 && (
          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Ionicons name="construct-outline" size={18} color={COLORS.primary} />
              <Text style={styles.sectionTitle}>Required Skills</Text>
            </View>
            <View style={styles.skillChips}>
              {requiredSkills.map((skill, index) => (
                <View key={`${skill}-${index}`} style={styles.requiredSkillChip}>
                  <Text style={styles.requiredSkillText}>{skill}</Text>
                </View>
              ))}
            </View>
          </View>
        )}

        {matchDetails && (
          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Ionicons name="analytics-outline" size={18} color={COLORS.primary} />
              <Text style={styles.sectionTitle}>Skill Compatibility Breakdown</Text>
            </View>
            <View style={styles.matchSummary}>
              <Text style={styles.matchPercentage}>{matchDetails.percentage}%</Text>
              <View style={{ flex: 1 }}>
                <Text style={styles.matchTier}>{matchDetails.tier}</Text>
                <Text style={styles.matchCaption}>
                  {matchDetails.matchedCount} of {matchDetails.totalRequired} required skills matched
                </Text>
              </View>
            </View>
            <View style={styles.matchLists}>
              <View style={styles.matchListColumn}>
                <Text style={styles.matchListTitle}>Your matching skills</Text>
                {matchedSkills.length > 0 ? matchedSkills.map((skill, index) => (
                  <View key={`matched-${skill}-${index}`} style={styles.matchSkillRow}>
                    <Ionicons name="checkmark-circle" size={16} color={COLORS.emerald700} />
                    <Text style={styles.matchSkillText}>{skill}</Text>
                  </View>
                )) : <Text style={styles.emptyMatchText}>No required skills matched yet.</Text>}
              </View>
              <View style={styles.matchListColumn}>
                <Text style={styles.matchListTitle}>Skills to develop</Text>
                {missingSkills.length > 0 ? missingSkills.map((skill, index) => (
                  <View key={`missing-${skill}-${index}`} style={styles.matchSkillRow}>
                    <Ionicons name="add-circle-outline" size={16} color={COLORS.slate500} />
                    <Text style={styles.matchSkillText}>{skill}</Text>
                  </View>
                )) : <Text style={styles.emptyMatchText}>You meet all listed skills.</Text>}
              </View>
            </View>
          </View>
        )}

        {benefitsList.length > 0 && (
          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Ionicons name="gift-outline" size={18} color={COLORS.primary} />
              <Text style={styles.sectionTitle}>Benefits & Perks</Text>
            </View>
            {benefitsList.map((benefit, index) => (
              <View key={`benefit-${index}`} style={styles.reqRow}>
                <Ionicons name="checkmark-circle-outline" size={18} color={COLORS.emerald700} />
                <Text style={styles.reqText}>{benefit}</Text>
              </View>
            ))}
          </View>
        )}

        {!!job.other_instructions?.trim() && (
          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Ionicons name="chatbox-ellipses-outline" size={18} color={COLORS.primary} />
              <Text style={styles.sectionTitle}>Application Instructions</Text>
            </View>
            <Text style={styles.bodyText}>{job.other_instructions}</Text>
          </View>
        )}

        {/* Account Verification Prompt */}
        {isAuthenticated && !isAccountVerified && (
          <TouchableOpacity
            style={styles.unverifiedBanner}
            activeOpacity={0.88}
            onPress={() => router.push('/profile/edit')}>
            <View style={styles.unverifiedIconCircle}>
              <Ionicons name="lock-closed" size={18} color={COLORS.warning} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.unverifiedTitle}>Account Verification Required</Text>
              <Text style={styles.unverifiedText}>Complete your NSRP profile & documents to unlock job applications.</Text>
            </View>
            <Ionicons name="chevron-forward" size={16} color={COLORS.warning} />
          </TouchableOpacity>
        )}

        <View style={{ height: 110 }} />
      </ScrollView>

      {/* Floating Bottom Action Bar */}
      <View style={styles.bottomBar}>
        <View style={styles.bottomBarInfo}>
          <Text style={styles.bottomBarLabel}>Compensation</Text>
          <Text style={styles.bottomBarSalary} numberOfLines={1}>
            {job.salary || job.salary_expectation || 'Not specified'}
          </Text>
        </View>

        {isAuthenticated && !isAccountVerified ? (
          <TouchableOpacity
            style={[styles.applyBtn, styles.lockedApplyBtn]}
            activeOpacity={0.88}
            onPress={() => router.push('/profile/edit')}>
            <Ionicons name="lock-closed" size={15} color={COLORS.slate500} />
            <Text style={[styles.applyBtnText, { color: COLORS.slate600 }]}>
              Verify Profile to Apply
            </Text>
          </TouchableOpacity>
        ) : (
          <TouchableOpacity style={styles.applyBtn} activeOpacity={0.88} onPress={handleApplyPress}>
            <Text style={styles.applyBtnText}>
              {isAuthenticated ? 'Apply Now' : 'Sign In to Apply'}
            </Text>
            <Ionicons name="send" size={14} color={COLORS.white} />
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  scrollContent: {
    padding: SPACING.lg,
    gap: SPACING.md,
  },
  headerCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  avatar: {
    width: 64,
    height: 64,
    borderRadius: BORDER_RADIUS.xl,
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1.5,
    borderColor: COLORS.emerald200,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  avatarText: {
    fontSize: 26,
    fontWeight: '800',
    color: COLORS.primaryDark,
  },
  title: {
    fontSize: 20,
    fontWeight: '800',
    color: COLORS.navy,
    textAlign: 'center',
    letterSpacing: -0.3,
  },
  companyRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 4,
  },
  company: {
    fontSize: 14,
    color: COLORS.textSecondary,
    fontWeight: '600',
  },
  locationRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: 6,
  },
  locationText: {
    fontSize: 13,
    color: COLORS.slate500,
  },
  metricsGrid: {
    flexDirection: 'row',
    width: '100%',
    gap: SPACING.sm,
    marginTop: SPACING.lg,
  },
  metricCard: {
    flex: 1,
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.borderLight,
  },
  metricIconWrap: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: COLORS.white,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 4,
    ...SHADOWS.sm,
  },
  metricLabel: {
    fontSize: 10,
    color: COLORS.slate500,
    textTransform: 'uppercase',
    fontWeight: '700',
    letterSpacing: 0.3,
  },
  metricValue: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.navy,
    marginTop: 2,
    textAlign: 'center',
  },
  pwdBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.purpleBg,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    marginTop: SPACING.md,
    width: '100%',
    borderWidth: 1,
    borderColor: COLORS.purpleBorder,
  },
  pwdTitle: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.purple,
  },
  pwdDesc: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
    lineHeight: 16,
  },
  verifiedCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.emerald50,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  verifiedIconWrap: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: COLORS.white,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  verifiedTitle: {
    fontSize: 13,
    fontWeight: '800',
    color: COLORS.emerald800,
  },
  verifiedText: {
    fontSize: 11,
    color: COLORS.slate600,
    marginTop: 2,
    lineHeight: 16,
  },
  section: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: SPACING.md,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: COLORS.navy,
  },
  bodyText: {
    fontSize: 13,
    color: COLORS.textSecondary,
    lineHeight: 21,
  },
  reqRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: SPACING.sm,
    marginBottom: SPACING.sm,
  },
  reqCheckCircle: {
    width: 20,
    height: 20,
    borderRadius: 10,
    backgroundColor: COLORS.emerald50,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 2,
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  reqText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.textSecondary,
    lineHeight: 20,
  },
  skillChips: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.sm,
  },
  requiredSkillChip: {
    backgroundColor: COLORS.emerald50,
    borderColor: COLORS.emerald200,
    borderWidth: 1,
    borderRadius: BORDER_RADIUS.md,
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.sm,
  },
  requiredSkillText: {
    color: COLORS.emerald800,
    fontSize: 12,
    fontWeight: '700',
  },
  matchSummary: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.md,
    backgroundColor: COLORS.emerald50,
    borderColor: COLORS.emerald200,
    borderWidth: 1,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
  },
  matchPercentage: {
    color: COLORS.emerald700,
    fontSize: 24,
    fontWeight: '800',
  },
  matchTier: {
    color: COLORS.navy,
    fontSize: 13,
    fontWeight: '700',
  },
  matchCaption: {
    color: COLORS.slate600,
    fontSize: 11,
    marginTop: 2,
  },
  matchLists: {
    gap: SPACING.md,
    marginTop: SPACING.md,
  },
  matchListColumn: {
    gap: SPACING.xs,
  },
  matchListTitle: {
    color: COLORS.slate600,
    fontSize: 11,
    fontWeight: '700',
    marginBottom: 2,
  },
  matchSkillRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
  },
  matchSkillText: {
    color: COLORS.textSecondary,
    flex: 1,
    fontSize: 12,
    lineHeight: 18,
  },
  emptyMatchText: {
    color: COLORS.slate500,
    fontSize: 12,
    fontStyle: 'italic',
  },
  bottomBar: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: COLORS.white,
    borderTopWidth: 1,
    borderTopColor: COLORS.borderLight,
    paddingHorizontal: SPACING.xl,
    paddingTop: SPACING.md,
    paddingBottom: Platform.OS === 'ios' ? 28 : SPACING.md,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    ...SHADOWS.lg,
  },
  bottomBarInfo: {
    flex: 1,
  },
  bottomBarLabel: {
    fontSize: 11,
    color: COLORS.slate400,
    fontWeight: '500',
  },
  bottomBarSalary: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.emerald700,
  },
  applyBtn: {
    backgroundColor: COLORS.primary,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingHorizontal: 22,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.xl,
    ...SHADOWS.sm,
  },
  applyBtnText: {
    color: COLORS.white,
    fontSize: 14,
    fontWeight: '700',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.xxl,
  },
  loadingText: {
    marginTop: SPACING.md,
    fontSize: 14,
    color: COLORS.textSecondary,
    fontWeight: '500',
  },
  emptyIconCircle: {
    width: 72,
    height: 72,
    borderRadius: 36,
    backgroundColor: COLORS.surfaceSubtle,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.md,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: COLORS.navy,
  },
  emptySubtitle: {
    fontSize: 13,
    color: COLORS.textMuted,
    textAlign: 'center',
    marginTop: SPACING.xs,
  },
  backBtn: {
    marginTop: SPACING.lg,
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.xl,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
  },
  backBtnText: {
    color: COLORS.white,
    fontWeight: '700',
  },
  unverifiedBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.warningBg,
    borderWidth: 1,
    borderColor: COLORS.warningBorder,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.md,
    gap: SPACING.sm,
    marginTop: SPACING.xs,
  },
  unverifiedIconCircle: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: COLORS.white,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: COLORS.warningBorder,
  },
  unverifiedTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.warning,
  },
  unverifiedText: {
    fontSize: 12,
    color: COLORS.slate600,
    marginTop: 2,
  },
  lockedApplyBtn: {
    backgroundColor: COLORS.slate200,
    borderWidth: 1,
    borderColor: COLORS.border,
    shadowOpacity: 0,
    elevation: 0,
  },
});

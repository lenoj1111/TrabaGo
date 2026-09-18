import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
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
  const { isAuthenticated } = useAuth();

  const [job, setJob] = useState<JobItem | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    if (id) {
      jobService.getJobById(id).then((data) => {
        setJob(data);
        setLoading(false);
      });
    }
  }, [id]);

  const handleApplyPress = () => {
    if (!isAuthenticated) {
      router.push('/auth/login');
    } else {
      router.push({
        pathname: '/jobs/apply',
        params: {
          jobId: job?.id || job?.job_id || id,
          jobTitle: job?.title,
          company: job?.company || job?.company_name,
        },
      });
    }
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading job details...</Text>
      </View>
    );
  }

  if (!job) {
    return (
      <View style={styles.centerContainer}>
        <Ionicons name="alert-circle-outline" size={64} color={COLORS.danger} />
        <Text style={styles.emptyTitle}>Job Not Found</Text>
        <Text style={styles.emptySubtitle}>This job posting may have been closed or removed.</Text>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Text style={styles.backBtnText}>Back to Jobs</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const requirementsList = Array.isArray(job.requirements) && job.requirements.length > 0
    ? job.requirements
    : (job.qualifications ? job.qualifications.split('\n').filter(Boolean) : []);

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Header Card */}
        <View style={styles.headerCard}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>
              {(job.company || job.company_name || 'DMDP').charAt(0).toUpperCase()}
            </Text>
          </View>
          <Text style={styles.title}>{job.title}</Text>
          <Text style={styles.company}>{job.company || job.company_name || 'DMDP Partner Employer'}</Text>

          <View style={styles.locationRow}>
            <Ionicons name="location-outline" size={16} color={COLORS.textMuted} />
            <Text style={styles.locationText}>{job.location || 'Cebu City, Philippines'}</Text>
          </View>

          {/* Quick Metrics */}
          <View style={styles.metricsGrid}>
            <View style={styles.metricItem}>
              <Text style={styles.metricLabel}>Monthly Compensation</Text>
              <Text style={styles.metricValue}>{job.salary || job.salary_expectation || 'Competitive'}</Text>
            </View>
            <View style={styles.metricItem}>
              <Text style={styles.metricLabel}>Job Type</Text>
              <Text style={styles.metricValue}>{job.type || job.employment_type || 'Full-time'}</Text>
            </View>
            <View style={styles.metricItem}>
              <Text style={styles.metricLabel}>Available Openings</Text>
              <Text style={styles.metricValue}>{job.vacancy_count || 1} slots</Text>
            </View>
          </View>

          {job.accepts_disability && (
            <View style={styles.pwdBanner}>
              <Ionicons name="accessibility" size={18} color={COLORS.purple} />
              <View style={{ flex: 1, marginLeft: 8 }}>
                <Text style={styles.pwdTitle}>PWD & Inclusive Workplace</Text>
                <Text style={styles.pwdDesc}>
                  Employer accepts persons with disability applications
                  {job.disability_type ? ` (${job.disability_type})` : ''}.
                </Text>
              </View>
            </View>
          )}
        </View>

        {/* DMDP Accreditation Badge */}
        <View style={styles.verifiedCard}>
          <Ionicons name="shield-checkmark" size={24} color={COLORS.primary} />
          <View style={{ flex: 1, marginLeft: 12 }}>
            <Text style={styles.verifiedTitle}>DMDP Verified Employer</Text>
            <Text style={styles.verifiedText}>
              This position complies with Cebu City Public Employment Service Division labor standards.
            </Text>
          </View>
        </View>

        {/* Description Section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Job Overview</Text>
          <Text style={styles.bodyText}>
            {job.description || 'No detailed description available.'}
          </Text>
        </View>

        {/* Requirements Section */}
        {requirementsList.length > 0 && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Qualifications & Requirements</Text>
            {requirementsList.map((req, index) => (
              <View key={index} style={styles.reqRow}>
                <Ionicons name="checkmark-circle" size={18} color={COLORS.primary} style={{ marginTop: 2 }} />
                <Text style={styles.reqText}>{req}</Text>
              </View>
            ))}
          </View>
        )}

        <View style={{ height: 90 }} />
      </ScrollView>

      {/* Sticky Bottom Action Bar */}
      <View style={styles.bottomBar}>
        <View style={styles.bottomBarInfo}>
          <Text style={styles.bottomBarLabel}>Compensation</Text>
          <Text style={styles.bottomBarSalary}>{job.salary || job.salary_expectation || 'Competitive'}</Text>
        </View>

        <TouchableOpacity style={styles.applyBtn} onPress={handleApplyPress}>
          <Text style={styles.applyBtnText}>
            {isAuthenticated ? 'Apply Now' : 'Sign In to Apply'}
          </Text>
          <Ionicons name="send" size={16} color={COLORS.white} />
        </TouchableOpacity>
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
    borderRadius: BORDER_RADIUS.lg,
    backgroundColor: COLORS.primaryUltraLight,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  avatarText: {
    fontSize: 26,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.navy,
    textAlign: 'center',
  },
  company: {
    fontSize: 14,
    color: COLORS.textSecondary,
    marginTop: 4,
  },
  locationRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: 6,
  },
  locationText: {
    fontSize: 13,
    color: COLORS.textMuted,
  },
  metricsGrid: {
    flexDirection: 'row',
    width: '100%',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    marginTop: SPACING.lg,
    justifyContent: 'space-around',
  },
  metricItem: {
    alignItems: 'center',
  },
  metricLabel: {
    fontSize: 10,
    color: COLORS.textMuted,
    textTransform: 'uppercase',
    fontWeight: 'bold',
  },
  metricValue: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginTop: 4,
  },
  pwdBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.purpleBg,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    marginTop: SPACING.md,
    width: '100%',
    borderWidth: 1,
    borderColor: '#e9d5ff',
  },
  pwdTitle: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.purple,
  },
  pwdDesc: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  verifiedCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primaryUltraLight,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.primaryLight,
  },
  verifiedTitle: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
  },
  verifiedText: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
    lineHeight: 16,
  },
  section: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: SPACING.md,
  },
  bodyText: {
    fontSize: 13,
    color: COLORS.textSecondary,
    lineHeight: 20,
  },
  reqRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: SPACING.sm,
    marginBottom: SPACING.sm,
  },
  reqText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.textSecondary,
    lineHeight: 18,
  },
  bottomBar: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: COLORS.white,
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
    paddingHorizontal: SPACING.xl,
    paddingVertical: SPACING.md,
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
    color: COLORS.textMuted,
  },
  bottomBarSalary: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
  },
  applyBtn: {
    backgroundColor: COLORS.primary,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingHorizontal: SPACING.xl,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
  },
  applyBtnText: {
    color: COLORS.white,
    fontSize: 14,
    fontWeight: 'bold',
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
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginTop: SPACING.md,
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
    paddingVertical: SPACING.sm,
    borderRadius: BORDER_RADIUS.md,
  },
  backBtnText: {
    color: COLORS.white,
    fontWeight: 'bold',
  },
});

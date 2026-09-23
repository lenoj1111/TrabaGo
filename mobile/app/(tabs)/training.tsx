import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Modal,
  Alert,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { trainingService } from '../../src/services/trainingService';
import { authService } from '../../src/services/authService';
import { TrainingProgramItem, UserProfile } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function TrainingScreen() {
  const router = useRouter();
  const { isAuthenticated } = useAuth();

  const [trainings, setTrainings] = useState<TrainingProgramItem[]>([]);
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [enrollingId, setEnrollingId] = useState<number | null>(null);
  const [activeTab, setActiveTab] = useState<'all' | 'enrolled' | 'completed'>('all');

  // Certificate Modal State
  const [selectedCert, setSelectedCert] = useState<{
    courseTitle: string;
    certificateNo: string;
    score: number | null;
    duration: string;
    type: string;
  } | null>(null);

  const loadData = useCallback(async () => {
    try {
      const [programs, userProfile] = await Promise.all([
        trainingService.getTrainings(),
        authService.getStoredUser(),
      ]);
      setTrainings(programs);
      setProfile(userProfile);
    } catch (err) {
      console.warn('Error fetching combined training & skills data:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const onRefresh = () => {
    setRefreshing(true);
    loadData();
  };

  const handleEnroll = async (item: TrainingProgramItem) => {
    const courseId = item.id || item.training_id;
    if (!courseId) return;

    if (!isAuthenticated) {
      Alert.alert(
        'Sign In Required 🔐',
        'Please sign in with your Jobseeker account to enroll in courses, study modules, and receive official DMDP certificates.',
        [
          { text: 'Sign In', onPress: () => router.push('/auth/login') },
          { text: 'Cancel', style: 'cancel' },
        ]
      );
      return;
    }

    setEnrollingId(courseId);
    try {
      const res = await trainingService.enroll(courseId);
      if (res.success) {
        Alert.alert(
          'Enrolled Successfully! 🎓',
          `You are now enrolled in "${item.title}". You can study the course topics and take the competency quiz to unlock certified skills.`,
          [
            { text: 'Start Now', onPress: () => router.push({ pathname: '/training/[id]', params: { id: courseId } }) },
            { text: 'Later', style: 'cancel' },
          ]
        );
        loadData();
      } else {
        Alert.alert('Notice', res.message || 'Unable to enroll.');
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Failed to enroll.');
    } finally {
      setEnrollingId(null);
    }
  };

  // User's verified skills list
  const userSkills: string[] = (profile?.skills || []).map((s: string) => s.toLowerCase());

  // Filter trainings based on activeTab
  const filteredTrainings = trainings.filter((t) => {
    if (activeTab === 'enrolled') return t.is_enrolled && !t.is_completed;
    if (activeTab === 'completed') return t.is_completed;
    return true;
  });

  // Summary counts
  const totalCount = trainings.length;
  const enrolledCount = trainings.filter((t) => t.is_enrolled && !t.is_completed).length;
  const completedCount = trainings.filter((t) => t.is_completed).length;

  const renderTrainingCard = ({ item }: { item: TrainingProgramItem }) => {
    const courseId = item.id || item.training_id;
    const duration = item.durationMonths || item.duration_months || 1;
    const type = item.trainingType || item.training_type || 'Online';
    const topicsCount = item.topics?.length || 0;
    const isEnrolled = !!item.is_enrolled;
    const isCompleted = !!item.is_completed;
    const score = item.score;
    const certNo = item.certificate_no;

    // Course skills list
    const skillsList = Array.isArray(item.skills) && item.skills.length > 0
      ? item.skills
      : [item.title.replace(/Program|Course|Training/gi, '').trim()];

    return (
      <View style={styles.card}>
        {/* Top Status & Course Type Bar */}
        <View style={styles.cardTopBar}>
          <View style={[styles.typeBadge, type.toLowerCase().includes('online') ? styles.typeOnline : styles.typeOnsite]}>
            <Ionicons
              name={type.toLowerCase().includes('online') ? 'laptop-outline' : 'business-outline'}
              size={12}
              color={type.toLowerCase().includes('online') ? COLORS.primary : COLORS.purple}
            />
            <Text style={[styles.typeBadgeText, type.toLowerCase().includes('online') ? styles.typeOnlineText : styles.typeOnsiteText]}>
              {type}
            </Text>
          </View>

          {isCompleted ? (
            <View style={styles.completedBadge}>
              <Ionicons name="checkmark-circle" size={14} color={COLORS.success} />
              <Text style={styles.completedBadgeText}>Completed ({score ?? 100}%)</Text>
            </View>
          ) : isEnrolled ? (
            <View style={styles.enrolledBadge}>
              <Ionicons name="time" size={14} color={COLORS.warning} />
              <Text style={styles.enrolledBadgeText}>In Progress</Text>
            </View>
          ) : (
            <View style={styles.availableBadge}>
              <Text style={styles.availableBadgeText}>Available to Enroll</Text>
            </View>
          )}
        </View>

        {/* Card Header & Title */}
        <View style={styles.cardHeader}>
          <View style={styles.iconBox}>
            <Ionicons
              name={isCompleted ? 'ribbon' : isEnrolled ? 'book' : 'school-outline'}
              size={24}
              color={isCompleted ? COLORS.purple : COLORS.primary}
            />
          </View>
          <View style={styles.headerText}>
            <Text style={styles.title} numberOfLines={2}>
              {item.title}
            </Text>
            <Text style={styles.subtitle}>
              Cebu DMDP Technical & Vocational Education
            </Text>
          </View>
        </View>

        {/* Target Competencies / Skills Section (COMBINED FEATURE) */}
        <View style={styles.skillsSection}>
          <View style={styles.skillsLabelRow}>
            <Ionicons name="sparkles" size={13} color={COLORS.primary} />
            <Text style={styles.skillsLabel}>Target Competencies / Skills:</Text>
          </View>
          <View style={styles.skillsPillContainer}>
            {skillsList.map((skill, index) => {
              const isVerified = userSkills.includes(skill.toLowerCase());
              return (
                <View
                  key={index}
                  style={[
                    styles.skillPill,
                    isCompleted || isVerified ? styles.skillPillEarned : styles.skillPillPending,
                  ]}>
                  <Ionicons
                    name={isCompleted || isVerified ? 'shield-checkmark' : 'add-circle-outline'}
                    size={11}
                    color={isCompleted || isVerified ? COLORS.primaryDark : COLORS.textSecondary}
                  />
                  <Text
                    style={[
                      styles.skillPillText,
                      (isCompleted || isVerified) && styles.skillPillTextEarned,
                    ]}>
                    {skill}
                  </Text>
                  {isCompleted && (
                    <Text style={styles.verifiedTag}>VERIFIED</Text>
                  )}
                </View>
              );
            })}
          </View>
        </View>

        {/* Meta Info Row */}
        <View style={styles.metaRow}>
          <View style={styles.metaItem}>
            <Ionicons name="calendar-outline" size={14} color={COLORS.textSecondary} />
            <Text style={styles.metaText}>{duration} {duration === 1 ? 'Month' : 'Months'}</Text>
          </View>
          {topicsCount > 0 && (
            <View style={styles.metaItem}>
              <Ionicons name="list-outline" size={14} color={COLORS.textSecondary} />
              <Text style={styles.metaText}>{topicsCount} Modules</Text>
            </View>
          )}
          <View style={styles.metaItem}>
            <Ionicons name="trophy-outline" size={14} color={COLORS.textSecondary} />
            <Text style={styles.metaText}>Passing Score: {item.passing_score || 80}%</Text>
          </View>
        </View>

        {/* Card Action Footer */}
        <View style={styles.cardFooter}>
          {isCompleted ? (
            <View style={styles.certRow}>
              <TouchableOpacity
                style={styles.certBtn}
                onPress={() =>
                  setSelectedCert({
                    courseTitle: item.title,
                    certificateNo: certNo || `DMDP-${courseId}-VALID`,
                    score: score || 100,
                    duration: `${duration} ${duration === 1 ? 'Month' : 'Months'}`,
                    type,
                  })
                }>
                <Ionicons name="ribbon" size={15} color={COLORS.purple} />
                <Text style={styles.certBtnText}>View Certificate</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={styles.reviewQuizBtn}
                onPress={() => router.push({ pathname: '/training/[id]', params: { id: courseId } })}>
                <Text style={styles.reviewQuizBtnText}>Review Quiz</Text>
                <Ionicons name="chevron-forward" size={14} color={COLORS.textSecondary} />
              </TouchableOpacity>
            </View>
          ) : isEnrolled ? (
            <TouchableOpacity
              style={styles.continueBtn}
              onPress={() => router.push({ pathname: '/training/[id]', params: { id: courseId } })}>
              <Text style={styles.continueBtnText}>Continue Course & Assessment</Text>
              <Ionicons name="arrow-forward" size={15} color={COLORS.white} />
            </TouchableOpacity>
          ) : (
            <TouchableOpacity
              style={styles.enrollBtn}
              disabled={enrollingId === courseId}
              onPress={() => handleEnroll(item)}>
              {enrollingId === courseId ? (
                <ActivityIndicator size="small" color={COLORS.white} />
              ) : (
                <>
                  <Ionicons name="school" size={15} color={COLORS.white} />
                  <Text style={styles.enrollBtnText}>Enroll & Learn Skills</Text>
                </>
              )}
            </TouchableOpacity>
          )}
        </View>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      {/* Header Banner */}
      <View style={styles.banner}>
        <View style={{ flex: 1 }}>
          <View style={styles.badgePill}>
            <Ionicons name="sparkles" size={11} color={COLORS.white} />
            <Text style={styles.badgePillText}>DMDP Skills & Enrollment Hub</Text>
          </View>
          <Text style={styles.bannerTitle}>Vocational Skills & Enrollments</Text>
          <Text style={styles.bannerSubtitle}>
            Enroll in training courses, master key competencies, and earn verified skills directly on your profile.
          </Text>
        </View>
      </View>

      {/* Quick Stats Grid */}
      <View style={styles.statsGrid}>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{userSkills.length}</Text>
          <Text style={styles.statLabel}>Verified Skills</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNumber, { color: COLORS.warning }]}>{enrolledCount}</Text>
          <Text style={styles.statLabel}>In Progress</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNumber, { color: COLORS.purple }]}>{completedCount}</Text>
          <Text style={styles.statLabel}>Certificates</Text>
        </View>
      </View>

      {/* Unified Filter Tabs */}
      <View style={styles.filterRow}>
        <TouchableOpacity
          style={[styles.filterPill, activeTab === 'all' && styles.filterPillActive]}
          onPress={() => setActiveTab('all')}>
          <Text style={[styles.filterText, activeTab === 'all' && styles.filterTextActive]}>
            All Courses ({totalCount})
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.filterPill, activeTab === 'enrolled' && styles.filterPillActive]}
          onPress={() => setActiveTab('enrolled')}>
          <Text style={[styles.filterText, activeTab === 'enrolled' && styles.filterTextActive]}>
            Enrolled ({enrolledCount})
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.filterPill, activeTab === 'completed' && styles.filterPillActive]}
          onPress={() => setActiveTab('completed')}>
          <Text style={[styles.filterText, activeTab === 'completed' && styles.filterTextActive]}>
            Completed ({completedCount})
          </Text>
        </TouchableOpacity>
      </View>

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
          <Text style={styles.loadingText}>Loading training courses & skills...</Text>
        </View>
      ) : (
        <FlatList
          data={filteredTrainings}
          keyExtractor={(item) => String(item.id || item.training_id || Math.random())}
          renderItem={renderTrainingCard}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="school-outline" size={64} color={COLORS.border} />
              <Text style={styles.emptyTitle}>
                {activeTab === 'completed'
                  ? 'No completed courses yet'
                  : activeTab === 'enrolled'
                  ? 'No active enrollments'
                  : 'No training programs available'}
              </Text>
              <Text style={styles.emptySubtitle}>
                {activeTab === 'completed'
                  ? 'Complete your enrolled courses and pass the quiz to earn your official certificate.'
                  : activeTab === 'enrolled'
                  ? 'Browse the course catalog and enroll to acquire new competencies.'
                  : 'Check back soon for new DMDP vocational programs.'}
              </Text>
            </View>
          }
        />
      )}

      {/* Certificate Modal Preview */}
      <Modal
        visible={!!selectedCert}
        transparent
        animationType="fade"
        onRequestClose={() => setSelectedCert(null)}>
        <View style={styles.modalOverlay}>
          <View style={styles.certModalCard}>
            <View style={styles.certHeader}>
              <Ionicons name="ribbon" size={48} color={COLORS.purple} />
              <Text style={styles.certSeal}>CITY OF CEBU • DMDP</Text>
              <Text style={styles.certMainTitle}>CERTIFICATE OF COMPLETION</Text>
              <Text style={styles.certSubtitle}>Department of Manpower Development and Placement</Text>
            </View>

            <Text style={styles.certBodyText}>This certifies that</Text>
            <Text style={styles.certName}>
              {profile?.fullName || `${profile?.firstName || 'Juan'} ${profile?.lastName || 'Dela Cruz'}`}
            </Text>
            <Text style={styles.certBodyText}>has successfully completed the vocational competency program:</Text>
            <Text style={styles.certCourseTitle}>{selectedCert?.courseTitle}</Text>

            <View style={styles.certDetailsRow}>
              <Text style={styles.certDetailText}>Score: {selectedCert?.score ?? 100}%</Text>
              <Text style={styles.certDetailText}>Duration: {selectedCert?.duration}</Text>
              <Text style={styles.certDetailText}>Mode: {selectedCert?.type}</Text>
            </View>

            <View style={styles.certSealRow}>
              <Text style={styles.certSignatory}>Serial: {selectedCert?.certificateNo}</Text>
              <Text style={styles.certSignatory}>✓ Verified in TrabaGo Document Hub</Text>
            </View>

            <TouchableOpacity style={styles.certCloseBtn} onPress={() => setSelectedCert(null)}>
              <Text style={styles.certCloseBtnText}>Close Certificate</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  banner: {
    backgroundColor: COLORS.primaryDark,
    padding: SPACING.lg,
    borderBottomLeftRadius: BORDER_RADIUS.xl,
    borderBottomRightRadius: BORDER_RADIUS.xl,
  },
  badgePill: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    gap: 4,
    backgroundColor: 'rgba(255, 255, 255, 0.15)',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.full,
    marginBottom: 6,
  },
  badgePillText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: COLORS.white,
    letterSpacing: 0.5,
  },
  bannerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  bannerSubtitle: {
    fontSize: 12,
    color: COLORS.primaryUltraLight,
    marginTop: 4,
    lineHeight: 16,
  },
  statsGrid: {
    flexDirection: 'row',
    paddingHorizontal: SPACING.lg,
    marginTop: -SPACING.md,
    gap: SPACING.sm,
  },
  statCard: {
    flex: 1,
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    paddingVertical: SPACING.md,
    paddingHorizontal: SPACING.sm,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  statNumber: {
    fontSize: 18,
    fontWeight: '900',
    color: COLORS.primary,
  },
  statLabel: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.textMuted,
    marginTop: 2,
  },
  filterRow: {
    flexDirection: 'row',
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
    gap: SPACING.sm,
  },
  filterPill: {
    paddingHorizontal: SPACING.md,
    paddingVertical: 7,
    borderRadius: BORDER_RADIUS.full,
    backgroundColor: COLORS.white,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  filterPillActive: {
    backgroundColor: COLORS.primary,
    borderColor: COLORS.primary,
  },
  filterText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.textSecondary,
  },
  filterTextActive: {
    color: COLORS.white,
  },
  listContent: {
    paddingHorizontal: SPACING.lg,
    paddingBottom: SPACING.xl,
    gap: SPACING.md,
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  cardTopBar: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  typeBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.sm,
  },
  typeOnline: {
    backgroundColor: COLORS.primaryUltraLight,
  },
  typeOnsite: {
    backgroundColor: COLORS.purpleBg,
  },
  typeBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
  },
  typeOnlineText: {
    color: COLORS.primaryDark,
  },
  typeOnsiteText: {
    color: COLORS.purple,
  },
  completedBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: '#ECFDF5',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.sm,
    borderWidth: 1,
    borderColor: '#A7F3D0',
  },
  completedBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#065F46',
  },
  enrolledBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: COLORS.warningBg,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.sm,
    borderWidth: 1,
    borderColor: '#FDE68A',
  },
  enrolledBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: COLORS.warning,
  },
  availableBadge: {
    backgroundColor: COLORS.surfaceSubtle,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.sm,
  },
  availableBadgeText: {
    fontSize: 10,
    fontWeight: '600',
    color: COLORS.textMuted,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginTop: 2,
  },
  iconBox: {
    width: 44,
    height: 44,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.primaryUltraLight,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: SPACING.md,
  },
  headerText: {
    flex: 1,
  },
  title: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  subtitle: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  skillsSection: {
    marginTop: SPACING.md,
    paddingTop: SPACING.sm,
    borderTopWidth: 1,
    borderTopColor: COLORS.borderLight,
  },
  skillsLabelRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginBottom: 6,
  },
  skillsLabel: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.textSecondary,
  },
  skillsPillContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
  },
  skillPill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
    borderWidth: 1,
  },
  skillPillEarned: {
    backgroundColor: COLORS.primaryUltraLight,
    borderColor: COLORS.primaryLight,
  },
  skillPillPending: {
    backgroundColor: COLORS.surfaceSubtle,
    borderColor: COLORS.border,
  },
  skillPillText: {
    fontSize: 11,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  skillPillTextEarned: {
    color: COLORS.primaryDark,
    fontWeight: 'bold',
  },
  verifiedTag: {
    fontSize: 8,
    fontWeight: '900',
    backgroundColor: COLORS.primary,
    color: COLORS.white,
    paddingHorizontal: 4,
    paddingVertical: 1,
    borderRadius: 3,
    marginLeft: 2,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    flexWrap: 'wrap',
    gap: SPACING.md,
    marginTop: SPACING.md,
    paddingVertical: SPACING.xs,
  },
  metaItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  metaText: {
    fontSize: 11,
    color: COLORS.textSecondary,
  },
  cardFooter: {
    marginTop: SPACING.md,
    paddingTop: SPACING.sm,
    borderTopWidth: 1,
    borderTopColor: COLORS.borderLight,
  },
  certRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    gap: SPACING.sm,
  },
  certBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: COLORS.purpleBg,
    paddingVertical: 9,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: '#E9D5FF',
  },
  certBtnText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.purple,
  },
  reviewQuizBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: SPACING.md,
    paddingVertical: 9,
  },
  reviewQuizBtnText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  continueBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: COLORS.primary,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
  },
  continueBtnText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  enrollBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: COLORS.primaryDark,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
  },
  enrollBtnText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.xl,
  },
  loadingText: {
    marginTop: SPACING.md,
    fontSize: 13,
    color: COLORS.textSecondary,
  },
  emptyContainer: {
    paddingVertical: SPACING.xxl,
    alignItems: 'center',
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginTop: SPACING.md,
  },
  emptySubtitle: {
    fontSize: 13,
    color: COLORS.textMuted,
    textAlign: 'center',
    marginTop: SPACING.xs,
    paddingHorizontal: SPACING.xl,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.75)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.lg,
  },
  certModalCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    width: '100%',
    maxWidth: 380,
    alignItems: 'center',
    borderWidth: 4,
    borderColor: COLORS.primaryLight,
  },
  certHeader: {
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  certSeal: {
    fontSize: 10,
    fontWeight: 'bold',
    color: COLORS.textMuted,
    letterSpacing: 1,
    marginTop: 4,
  },
  certMainTitle: {
    fontSize: 16,
    fontWeight: '900',
    color: COLORS.navy,
    marginTop: 2,
    letterSpacing: 0.5,
  },
  certSubtitle: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
    textAlign: 'center',
  },
  certName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
    textAlign: 'center',
    marginVertical: 4,
    textDecorationLine: 'underline',
  },
  certBodyText: {
    fontSize: 11,
    color: COLORS.textSecondary,
    textAlign: 'center',
    lineHeight: 16,
  },
  certCourseTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: COLORS.navy,
    textAlign: 'center',
    marginTop: 4,
    marginBottom: SPACING.md,
  },
  certDetailsRow: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    width: '100%',
    paddingVertical: SPACING.xs,
    borderTopWidth: 1,
    borderBottomWidth: 1,
    borderColor: COLORS.borderLight,
    marginBottom: SPACING.md,
  },
  certDetailText: {
    fontSize: 11,
    fontWeight: 'bold',
    color: COLORS.textSecondary,
  },
  certSealRow: {
    alignItems: 'center',
    gap: 2,
    marginBottom: SPACING.lg,
  },
  certSignatory: {
    fontSize: 10,
    color: COLORS.textMuted,
    fontWeight: '600',
  },
  certCloseBtn: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.xl,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
    width: '100%',
    alignItems: 'center',
  },
  certCloseBtnText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.white,
  },
});

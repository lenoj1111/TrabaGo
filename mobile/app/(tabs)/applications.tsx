import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
  Alert,
  Modal,
  TextInput,
  Linking,
  ScrollView,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { applicationService } from '../../src/services/applicationService';
import { JobApplicationItem, ApplicationCounts } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function ApplicationsScreen() {
  const router = useRouter();
  const { isAuthenticated, user } = useAuth();
  const userId = user?.id || user?.user_id;

  const [applications, setApplications] = useState<JobApplicationItem[]>([]);
  const [counts, setCounts] = useState<ApplicationCounts>({
    all: 0,
    offered: 0,
    pending: 0,
    reviewed: 0,
    interview: 0,
    hired: 0,
    rejected: 0,
  });
  const [isEmployed, setIsEmployed] = useState<boolean>(false);
  const [hiredCompany, setHiredCompany] = useState<string | null>(null);

  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [statusFilter, setStatusFilter] = useState<'all' | 'offered' | 'pending' | 'review' | 'interview' | 'hired'>('all');

  // Decline Offer Modal
  const [declineModalVisible, setDeclineModalVisible] = useState(false);
  const [activeDeclineApp, setActiveDeclineApp] = useState<{ id: string | number; title: string; company: string } | null>(null);
  const [declineReason, setDeclineReason] = useState('');
  const [submittingDecline, setSubmittingDecline] = useState(false);

  // Resignation Modal
  const [resignationModalVisible, setResignationModalVisible] = useState(false);
  const [resignationReason, setResignationReason] = useState('');
  const [submittingResignation, setSubmittingResignation] = useState(false);

  const fetchApplications = useCallback(async () => {
    try {
      const data = await applicationService.getApplications();
      setApplications(data.applications || []);
      if (data.counts) {
        setCounts(data.counts);
      }
      setIsEmployed(!!data.is_employed);
      setHiredCompany(data.hired_company || null);
    } catch (err) {
      console.warn('Error fetching applications:', err);
      setApplications([]);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated && userId) {
      setLoading(true);
      fetchApplications();
    } else {
      setApplications([]);
      setLoading(false);
    }
  }, [isAuthenticated, userId, fetchApplications]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchApplications();
  };

  const handleAcceptOffer = (appId: number | string, jobTitle: string, company: string) => {
    Alert.alert(
      'Accept Job Offer',
      `Congratulations! Are you sure you want to accept the job offer for "${jobTitle}" at ${company}?\n\nThis will tag your profile as Officially Employed and auto-withdraw your other pending applications.`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Accept Offer & Get Hired',
          style: 'default',
          onPress: async () => {
            const res = await applicationService.acceptOffer(appId);
            if (res.success) {
              Alert.alert('🎉 Congratulations!', res.message || 'You have accepted the offer!');
              fetchApplications();
            } else {
              Alert.alert('Error', res.message || 'Failed to accept offer.');
            }
          },
        },
      ]
    );
  };

  const handleOpenDecline = (appId: number | string, jobTitle: string, company: string) => {
    setActiveDeclineApp({ id: appId, title: jobTitle, company });
    setDeclineReason('');
    setDeclineModalVisible(true);
  };

  const handleConfirmDecline = async () => {
    if (!activeDeclineApp) return;
    setSubmittingDecline(true);
    try {
      const res = await applicationService.declineOffer(activeDeclineApp.id, declineReason);
      if (res.success) {
        Alert.alert('Offer Declined', res.message || 'Job offer was declined.');
        setDeclineModalVisible(false);
        fetchApplications();
      } else {
        Alert.alert('Error', res.message || 'Failed to decline offer.');
      }
    } finally {
      setSubmittingDecline(false);
    }
  };

  const handleConfirmResignation = async () => {
    if (!resignationReason.trim()) {
      Alert.alert('Reason Required', 'Please enter a reason for requesting resignation.');
      return;
    }
    setSubmittingResignation(true);
    try {
      const res = await applicationService.requestResignation(resignationReason.trim());
      if (res.success) {
        Alert.alert('Resignation Submitted', res.message || 'Your request has been submitted to your employer.');
        setResignationModalVisible(false);
        setResignationReason('');
        fetchApplications();
      } else {
        Alert.alert('Error', res.message || 'Failed to submit resignation request.');
      }
    } finally {
      setSubmittingResignation(false);
    }
  };

  const handleWithdraw = (applicationId: number | string, jobTitle: string) => {
    Alert.alert(
      'Withdraw Application',
      `Are you sure you want to withdraw your application for "${jobTitle}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Withdraw',
          style: 'destructive',
          onPress: async () => {
            const res = await applicationService.withdraw(applicationId);
            if (res.success) {
              setApplications((prev) => prev.filter((a) => (a.id || a.application_id) !== applicationId));
              Alert.alert('Success', 'Application withdrawn.');
            } else {
              Alert.alert('Error', res.message || 'Failed to withdraw application.');
            }
          },
        },
      ]
    );
  };

  const filteredApplications = applications.filter((app) => {
    const s = (app.status || '').toLowerCase();
    if (statusFilter === 'offered') {
      return s.includes('offer');
    }
    if (statusFilter === 'pending') {
      return s.includes('pend') || s.includes('submit');
    }
    if (statusFilter === 'review') {
      return s.includes('review') || s.includes('shortlist');
    }
    if (statusFilter === 'interview') {
      return s.includes('interview');
    }
    if (statusFilter === 'hired') {
      return s.includes('hire') || s.includes('accept') || s.includes('placed');
    }
    return true;
  });

  const getStatusBadgeStyle = (status: string) => {
    const s = status.toLowerCase();
    if (s.includes('offer')) {
      return { bg: COLORS.emerald50, text: COLORS.emerald800, border: COLORS.emerald300, label: 'Offer Extended!' };
    }
    if (s.includes('hire') || s.includes('placed') || s.includes('accept')) {
      return { bg: COLORS.successBg, text: COLORS.success, border: COLORS.successBorder, label: 'Hired / Placed' };
    }
    if (s.includes('shortlist')) {
      return { bg: COLORS.purpleBg, text: COLORS.purple, border: COLORS.purpleBorder, label: 'Shortlisted' };
    }
    if (s.includes('interview')) {
      return { bg: COLORS.infoBg, text: COLORS.info, border: COLORS.infoBorder, label: 'Interview Scheduled' };
    }
    if (s.includes('review')) {
      return { bg: COLORS.infoBg, text: COLORS.info, border: COLORS.infoBorder, label: 'Under Review' };
    }
    if (s.includes('reject') || s.includes('decline')) {
      return { bg: COLORS.dangerBg, text: COLORS.danger, border: COLORS.dangerBorder, label: 'Not Selected' };
    }
    return { bg: COLORS.warningBg, text: COLORS.warning, border: COLORS.warningBorder, label: 'Pending Review' };
  };

  if (!isAuthenticated) {
    return (
      <View style={styles.centerContainer}>
        <View style={styles.emptyIconCircle}>
          <Ionicons name="lock-closed-outline" size={36} color={COLORS.primary} />
        </View>
        <Text style={styles.emptyTitle}>Sign In Required</Text>
        <Text style={styles.emptySubtitle}>
          Sign in to your TrabaGo Jobseeker account to track your submitted job applications, interview schedules, and official employment offers.
        </Text>
        <TouchableOpacity style={styles.actionBtn} onPress={() => router.push('/auth/login')}>
          <Text style={styles.actionBtnText}>Sign In Now</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const renderApplicationCard = ({ item }: { item: JobApplicationItem }) => {
    const statusInfo = getStatusBadgeStyle(item.status);
    const dateFormatted = item.appliedAt || item.applied_at
      ? (item.appliedAt || item.applied_at).split('T')[0]
      : 'Recently';
    const isOffer = (item.status || '').toLowerCase().includes('offer');
    const isInterview = (item.status || '').toLowerCase().includes('interview');
    const venue = item.interviewLocation || item.interview_location;
    const isVirtual = (item.interviewMode || item.interview_mode || '').toLowerCase().includes('virtual') || (venue && venue.startsWith('http'));
    const companyName = item.company || item.company_name || 'DMDP Partner Employer';
    const companyInitial = companyName.charAt(0).toUpperCase();

    return (
      <View style={[styles.card, isOffer && styles.cardOffer]}>
        {/* Card Header */}
        <View style={styles.cardHeader}>
          <View style={styles.avatarPlaceholder}>
            <Text style={styles.avatarText}>{companyInitial}</Text>
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.jobTitle} numberOfLines={1}>
              {item.jobTitle || item.job_title || 'Position'}
            </Text>
            <Text style={styles.companyName} numberOfLines={1}>
              {companyName}
            </Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: statusInfo.bg, borderColor: statusInfo.border }]}>
            <Text style={[styles.statusBadgeText, { color: statusInfo.text }]}>
              {statusInfo.label}
            </Text>
          </View>
        </View>

        {/* Job Offer Extended Action Box */}
        {isOffer && (
          <View style={styles.offerBox}>
            <View style={styles.offerHeader}>
              <Ionicons name="sparkles" size={16} color={COLORS.emerald700} />
              <Text style={styles.offerTitle}>Official Job Offer Extended!</Text>
            </View>
            <Text style={styles.offerText}>
              The employer has extended a formal employment offer for this position. Confirm your acceptance to finalize your official DMDP placement.
            </Text>
            <View style={styles.offerBtnRow}>
              <TouchableOpacity
                style={styles.acceptOfferBtn}
                activeOpacity={0.88}
                onPress={() => handleAcceptOffer(item.id || item.application_id, item.jobTitle || item.job_title, companyName)}>
                <Ionicons name="checkmark-circle" size={15} color={COLORS.white} />
                <Text style={styles.acceptOfferText}>Accept Offer & Get Hired</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.declineOfferBtn}
                activeOpacity={0.88}
                onPress={() => handleOpenDecline(item.id || item.application_id, item.jobTitle || item.job_title, companyName)}>
                <Text style={styles.declineOfferText}>Decline</Text>
              </TouchableOpacity>
            </View>
          </View>
        )}

        {/* Interview Schedule Notice */}
        {(isInterview || item.interviewSchedule || item.interview_schedule) && (
          <View style={styles.interviewBox}>
            <View style={styles.interviewHeader}>
              <Ionicons name="calendar" size={16} color={COLORS.info} />
              <Text style={styles.interviewTitle}>Interview Scheduled</Text>
            </View>
            {(item.interviewSchedule || item.interview_schedule) && (
              <View style={styles.interviewRow}>
                <Ionicons name="time-outline" size={13} color={COLORS.slate500} />
                <Text style={styles.interviewDetail}>
                  <Text style={{ fontWeight: '700' }}>Schedule: </Text>
                  {item.interviewSchedule || item.interview_schedule}
                </Text>
              </View>
            )}
            {(item.interviewMode || item.interview_mode) && (
              <View style={styles.interviewRow}>
                <Ionicons name="videocam-outline" size={13} color={COLORS.slate500} />
                <Text style={styles.interviewDetail}>
                  <Text style={{ fontWeight: '700' }}>Format: </Text>
                  {item.interviewMode || item.interview_mode}
                </Text>
              </View>
            )}
            {venue && (
              <View style={{ marginTop: 4 }}>
                <View style={styles.interviewRow}>
                  <Ionicons name={isVirtual ? "link-outline" : "location-outline"} size={13} color={COLORS.slate500} />
                  <Text style={styles.interviewDetail} numberOfLines={2}>
                    <Text style={{ fontWeight: '700' }}>{isVirtual ? 'Link: ' : 'Venue: '}</Text>
                    {venue}
                  </Text>
                </View>
                {isVirtual && (
                  <TouchableOpacity
                    style={styles.meetingLinkBtn}
                    onPress={() => Linking.openURL(venue).catch(() => Alert.alert('Invalid Link', venue))}>
                    <Ionicons name="videocam" size={13} color={COLORS.info} />
                    <Text style={styles.meetingLinkText}>Join Virtual Meeting</Text>
                  </TouchableOpacity>
                )}
              </View>
            )}
          </View>
        )}

        {/* Footer */}
        <View style={styles.cardFooter}>
          <View style={styles.appliedDateContainer}>
            <Ionicons name="time-outline" size={14} color={COLORS.slate400} />
            <Text style={styles.dateText}>Applied: {dateFormatted}</Text>
          </View>

          {item.status !== 'hired' && item.status !== 'withdrawn' && item.status !== 'declined' && (
            <TouchableOpacity
              style={styles.withdrawBtn}
              onPress={() => handleWithdraw(item.id || item.application_id, item.jobTitle || item.job_title)}
              hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="trash-outline" size={14} color={COLORS.danger} />
              <Text style={styles.withdrawBtnText}>Withdraw</Text>
            </TouchableOpacity>
          )}
        </View>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      {/* Officially Employed Placement Banner */}
      {isEmployed && (
        <View style={styles.employedHeaderCard}>
          <View style={styles.employedHeaderIcon}>
            <Ionicons name="checkmark-done-circle" size={22} color={COLORS.emerald700} />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.employedHeaderTag}>Officially Employed</Text>
            <Text style={styles.employedHeaderCompany}>Placed at {hiredCompany || 'Partner Employer'}</Text>
          </View>
          <TouchableOpacity
            style={styles.resignationBtn}
            onPress={() => setResignationModalVisible(true)}>
            <Text style={styles.resignationBtnText}>Request Resignation</Text>
          </TouchableOpacity>
        </View>
      )}

      {/* Filter Tabs Scrollable */}
      <View style={styles.tabsWrapper}>
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.tabsScrollContent}
          style={styles.tabsScrollView}>
          <TouchableOpacity
            style={[styles.tabItem, statusFilter === 'all' && styles.tabItemActive]}
            onPress={() => setStatusFilter('all')}>
            <Text style={[styles.tabItemText, statusFilter === 'all' && styles.tabItemTextActive]}>
              All ({counts.all})
            </Text>
          </TouchableOpacity>

          {counts.offered > 0 && (
            <TouchableOpacity
              style={[styles.tabItem, styles.tabItemOffer, statusFilter === 'offered' && styles.tabItemActive]}
              onPress={() => setStatusFilter('offered')}>
              <Ionicons name="sparkles" size={12} color={statusFilter === 'offered' ? COLORS.white : COLORS.emerald700} />
              <Text style={[styles.tabItemText, styles.tabItemOfferText, statusFilter === 'offered' && styles.tabItemTextActive]}>
                Offers ({counts.offered})
              </Text>
            </TouchableOpacity>
          )}

          <TouchableOpacity
            style={[styles.tabItem, statusFilter === 'pending' && styles.tabItemActive]}
            onPress={() => setStatusFilter('pending')}>
            <Text style={[styles.tabItemText, statusFilter === 'pending' && styles.tabItemTextActive]}>
              Pending ({counts.pending})
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tabItem, statusFilter === 'review' && styles.tabItemActive]}
            onPress={() => setStatusFilter('review')}>
            <Text style={[styles.tabItemText, statusFilter === 'review' && styles.tabItemTextActive]}>
              In Review ({counts.reviewed})
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tabItem, statusFilter === 'interview' && styles.tabItemActive]}
            onPress={() => setStatusFilter('interview')}>
            <Text style={[styles.tabItemText, statusFilter === 'interview' && styles.tabItemTextActive]}>
              Interview ({counts.interview})
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tabItem, statusFilter === 'hired' && styles.tabItemActive]}
            onPress={() => setStatusFilter('hired')}>
            <Text style={[styles.tabItemText, statusFilter === 'hired' && styles.tabItemTextActive]}>
              Hired ({counts.hired})
            </Text>
          </TouchableOpacity>
        </ScrollView>
      </View>

      {/* Decline Offer Modal */}
      <Modal visible={declineModalVisible} transparent animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalCard}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Decline Job Offer</Text>
              <TouchableOpacity onPress={() => setDeclineModalVisible(false)}>
                <Ionicons name="close" size={20} color={COLORS.slate500} />
              </TouchableOpacity>
            </View>
            <Text style={styles.modalSub}>
              Position: <Text style={{ fontWeight: '700' }}>{activeDeclineApp?.title}</Text> at {activeDeclineApp?.company}
            </Text>
            <Text style={styles.inputLabel}>Reason for Declining (Optional):</Text>
            <TextInput
              style={styles.modalInput}
              value={declineReason}
              onChangeText={setDeclineReason}
              placeholder="e.g. Accepted another offer, salary expectation, relocation..."
              placeholderTextColor={COLORS.slate400}
              multiline
              numberOfLines={3}
            />
            <View style={styles.modalBtnRow}>
              <TouchableOpacity
                style={styles.modalCancelBtn}
                onPress={() => setDeclineModalVisible(false)}>
                <Text style={styles.modalCancelText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.modalDeclineBtn}
                disabled={submittingDecline}
                onPress={handleConfirmDecline}>
                {submittingDecline ? (
                  <ActivityIndicator size="small" color={COLORS.white} />
                ) : (
                  <Text style={styles.modalDeclineText}>Confirm Decline</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* Resignation Request Modal */}
      <Modal visible={resignationModalVisible} transparent animationType="fade">
        <View style={styles.modalOverlay}>
          <View style={styles.modalCard}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Request Resignation</Text>
              <TouchableOpacity onPress={() => setResignationModalVisible(false)}>
                <Ionicons name="close" size={20} color={COLORS.slate500} />
              </TouchableOpacity>
            </View>
            <Text style={styles.modalSub}>
              Submit an official resignation notice to <Text style={{ fontWeight: '700' }}>{hiredCompany || 'your employer'}</Text> via the DMDP platform.
            </Text>
            <Text style={styles.inputLabel}>Reason for Resignation *:</Text>
            <TextInput
              style={styles.modalInput}
              value={resignationReason}
              onChangeText={setResignationReason}
              placeholder="State your reason and intended last day of service..."
              placeholderTextColor={COLORS.slate400}
              multiline
              numberOfLines={4}
            />
            <View style={styles.modalBtnRow}>
              <TouchableOpacity
                style={styles.modalCancelBtn}
                onPress={() => setResignationModalVisible(false)}>
                <Text style={styles.modalCancelText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.modalSubmitResignBtn}
                disabled={submittingResignation}
                onPress={handleConfirmResignation}>
                {submittingResignation ? (
                  <ActivityIndicator size="small" color={COLORS.white} />
                ) : (
                  <Text style={styles.modalSubmitResignText}>Submit Notice</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* Main List */}
      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
          <Text style={styles.loadingText}>Loading your applications...</Text>
        </View>
      ) : (
        <FlatList
          data={filteredApplications}
          keyExtractor={(item) => String(item.id || item.application_id || Math.random())}
          renderItem={renderApplicationCard}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <View style={styles.emptyIconCircle}>
                <Ionicons name="document-text-outline" size={36} color={COLORS.slate400} />
              </View>
              <Text style={styles.emptyTitle}>No applications found</Text>
              <Text style={styles.emptySubtitle}>
                {statusFilter === 'all'
                  ? "You haven't submitted any job applications yet. Browse positions matching your qualifications and apply."
                  : `No applications currently tagged as "${statusFilter}".`}
              </Text>
              <TouchableOpacity
                style={styles.actionBtn}
                onPress={() => router.push('/(tabs)')}>
                <Text style={styles.actionBtnText}>Explore Openings</Text>
              </TouchableOpacity>
            </View>
          }
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  employedHeaderCard: {
    marginHorizontal: SPACING.lg,
    marginTop: SPACING.md,
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.md,
    borderWidth: 1.5,
    borderColor: COLORS.emerald300,
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.md,
    ...SHADOWS.sm,
  },
  employedHeaderIcon: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: COLORS.emerald50,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  employedHeaderTag: {
    fontSize: 10,
    fontWeight: '800',
    color: COLORS.emerald800,
    textTransform: 'uppercase',
  },
  employedHeaderCompany: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.slate900,
    marginTop: 1,
  },
  resignationBtn: {
    backgroundColor: COLORS.surfaceSubtle,
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.sm,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  resignationBtnText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.slate700,
  },
  tabsWrapper: {
    backgroundColor: COLORS.white,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.borderLight,
    ...SHADOWS.sm,
  },
  tabsScrollView: {
    maxHeight: 52,
  },
  tabsScrollContent: {
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.sm,
    gap: SPACING.xs,
    flexDirection: 'row',
    alignItems: 'center',
  },
  tabItem: {
    paddingHorizontal: 14,
    paddingVertical: 7,
    borderRadius: BORDER_RADIUS.full,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.surfaceSubtle,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  tabItemActive: {
    backgroundColor: COLORS.primary,
    borderColor: COLORS.primary,
    ...SHADOWS.sm,
  },
  tabItemText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  tabItemTextActive: {
    color: COLORS.white,
  },
  tabItemOffer: {
    backgroundColor: COLORS.emerald50,
    borderColor: COLORS.emerald200,
    flexDirection: 'row',
    gap: 4,
  },
  tabItemOfferText: {
    color: COLORS.emerald800,
    fontWeight: '700',
  },
  listContent: {
    padding: SPACING.lg,
    gap: SPACING.md,
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  cardOffer: {
    borderWidth: 2,
    borderColor: COLORS.emerald400,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
  },
  avatarPlaceholder: {
    width: 40,
    height: 40,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarText: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.primaryDark,
  },
  jobTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: COLORS.navy,
    letterSpacing: -0.2,
  },
  companyName: {
    fontSize: 12,
    color: COLORS.textSecondary,
    fontWeight: '500',
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
  },
  statusBadgeText: {
    fontSize: 11,
    fontWeight: '700',
  },
  offerBox: {
    marginTop: SPACING.md,
    backgroundColor: COLORS.emerald50,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  offerHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 4,
  },
  offerTitle: {
    fontSize: 13,
    fontWeight: '800',
    color: COLORS.emerald800,
  },
  offerText: {
    fontSize: 12,
    color: COLORS.slate700,
    lineHeight: 16,
  },
  offerBtnRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
    marginTop: SPACING.md,
  },
  acceptOfferBtn: {
    flex: 1,
    backgroundColor: COLORS.primary,
    paddingVertical: 10,
    paddingHorizontal: SPACING.md,
    borderRadius: BORDER_RADIUS.md,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    ...SHADOWS.sm,
  },
  acceptOfferText: {
    fontSize: 12,
    fontWeight: '800',
    color: COLORS.white,
  },
  declineOfferBtn: {
    paddingVertical: 10,
    paddingHorizontal: SPACING.md,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.dangerBorder,
    backgroundColor: COLORS.white,
  },
  declineOfferText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.danger,
  },
  interviewBox: {
    backgroundColor: COLORS.infoBg,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    marginTop: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.infoBorder,
  },
  interviewHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 6,
  },
  interviewTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.info,
  },
  interviewRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginTop: 3,
  },
  interviewDetail: {
    fontSize: 12,
    color: COLORS.slate700,
    flex: 1,
  },
  meetingLinkBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 5,
    marginTop: 6,
    alignSelf: 'flex-start',
    backgroundColor: COLORS.white,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: BORDER_RADIUS.xs,
    borderWidth: 1,
    borderColor: COLORS.infoBorder,
    ...SHADOWS.sm,
  },
  meetingLinkText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.info,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: SPACING.md,
    paddingTop: SPACING.sm,
    borderTopWidth: 1,
    borderTopColor: COLORS.borderLight,
  },
  appliedDateContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  dateText: {
    fontSize: 12,
    color: COLORS.slate400,
    fontWeight: '500',
  },
  withdrawBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingVertical: 4,
    paddingHorizontal: 8,
  },
  withdrawBtnText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.danger,
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
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
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
    fontSize: 17,
    fontWeight: '700',
    color: COLORS.navy,
  },
  emptySubtitle: {
    fontSize: 13,
    color: COLORS.textMuted,
    textAlign: 'center',
    marginTop: SPACING.xs,
    maxWidth: 260,
    lineHeight: 18,
  },
  actionBtn: {
    marginTop: SPACING.lg,
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.xl,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
    ...SHADOWS.sm,
  },
  actionBtnText: {
    color: COLORS.white,
    fontWeight: '700',
    fontSize: 13,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.6)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.lg,
  },
  modalCard: {
    width: '100%',
    maxWidth: 400,
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    ...SHADOWS.lg,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  modalTitle: {
    fontSize: 17,
    fontWeight: '800',
    color: COLORS.slate900,
  },
  modalSub: {
    fontSize: 12,
    color: COLORS.slate600,
    lineHeight: 18,
    marginBottom: SPACING.md,
  },
  inputLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.slate700,
    marginBottom: 6,
  },
  modalInput: {
    backgroundColor: COLORS.slate50,
    borderWidth: 1,
    borderColor: COLORS.border,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    fontSize: 13,
    color: COLORS.slate900,
    textAlignVertical: 'top',
    minHeight: 80,
  },
  modalBtnRow: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    gap: SPACING.sm,
    marginTop: SPACING.lg,
  },
  modalCancelBtn: {
    paddingHorizontal: SPACING.md,
    paddingVertical: 8,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
  },
  modalCancelText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.slate600,
  },
  modalDeclineBtn: {
    backgroundColor: COLORS.danger,
    paddingHorizontal: SPACING.lg,
    paddingVertical: 8,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalDeclineText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.white,
  },
  modalSubmitResignBtn: {
    backgroundColor: COLORS.primaryDark,
    paddingHorizontal: SPACING.lg,
    paddingVertical: 8,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalSubmitResignText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.white,
  },
});

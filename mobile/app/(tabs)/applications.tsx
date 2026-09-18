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
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { applicationService } from '../../src/services/applicationService';
import { JobApplicationItem } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function ApplicationsScreen() {
  const router = useRouter();
  const { isAuthenticated } = useAuth();

  const [applications, setApplications] = useState<JobApplicationItem[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [statusFilter, setStatusFilter] = useState<'all' | 'pending' | 'review' | 'hired'>('all');

  const fetchApplications = useCallback(async () => {
    try {
      const data = await applicationService.getApplications();
      setApplications(data.applications);
    } catch (err) {
      console.warn('Error fetching applications:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      fetchApplications();
    } else {
      setLoading(false);
    }
  }, [isAuthenticated, fetchApplications]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchApplications();
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
    if (statusFilter === 'pending') {
      return s.includes('pend') || s.includes('submit');
    }
    if (statusFilter === 'review') {
      return s.includes('review') || s.includes('shortlist') || s.includes('interview');
    }
    if (statusFilter === 'hired') {
      return s.includes('hire') || s.includes('accept') || s.includes('placed');
    }
    return true;
  });

  const getStatusBadgeStyle = (status: string) => {
    const s = status.toLowerCase();
    if (s.includes('hire') || s.includes('placed') || s.includes('accept')) {
      return { bg: COLORS.successBg, text: COLORS.success, label: 'Hired / Placed' };
    }
    if (s.includes('shortlist')) {
      return { bg: COLORS.purpleBg, text: COLORS.purple, label: 'Shortlisted' };
    }
    if (s.includes('interview')) {
      return { bg: COLORS.infoBg, text: COLORS.info, label: 'Interview Scheduled' };
    }
    if (s.includes('review')) {
      return { bg: COLORS.infoBg, text: COLORS.info, label: 'Under Review' };
    }
    if (s.includes('reject') || s.includes('decline')) {
      return { bg: COLORS.dangerBg, text: COLORS.danger, label: 'Not Selected' };
    }
    return { bg: COLORS.warningBg, text: COLORS.warning, label: 'Pending Review' };
  };

  if (!isAuthenticated) {
    return (
      <View style={styles.centerContainer}>
        <Ionicons name="lock-closed-outline" size={64} color={COLORS.textMuted} />
        <Text style={styles.emptyTitle}>Sign In Required</Text>
        <Text style={styles.emptySubtitle}>
          Sign in to your TrabaGo Jobseeker account to track your job applications and interview invites.
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

    return (
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <View style={{ flex: 1 }}>
            <Text style={styles.jobTitle} numberOfLines={1}>
              {item.jobTitle || item.job_title || 'Position'}
            </Text>
            <Text style={styles.companyName} numberOfLines={1}>
              {item.company || item.company_name || 'DMDP Partner'}
            </Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: statusInfo.bg }]}>
            <Text style={[styles.statusBadgeText, { color: statusInfo.text }]}>
              {statusInfo.label}
            </Text>
          </View>
        </View>

        {/* Interview Schedule Notice if scheduled */}
        {(item.interviewSchedule || item.interview_schedule) && (
          <View style={styles.interviewBox}>
            <View style={styles.interviewHeader}>
              <Ionicons name="calendar" size={16} color={COLORS.info} />
              <Text style={styles.interviewTitle}>Interview Scheduled</Text>
            </View>
            <Text style={styles.interviewDetail}>
              Date: {item.interviewSchedule || item.interview_schedule}
            </Text>
            {(item.interviewLocation || item.interview_location) && (
              <Text style={styles.interviewDetail}>
                Location: {item.interviewLocation || item.interview_location}
              </Text>
            )}
            {(item.interviewMode || item.interview_mode) && (
              <Text style={styles.interviewDetail}>
                Mode: {item.interviewMode || item.interview_mode}
              </Text>
            )}
          </View>
        )}

        <View style={styles.cardFooter}>
          <View style={styles.appliedDateContainer}>
            <Ionicons name="time-outline" size={14} color={COLORS.textMuted} />
            <Text style={styles.dateText}>Applied: {dateFormatted}</Text>
          </View>

          <TouchableOpacity
            style={styles.withdrawBtn}
            onPress={() => handleWithdraw(item.id || item.application_id, item.jobTitle || item.job_title)}>
            <Ionicons name="trash-outline" size={14} color={COLORS.danger} />
            <Text style={styles.withdrawBtnText}>Withdraw</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      {/* Filter Tabs */}
      <View style={styles.tabsRow}>
        <TouchableOpacity
          style={[styles.tabItem, statusFilter === 'all' && styles.tabItemActive]}
          onPress={() => setStatusFilter('all')}>
          <Text style={[styles.tabItemText, statusFilter === 'all' && styles.tabItemTextActive]}>
            All ({applications.length})
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tabItem, statusFilter === 'pending' && styles.tabItemActive]}
          onPress={() => setStatusFilter('pending')}>
          <Text style={[styles.tabItemText, statusFilter === 'pending' && styles.tabItemTextActive]}>
            Pending
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tabItem, statusFilter === 'review' && styles.tabItemActive]}
          onPress={() => setStatusFilter('review')}>
          <Text style={[styles.tabItemText, statusFilter === 'review' && styles.tabItemTextActive]}>
            In Review
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tabItem, statusFilter === 'hired' && styles.tabItemActive]}
          onPress={() => setStatusFilter('hired')}>
          <Text style={[styles.tabItemText, statusFilter === 'hired' && styles.tabItemTextActive]}>
            Hired
          </Text>
        </TouchableOpacity>
      </View>

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
              <Ionicons name="document-text-outline" size={64} color={COLORS.border} />
              <Text style={styles.emptyTitle}>No applications found</Text>
              <Text style={styles.emptySubtitle}>
                {statusFilter === 'all'
                  ? "You haven't submitted any job applications yet."
                  : `No applications currently matching "${statusFilter}".`}
              </Text>
              <TouchableOpacity
                style={styles.actionBtn}
                onPress={() => router.push('/(tabs)')}>
                <Text style={styles.actionBtnText}>Explore Open Positions</Text>
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
  tabsRow: {
    flexDirection: 'row',
    backgroundColor: COLORS.white,
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.sm,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border,
    gap: SPACING.xs,
  },
  tabItem: {
    flex: 1,
    paddingVertical: 8,
    alignItems: 'center',
    borderRadius: BORDER_RADIUS.md,
  },
  tabItemActive: {
    backgroundColor: COLORS.primaryUltraLight,
  },
  tabItemText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  tabItemTextActive: {
    color: COLORS.primaryDark,
    fontWeight: 'bold',
  },
  listContent: {
    padding: SPACING.lg,
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
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
  },
  jobTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  companyName: {
    fontSize: 13,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: SPACING.sm,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
    marginLeft: SPACING.sm,
  },
  statusBadgeText: {
    fontSize: 11,
    fontWeight: 'bold',
  },
  interviewBox: {
    backgroundColor: COLORS.infoBg,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    marginTop: SPACING.md,
    borderLeftWidth: 3,
    borderLeftColor: COLORS.info,
  },
  interviewHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 4,
  },
  interviewTitle: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.info,
  },
  interviewDetail: {
    fontSize: 12,
    color: COLORS.text,
    marginTop: 2,
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
    fontSize: 11,
    color: COLORS.textMuted,
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
    color: COLORS.danger,
    fontWeight: '600',
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
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
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
    maxWidth: 260,
  },
  actionBtn: {
    marginTop: SPACING.lg,
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.xl,
    paddingVertical: SPACING.md,
    borderRadius: BORDER_RADIUS.md,
  },
  actionBtnText: {
    color: COLORS.white,
    fontWeight: 'bold',
    fontSize: 14,
  },
});

import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TextInput,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { jobService } from '../../src/services/jobService';
import { JobItem } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function JobsScreen() {
  const router = useRouter();
  const { user, isAuthenticated } = useAuth();

  const [jobs, setJobs] = useState<JobItem[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedFilter, setSelectedFilter] = useState<'all' | 'fulltime' | 'pwd'>('all');

  const fetchJobs = useCallback(async () => {
    try {
      const data = await jobService.getJobs({ q: searchQuery });
      setJobs(data);
    } catch (err) {
      console.warn('Error fetching jobs:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [searchQuery]);

  useEffect(() => {
    fetchJobs();
  }, [fetchJobs]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchJobs();
  };

  const filteredJobs = jobs.filter((job) => {
    if (selectedFilter === 'pwd') {
      return job.accepts_disability;
    }
    if (selectedFilter === 'fulltime') {
      return (job.type || job.employment_type || '').toLowerCase().includes('full');
    }
    return true;
  });

  const renderJobCard = ({ item }: { item: JobItem }) => {
    return (
      <TouchableOpacity
        style={styles.card}
        activeOpacity={0.85}
        onPress={() => router.push({ pathname: '/jobs/[id]', params: { id: item.id || item.job_id } })}>
        <View style={styles.cardHeader}>
          <View style={styles.avatarPlaceholder}>
            <Text style={styles.avatarText}>
              {(item.company || item.company_name || 'DMDP').charAt(0).toUpperCase()}
            </Text>
          </View>
          <View style={styles.headerTextContainer}>
            <Text style={styles.jobTitle} numberOfLines={1}>
              {item.title}
            </Text>
            <Text style={styles.companyName} numberOfLines={1}>
              {item.company || item.company_name || 'DMDP Partner Employer'}
            </Text>
          </View>
        </View>

        <View style={styles.infoRow}>
          <View style={styles.infoItem}>
            <Ionicons name="location-outline" size={14} color={COLORS.textMuted} />
            <Text style={styles.infoText}>{item.location || 'Cebu City'}</Text>
          </View>
          <View style={styles.infoItem}>
            <Ionicons name="cash-outline" size={14} color={COLORS.primary} />
            <Text style={[styles.infoText, { color: COLORS.primaryDark, fontWeight: '600' }]}>
              {item.salary || item.salary_expectation || 'Competitive'}
            </Text>
          </View>
        </View>

        <View style={styles.badgeRow}>
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{item.type || item.employment_type || 'Full-time'}</Text>
          </View>
          {item.accepts_disability && (
            <View style={[styles.badge, styles.pwdBadge]}>
              <Ionicons name="accessibility" size={12} color={COLORS.purple} style={{ marginRight: 4 }} />
              <Text style={styles.pwdBadgeText}>PWD Inclusive</Text>
            </View>
          )}
          {item.vacancy_count > 1 && (
            <View style={[styles.badge, styles.vacancyBadge]}>
              <Text style={styles.vacancyBadgeText}>{item.vacancy_count} Openings</Text>
            </View>
          )}
        </View>

        <View style={styles.cardFooter}>
          <Text style={styles.dateText}>
            Posted: {item.created_at ? item.created_at.split('T')[0] : 'Recently'}
          </Text>
          <View style={styles.viewDetailsBtn}>
            <Text style={styles.viewDetailsText}>View Details</Text>
            <Ionicons name="arrow-forward" size={14} color={COLORS.primary} />
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <View style={styles.container}>
      {/* Search and Filters Header */}
      <View style={styles.searchSection}>
        <View style={styles.searchBar}>
          <Ionicons name="search" size={20} color={COLORS.textMuted} style={styles.searchIcon} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search job title, skills, or company..."
            placeholderTextColor={COLORS.textMuted}
            value={searchQuery}
            onChangeText={setSearchQuery}
            returnKeyType="search"
            onSubmitEditing={fetchJobs}
          />
          {searchQuery.length > 0 && (
            <TouchableOpacity onPress={() => setSearchQuery('')}>
              <Ionicons name="close-circle" size={18} color={COLORS.textMuted} />
            </TouchableOpacity>
          )}
        </View>

        {/* Filter Pills */}
        <View style={styles.filterPills}>
          <TouchableOpacity
            style={[styles.pill, selectedFilter === 'all' && styles.pillActive]}
            onPress={() => setSelectedFilter('all')}>
            <Text style={[styles.pillText, selectedFilter === 'all' && styles.pillTextActive]}>
              All Jobs
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.pill, selectedFilter === 'fulltime' && styles.pillActive]}
            onPress={() => setSelectedFilter('fulltime')}>
            <Text style={[styles.pillText, selectedFilter === 'fulltime' && styles.pillTextActive]}>
              Full-time
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.pill, selectedFilter === 'pwd' && styles.pillActive]}
            onPress={() => setSelectedFilter('pwd')}>
            <Ionicons
              name="accessibility-outline"
              size={13}
              color={selectedFilter === 'pwd' ? COLORS.white : COLORS.purple}
              style={{ marginRight: 4 }}
            />
            <Text style={[styles.pillText, selectedFilter === 'pwd' && styles.pillTextActive]}>
              PWD Friendly
            </Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* Auth Banner if guest */}
      {!isAuthenticated && (
        <TouchableOpacity
          style={styles.guestBanner}
          onPress={() => router.push('/auth/login')}>
          <View style={{ flex: 1 }}>
            <Text style={styles.guestBannerTitle}>Welcome to DMDP TrabaGo</Text>
            <Text style={styles.guestBannerSubtitle}>Sign in to apply and track your job applications.</Text>
          </View>
          <View style={styles.guestBannerBtn}>
            <Text style={styles.guestBannerBtnText}>Sign In</Text>
          </View>
        </TouchableOpacity>
      )}

      {/* Main Jobs List */}
      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
          <Text style={styles.loadingText}>Fetching available positions...</Text>
        </View>
      ) : (
        <FlatList
          data={filteredJobs}
          keyExtractor={(item) => String(item.id || item.job_id || Math.random())}
          renderItem={renderJobCard}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="briefcase-outline" size={64} color={COLORS.border} />
              <Text style={styles.emptyTitle}>No positions found</Text>
              <Text style={styles.emptySubtitle}>
                Try adjusting your search terms or filter criteria.
              </Text>
              <TouchableOpacity style={styles.resetBtn} onPress={() => { setSearchQuery(''); setSelectedFilter('all'); fetchJobs(); }}>
                <Text style={styles.resetBtnText}>Reset Filters</Text>
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
  searchSection: {
    backgroundColor: COLORS.white,
    paddingHorizontal: SPACING.lg,
    paddingTop: SPACING.md,
    paddingBottom: SPACING.md,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border,
  },
  searchBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.md,
    paddingHorizontal: SPACING.md,
    height: 44,
  },
  searchIcon: {
    marginRight: SPACING.sm,
  },
  searchInput: {
    flex: 1,
    fontSize: 14,
    color: COLORS.text,
  },
  filterPills: {
    flexDirection: 'row',
    marginTop: SPACING.sm,
    gap: SPACING.sm,
  },
  pill: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: SPACING.md,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.full,
    backgroundColor: COLORS.surfaceSubtle,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  pillActive: {
    backgroundColor: COLORS.primary,
    borderColor: COLORS.primary,
  },
  pillText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  pillTextActive: {
    color: COLORS.white,
  },
  guestBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
    marginHorizontal: SPACING.lg,
    marginTop: SPACING.md,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.primaryLight,
  },
  guestBannerTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
  },
  guestBannerSubtitle: {
    fontSize: 12,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  guestBannerBtn: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.md,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.sm,
    marginLeft: SPACING.sm,
  },
  guestBannerBtnText: {
    color: COLORS.white,
    fontSize: 12,
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
    alignItems: 'center',
  },
  avatarPlaceholder: {
    width: 44,
    height: 44,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.primaryUltraLight,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: SPACING.md,
  },
  avatarText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
  },
  headerTextContainer: {
    flex: 1,
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
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: SPACING.md,
    gap: SPACING.lg,
  },
  infoItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  infoText: {
    fontSize: 13,
    color: COLORS.textSecondary,
  },
  badgeRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.sm,
    marginTop: SPACING.md,
  },
  badge: {
    backgroundColor: COLORS.surfaceSubtle,
    paddingHorizontal: SPACING.sm,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
  },
  badgeText: {
    fontSize: 11,
    color: COLORS.textSecondary,
    fontWeight: '500',
  },
  pwdBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.purpleBg,
  },
  pwdBadgeText: {
    fontSize: 11,
    color: COLORS.purple,
    fontWeight: '600',
  },
  vacancyBadge: {
    backgroundColor: COLORS.infoBg,
  },
  vacancyBadgeText: {
    fontSize: 11,
    color: COLORS.info,
    fontWeight: '600',
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
  dateText: {
    fontSize: 11,
    color: COLORS.textMuted,
  },
  viewDetailsBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  viewDetailsText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.primary,
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
    maxWidth: 240,
  },
  resetBtn: {
    marginTop: SPACING.lg,
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.sm,
    borderRadius: BORDER_RADIUS.md,
  },
  resetBtnText: {
    color: COLORS.white,
    fontWeight: 'bold',
    fontSize: 13,
  },
});

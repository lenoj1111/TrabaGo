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
  ScrollView,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { jobService } from '../../src/services/jobService';
import { JobItem, DashboardData } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function JobsScreen() {
  const router = useRouter();
  const { user, isAuthenticated } = useAuth();

  const [jobs, setJobs] = useState<JobItem[]>([]);
  const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedFilter, setSelectedFilter] = useState<'all' | 'match' | 'fulltime' | 'pwd'>('all');

  const fetchJobs = useCallback(async () => {
    try {
      const [jobsData, dash] = await Promise.all([
        jobService.getJobs({ q: searchQuery }),
        isAuthenticated ? jobService.getDashboard() : Promise.resolve(null),
      ]);
      setJobs(jobsData);
      if (dash) {
        setDashboardData(dash);
      }
    } catch (err) {
      console.warn('Error fetching jobs:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [searchQuery, isAuthenticated]);

  const userId = user?.id || user?.user_id;

  useEffect(() => {
    fetchJobs();
  }, [fetchJobs, userId, isAuthenticated]);

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
    if (selectedFilter === 'match') {
      return (job.match_percentage ?? 0) > 0;
    }
    return true;
  }).sort((a, b) => {
    if (selectedFilter === 'match') {
      return (b.match_percentage ?? 0) - (a.match_percentage ?? 0);
    }
    return 0;
  });

  const bestMatchJob = dashboardData?.best_match || jobs.find((j) => (j.match_percentage ?? 0) >= 60);
  const profileStrength = dashboardData?.profile_strength ?? user?.profile_strength ?? user?.profileStrength ?? 30;
  const isEmployed = dashboardData?.is_employed ?? user?.is_employed;
  const hiredCompany = dashboardData?.hired_company ?? user?.hired_company;

  const renderJobCard = ({ item }: { item: JobItem }) => {
    const companyInitial = (item.company || item.company_name || 'DMDP').charAt(0).toUpperCase();
    const hasMatchScore = typeof item.match_percentage === 'number' && item.match_percentage > 0;

    return (
      <TouchableOpacity
        style={styles.card}
        activeOpacity={0.88}
        onPress={() => router.push({ pathname: '/jobs/[id]', params: { id: item.id || item.job_id } })}>
        {/* Card Top Row: Avatar & Title */}
        <View style={styles.cardHeader}>
          <View style={styles.avatarPlaceholder}>
            <Text style={styles.avatarText}>{companyInitial}</Text>
          </View>
          <View style={styles.headerTextContainer}>
            <View style={styles.titleTopRow}>
              <Text style={styles.jobTitle} numberOfLines={1}>
                {item.title}
              </Text>
              {item.has_applied && (
                <View style={styles.appliedBadge}>
                  <Ionicons name="checkmark-circle" size={12} color={COLORS.emerald700} />
                  <Text style={styles.appliedBadgeText}>Applied</Text>
                </View>
              )}
            </View>
            <View style={styles.companyRow}>
              <Text style={styles.companyName} numberOfLines={1}>
                {item.company || item.company_name || 'DMDP Partner Employer'}
              </Text>
              <Ionicons name="shield-checkmark" size={13} color={COLORS.primary} style={{ marginLeft: 4 }} />
            </View>
          </View>
        </View>

        {/* Location & Compensation */}
        <View style={styles.infoRow}>
          <View style={styles.infoItem}>
            <Ionicons name="location-outline" size={15} color={COLORS.slate500} />
            <Text style={styles.infoText} numberOfLines={1}>{item.location || 'Cebu City'}</Text>
          </View>
          <View style={styles.salaryChip}>
            <Ionicons name="cash-outline" size={13} color={COLORS.emerald700} />
            <Text style={styles.salaryText}>
              {item.salary || item.salary_expectation || 'Competitive'}
            </Text>
          </View>
        </View>

        {/* Feature Badges */}
        <View style={styles.badgeRow}>
          <View style={styles.badge}>
            <Ionicons name="time-outline" size={12} color={COLORS.slate600} style={{ marginRight: 3 }} />
            <Text style={styles.badgeText}>{item.type || item.employment_type || 'Full-time'}</Text>
          </View>

          {hasMatchScore && (
            <View style={styles.matchBadge}>
              <Ionicons name="sparkles" size={11} color={COLORS.emerald800} style={{ marginRight: 3 }} />
              <Text style={styles.matchBadgeText}>{item.match_percentage}% AI Match</Text>
            </View>
          )}

          {item.accepts_disability && (
            <View style={[styles.badge, styles.pwdBadge]}>
              <Ionicons name="accessibility" size={12} color={COLORS.purple} style={{ marginRight: 4 }} />
              <Text style={styles.pwdBadgeText}>PWD Inclusive</Text>
            </View>
          )}

          {item.vacancy_count > 1 && (
            <View style={[styles.badge, styles.vacancyBadge]}>
              <Ionicons name="people-outline" size={12} color={COLORS.info} style={{ marginRight: 3 }} />
              <Text style={styles.vacancyBadgeText}>{item.vacancy_count} Openings</Text>
            </View>
          )}
        </View>

        {/* Matched Skills Chips */}
        {item.matched_skills && item.matched_skills.length > 0 && (
          <View style={styles.matchedSkillsRow}>
            <Text style={styles.matchedSkillsLabel}>Matched:</Text>
            {item.matched_skills.slice(0, 3).map((sk: string, idx: number) => (
              <View key={idx} style={styles.skillMatchChip}>
                <Ionicons name="checkmark" size={10} color={COLORS.emerald700} />
                <Text style={styles.skillMatchChipText}>{sk}</Text>
              </View>
            ))}
          </View>
        )}

        {/* Card Footer */}
        <View style={styles.cardFooter}>
          <Text style={styles.dateText}>
            Posted {item.created_at ? item.created_at.split('T')[0] : 'Recently'}
          </Text>
          <View style={styles.viewDetailsBtn}>
            <Text style={styles.viewDetailsText}>View Details</Text>
            <Ionicons name="arrow-forward" size={14} color={COLORS.primary} />
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  const renderHeader = () => {
    return (
      <View style={styles.headerContainer}>
        {/* Guest Welcome & Sign In Callout */}
        {!isAuthenticated && (
          <TouchableOpacity
            style={styles.guestBanner}
            activeOpacity={0.9}
            onPress={() => router.push('/auth/login')}>
            <View style={styles.guestBannerIcon}>
              <Ionicons name="person" size={20} color={COLORS.primary} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.guestBannerTitle}>Welcome to DMDP TrabaGo</Text>
              <Text style={styles.guestBannerSubtitle}>Sign in to apply with your NSRP profile & track offers.</Text>
            </View>
            <View style={styles.guestBannerBtn}>
              <Text style={styles.guestBannerBtnText}>Sign In</Text>
            </View>
          </TouchableOpacity>
        )}

        {/* Employment Placement Success Banner */}
        {isAuthenticated && isEmployed && (
          <View style={styles.employedBanner}>
            <View style={styles.employedIconBox}>
              <Ionicons name="checkmark-done-circle" size={24} color={COLORS.emerald700} />
            </View>
            <View style={{ flex: 1 }}>
              <View style={styles.employedTagRow}>
                <Text style={styles.employedTag}>Verified Employed</Text>
                <Text style={styles.employedSubTag}>DMDP Official Placement</Text>
              </View>
              <Text style={styles.employedTitle}>Placed at {hiredCompany || 'DMDP Partner Employer'}</Text>
            </View>
          </View>
        )}

        {/* AI Top Skill-Match Hero Banner */}
        {isAuthenticated && bestMatchJob && (
          <View style={styles.aiMatchHero}>
            <View style={styles.aiMatchTopRow}>
              <View style={styles.aiBadgePill}>
                <Ionicons name="sparkles" size={13} color={COLORS.emerald700} />
                <Text style={styles.aiBadgeText}>AI Skill-Match #1 Recommendation</Text>
              </View>
              {bestMatchJob.match_percentage ? (
                <View style={styles.aiScoreBadge}>
                  <Text style={styles.aiScoreText}>{bestMatchJob.match_percentage}% Match</Text>
                </View>
              ) : null}
            </View>

            <Text style={styles.aiMatchTitle} numberOfLines={1}>{bestMatchJob.title}</Text>
            <Text style={styles.aiMatchCompany}>{bestMatchJob.company || bestMatchJob.company_name} • Cebu City</Text>
            <Text style={styles.aiMatchSalary}>{bestMatchJob.salary || '₱18,000 - ₱35,000 / mo'}</Text>

            {bestMatchJob.matched_skills && bestMatchJob.matched_skills.length > 0 && (
              <View style={styles.aiSkillsRow}>
                <Text style={styles.aiSkillsLabel}>Matching Skills:</Text>
                {bestMatchJob.matched_skills.slice(0, 3).map((sk: string, idx: number) => (
                  <View key={idx} style={styles.aiSkillChip}>
                    <Ionicons name="checkmark-circle" size={11} color={COLORS.emerald700} />
                    <Text style={styles.aiSkillChipText}>{sk}</Text>
                  </View>
                ))}
              </View>
            )}

            <TouchableOpacity
              style={styles.aiMatchBtn}
              activeOpacity={0.88}
              onPress={() => router.push({ pathname: '/jobs/[id]', params: { id: bestMatchJob.id || bestMatchJob.job_id } })}>
              <Text style={styles.aiMatchBtnText}>View AI Recommended Position</Text>
              <Ionicons name="arrow-forward" size={14} color={COLORS.white} />
            </TouchableOpacity>
          </View>
        )}

        {/* Profile Strength Progress Card */}
        {isAuthenticated && profileStrength < 100 && (
          <View style={styles.strengthCard}>
            <View style={styles.strengthTopRow}>
              <View style={{ flex: 1 }}>
                <Text style={styles.strengthTitle}>Profile Strength</Text>
                <Text style={styles.strengthSubtitle}>
                  {profileStrength >= 80 ? 'Almost complete! Add certifications for max employer visibility.' : 'Complete your NSRP profile details to boost job interview invites.'}
                </Text>
              </View>
              <Text style={styles.strengthPercentText}>{profileStrength}%</Text>
            </View>
            <View style={styles.strengthProgressBarBg}>
              <View style={[styles.strengthProgressBarFill, { width: `${profileStrength}%` }]} />
            </View>
            <TouchableOpacity
              style={styles.strengthActionBtn}
              onPress={() => router.push('/profile/edit')}>
              <Text style={styles.strengthActionText}>Complete Profile Details &rarr;</Text>
            </TouchableOpacity>
          </View>
        )}

        {/* Section Title & Count */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Available Positions</Text>
          <View style={styles.countBadge}>
            <Text style={styles.countBadgeText}>{filteredJobs.length} openings</Text>
          </View>
        </View>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      {/* Sticky Search and Filter Header */}
      <View style={styles.searchSection}>
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={18} color={COLORS.slate400} style={styles.searchIcon} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search title, skills, or company..."
            placeholderTextColor={COLORS.slate400}
            value={searchQuery}
            onChangeText={setSearchQuery}
            returnKeyType="search"
            onSubmitEditing={fetchJobs}
          />
          {searchQuery.length > 0 && (
            <TouchableOpacity onPress={() => setSearchQuery('')} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="close-circle" size={18} color={COLORS.slate400} />
            </TouchableOpacity>
          )}
        </View>

        {/* Horizontal Scrollable Filter Pills */}
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.filterPillsContainer}
          style={styles.filterPillsScroll}>
          <TouchableOpacity
            style={[styles.pill, selectedFilter === 'all' && styles.pillActive]}
            onPress={() => setSelectedFilter('all')}>
            <Text style={[styles.pillText, selectedFilter === 'all' && styles.pillTextActive]}>
              All Jobs
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.pill, selectedFilter === 'match' && styles.pillActive]}
            onPress={() => setSelectedFilter('match')}>
            <Ionicons
              name="sparkles"
              size={12}
              color={selectedFilter === 'match' ? COLORS.white : COLORS.emerald700}
              style={{ marginRight: 4 }}
            />
            <Text style={[styles.pillText, selectedFilter === 'match' && styles.pillTextActive]}>
              AI Matched
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
        </ScrollView>
      </View>

      {/* Main Feed */}
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
          ListHeaderComponent={renderHeader}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <View style={styles.emptyIconCircle}>
                <Ionicons name="briefcase-outline" size={36} color={COLORS.slate400} />
              </View>
              <Text style={styles.emptyTitle}>No positions found</Text>
              <Text style={styles.emptySubtitle}>
                Try adjusting your search terms or filter criteria.
              </Text>
              <TouchableOpacity
                style={styles.resetBtn}
                onPress={() => { setSearchQuery(''); setSelectedFilter('all'); fetchJobs(); }}>
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
    paddingBottom: SPACING.sm,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.borderLight,
    ...SHADOWS.sm,
  },
  searchBar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.xl,
    paddingHorizontal: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
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
  filterPillsScroll: {
    marginTop: SPACING.sm,
    marginBottom: 4,
  },
  filterPillsContainer: {
    gap: SPACING.xs,
    paddingRight: SPACING.md,
  },
  pill: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 14,
    paddingVertical: 7,
    borderRadius: BORDER_RADIUS.full,
    backgroundColor: COLORS.surfaceSubtle,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  pillActive: {
    backgroundColor: COLORS.primary,
    borderColor: COLORS.primary,
    ...SHADOWS.sm,
  },
  pillText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.textSecondary,
  },
  pillTextActive: {
    color: COLORS.white,
  },
  headerContainer: {
    gap: SPACING.md,
    marginBottom: SPACING.xs,
  },
  guestBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
    borderRadius: BORDER_RADIUS.xl,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
    ...SHADOWS.sm,
  },
  guestBannerIcon: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: COLORS.white,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
  },
  guestBannerTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.primaryDark,
  },
  guestBannerSubtitle: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  guestBannerBtn: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: 14,
    paddingVertical: 8,
    borderRadius: BORDER_RADIUS.md,
    marginLeft: SPACING.sm,
  },
  guestBannerBtnText: {
    color: COLORS.white,
    fontSize: 12,
    fontWeight: '700',
  },
  employedBanner: {
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
  employedIconBox: {
    width: 44,
    height: 44,
    borderRadius: BORDER_RADIUS.lg,
    backgroundColor: COLORS.emerald50,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  employedTagRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  employedTag: {
    fontSize: 11,
    fontWeight: '800',
    color: COLORS.emerald800,
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 6,
    paddingVertical: 1,
    borderRadius: 4,
  },
  employedSubTag: {
    fontSize: 10,
    color: COLORS.slate500,
    fontWeight: '600',
  },
  employedTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.slate900,
    marginTop: 2,
  },
  aiMatchHero: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    borderWidth: 1.5,
    borderColor: COLORS.emerald300,
    ...SHADOWS.md,
  },
  aiMatchTopRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.xs,
  },
  aiBadgePill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  aiBadgeText: {
    fontSize: 10,
    fontWeight: '800',
    color: COLORS.emerald800,
  },
  aiScoreBadge: {
    backgroundColor: COLORS.primaryDark,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.full,
  },
  aiScoreText: {
    fontSize: 11,
    fontWeight: '800',
    color: COLORS.white,
  },
  aiMatchTitle: {
    fontSize: 17,
    fontWeight: '800',
    color: COLORS.slate900,
    marginTop: SPACING.xs,
  },
  aiMatchCompany: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.slate500,
    marginTop: 2,
  },
  aiMatchSalary: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.emerald700,
    marginTop: 4,
  },
  aiSkillsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    flexWrap: 'wrap',
    gap: 5,
    marginTop: SPACING.sm,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: COLORS.borderLight,
  },
  aiSkillsLabel: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.slate400,
    textTransform: 'uppercase',
  },
  aiSkillChip: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 3,
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.xs,
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  aiSkillChipText: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.emerald800,
  },
  aiMatchBtn: {
    marginTop: SPACING.md,
    backgroundColor: COLORS.primary,
    borderRadius: BORDER_RADIUS.md,
    paddingVertical: 10,
    paddingHorizontal: SPACING.md,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    ...SHADOWS.sm,
  },
  aiMatchBtnText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.white,
  },
  strengthCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  strengthTopRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  strengthTitle: {
    fontSize: 12,
    fontWeight: '800',
    color: COLORS.slate800,
  },
  strengthSubtitle: {
    fontSize: 10,
    color: COLORS.slate500,
    marginTop: 1,
  },
  strengthPercentText: {
    fontSize: 14,
    fontWeight: '900',
    color: COLORS.emerald700,
    marginLeft: SPACING.sm,
  },
  strengthProgressBarBg: {
    height: 6,
    backgroundColor: COLORS.slate100,
    borderRadius: 3,
    marginTop: 8,
    overflow: 'hidden',
  },
  strengthProgressBarFill: {
    height: '100%',
    backgroundColor: COLORS.primary,
    borderRadius: 3,
  },
  strengthActionBtn: {
    marginTop: 6,
    alignSelf: 'flex-start',
  },
  strengthActionText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.primaryDark,
  },
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: SPACING.xs,
    marginTop: SPACING.xs,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.navy,
  },
  countBadge: {
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
    borderColor: COLORS.emerald200,
  },
  countBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.emerald800,
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
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  avatarPlaceholder: {
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
  avatarText: {
    fontSize: 18,
    fontWeight: '800',
    color: COLORS.primaryDark,
  },
  headerTextContainer: {
    flex: 1,
  },
  titleTopRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 8,
  },
  jobTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: COLORS.navy,
    letterSpacing: -0.2,
    flex: 1,
  },
  appliedBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 3,
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
  },
  appliedBadgeText: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.primaryDark,
  },
  companyRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 2,
  },
  companyName: {
    fontSize: 13,
    color: COLORS.textSecondary,
    fontWeight: '500',
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: SPACING.md,
    paddingVertical: 2,
  },
  infoItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    flex: 1,
  },
  infoText: {
    fontSize: 13,
    color: COLORS.textSecondary,
  },
  salaryChip: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
  },
  salaryText: {
    fontSize: 12,
    color: COLORS.emerald800,
    fontWeight: '700',
  },
  badgeRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.xs,
    marginTop: SPACING.sm,
  },
  badge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.surfaceSubtle,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
    borderWidth: 1,
    borderColor: COLORS.borderLight,
  },
  badgeText: {
    fontSize: 11,
    color: COLORS.textSecondary,
    fontWeight: '600',
  },
  matchBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
  },
  matchBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.emerald800,
  },
  pwdBadge: {
    backgroundColor: COLORS.purpleBg,
    borderColor: COLORS.purpleBorder,
  },
  pwdBadgeText: {
    fontSize: 11,
    color: COLORS.purple,
    fontWeight: '700',
  },
  vacancyBadge: {
    backgroundColor: COLORS.infoBg,
    borderColor: COLORS.infoBorder,
  },
  vacancyBadgeText: {
    fontSize: 11,
    color: COLORS.info,
    fontWeight: '700',
  },
  matchedSkillsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    flexWrap: 'wrap',
    gap: 5,
    marginTop: SPACING.sm,
    paddingTop: 6,
    borderTopWidth: 1,
    borderTopColor: COLORS.borderLight,
  },
  matchedSkillsLabel: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.slate400,
    textTransform: 'uppercase',
  },
  skillMatchChip: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 3,
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.xs,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
  },
  skillMatchChipText: {
    fontSize: 10,
    fontWeight: '600',
    color: COLORS.emerald800,
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
    fontWeight: '500',
  },
  viewDetailsBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  viewDetailsText: {
    fontSize: 13,
    fontWeight: '700',
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
    maxWidth: 240,
    lineHeight: 18,
  },
  resetBtn: {
    marginTop: SPACING.lg,
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.xl,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
    ...SHADOWS.sm,
  },
  resetBtnText: {
    color: COLORS.white,
    fontWeight: '700',
    fontSize: 13,
  },
});

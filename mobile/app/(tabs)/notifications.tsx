import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { notificationService } from '../../src/services/notificationService';
import { NotificationItem } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';
import { getEcho } from '../../src/services/echoService';

export default function NotificationsScreen() {
  const router = useRouter();
  const { user, isAuthenticated } = useAuth();
  const userId = user?.id || user?.user_id;

  const [notifications, setNotifications] = useState<NotificationItem[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [refreshing, setRefreshing] = useState<boolean>(false);

  const fetchNotifications = useCallback(async () => {
    try {
      const data = await notificationService.getNotifications();
      setNotifications(data);
    } catch (err) {
      console.warn('Error fetching notifications:', err);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated && userId) {
      setLoading(true);
      fetchNotifications();
    } else {
      setNotifications([]);
      setLoading(false);
    }
  }, [isAuthenticated, userId, fetchNotifications]);

  useEffect(() => {
    if (!userId || !isAuthenticated) return;
    let channelSubscription: any;

    getEcho().then((echo) => {
      if (!echo || typeof echo.private !== 'function') return;
      channelSubscription = echo.private(`App.Models.User.${userId}`);
      if (channelSubscription && typeof channelSubscription.listen === 'function') {
        channelSubscription.listen('.notification.created', (event: { notification: NotificationItem }) => {
          setNotifications((prev) => [event.notification, ...prev]);
        });
      }
    }).catch((err) => {
      console.warn('Echo subscription notice:', err?.message || err);
    });

    return () => {
      if (channelSubscription?.stopListening) {
        channelSubscription.stopListening('.notification.created');
      }
    };
  }, [userId, isAuthenticated]);

  const onRefresh = () => {
    setRefreshing(true);
    fetchNotifications();
  };

  const handleMarkAsRead = async (id: number | string) => {
    await notificationService.markAsRead(id);
    setNotifications((prev) =>
      prev.map((n) => (n.id === id ? { ...n, is_read: true } : n))
    );
  };

  if (!isAuthenticated) {
    return (
      <View style={styles.centerContainer}>
        <View style={styles.emptyIconCircle}>
          <Ionicons name="lock-closed-outline" size={36} color={COLORS.slate400} />
        </View>
        <Text style={styles.emptyTitle}>Sign In Required</Text>
        <Text style={styles.emptySubtitle}>
          Sign in to your TrabaGo Jobseeker account to view application status updates and notifications.
        </Text>
        <TouchableOpacity style={styles.actionBtn} onPress={() => router.push('/auth/login')}>
          <Text style={styles.actionBtnText}>Sign In Now</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const renderNotificationCard = ({ item }: { item: NotificationItem }) => {
    const isRead = item.is_read;
    const dateFormatted = item.created_at ? item.created_at.split('T')[0] : 'Today';

    return (
      <TouchableOpacity
        style={[styles.card, !isRead && styles.unreadCard]}
        activeOpacity={0.85}
        onPress={() => handleMarkAsRead(item.id)}>
        <View style={styles.cardRow}>
          <View style={[styles.iconBox, !isRead ? styles.iconBoxUnread : styles.iconBoxRead]}>
            <Ionicons
              name={!isRead ? 'notifications' : 'notifications-outline'}
              size={20}
              color={!isRead ? COLORS.primaryDark : COLORS.slate400}
            />
          </View>
          <View style={styles.contentContainer}>
            <View style={styles.titleRow}>
              <Text style={[styles.title, !isRead && styles.unreadTitle]} numberOfLines={1}>
                {item.title}
              </Text>
              {!isRead && <View style={styles.unreadDot} />}
            </View>
            <Text style={styles.message} numberOfLines={3}>
              {item.message}
            </Text>
            <View style={styles.metaRow}>
              <Ionicons name="time-outline" size={13} color={COLORS.slate400} />
              <Text style={styles.date}>{dateFormatted}</Text>
            </View>
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  const unreadCount = notifications.filter((n) => !n.is_read).length;

  const handleMarkAllAsRead = async () => {
    await notificationService.markAllAsRead();
    setNotifications((prev) => prev.map((n) => ({ ...n, is_read: true })));
  };

  return (
    <View style={styles.container}>
      {notifications.length > 0 && !loading && (
        <View style={styles.topBar}>
          <View style={styles.topBarLeft}>
            <View style={[styles.statusDot, unreadCount > 0 ? styles.statusDotActive : styles.statusDotInactive]} />
            <Text style={styles.topBarText}>
              {unreadCount > 0 ? `${unreadCount} unread alert${unreadCount > 1 ? 's' : ''}` : 'All alerts read'}
            </Text>
          </View>
          {unreadCount > 0 && (
            <TouchableOpacity style={styles.markAllBtn} onPress={handleMarkAllAsRead} activeOpacity={0.8}>
              <Ionicons name="checkmark-done" size={15} color={COLORS.primary} />
              <Text style={styles.markAllText}>Mark all read</Text>
            </TouchableOpacity>
          )}
        </View>
      )}

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
          <Text style={styles.loadingText}>Checking notifications...</Text>
        </View>
      ) : (
        <FlatList
          data={notifications}
          keyExtractor={(item) => String(item.id || Math.random())}
          renderItem={renderNotificationCard}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[COLORS.primary]} />}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <View style={styles.emptyIconCircle}>
                <Ionicons name="notifications-off-outline" size={36} color={COLORS.slate400} />
              </View>
              <Text style={styles.emptyTitle}>No notifications yet</Text>
              <Text style={styles.emptySubtitle}>
                You will receive alerts here about application updates and interview schedules.
              </Text>
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
  topBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: COLORS.white,
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border,
    ...SHADOWS.sm,
  },
  topBarLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  statusDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  statusDotActive: {
    backgroundColor: COLORS.primary,
  },
  statusDotInactive: {
    backgroundColor: COLORS.slate400,
  },
  topBarText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.navy,
  },
  markAllBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
  },
  markAllText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.primaryDark,
  },
  listContent: {
    padding: SPACING.lg,
    gap: SPACING.sm,
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  unreadCard: {
    backgroundColor: COLORS.white,
    borderColor: COLORS.successBorder,
    borderLeftWidth: 4,
    borderLeftColor: COLORS.primary,
  },
  cardRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  iconBox: {
    width: 40,
    height: 40,
    borderRadius: BORDER_RADIUS.lg,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: SPACING.md,
  },
  iconBoxUnread: {
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
  },
  iconBoxRead: {
    backgroundColor: COLORS.surfaceSubtle,
  },
  contentContainer: {
    flex: 1,
  },
  titleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  title: {
    fontSize: 14,
    color: COLORS.navy,
    flex: 1,
    fontWeight: '500',
  },
  unreadTitle: {
    fontWeight: '700',
    color: COLORS.navy,
  },
  unreadDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: COLORS.primary,
    marginLeft: SPACING.sm,
  },
  message: {
    fontSize: 13,
    color: COLORS.textSecondary,
    marginTop: 4,
    lineHeight: 18,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: 6,
  },
  date: {
    fontSize: 11,
    color: COLORS.slate400,
    fontWeight: '500',
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
});

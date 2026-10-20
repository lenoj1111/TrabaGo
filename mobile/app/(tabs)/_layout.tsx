import React, { useState, useEffect } from 'react';
import { Tabs } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import { Platform } from 'react-native';

import { COLORS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';
import { notificationService } from '../../src/services/notificationService';
import { getEcho } from '../../src/services/echoService';

export default function TabLayout() {
  const { user, isAuthenticated } = useAuth();
  const userId = user?.id || user?.user_id;
  const [unreadCount, setUnreadCount] = useState<number>(0);

  useEffect(() => {
    if (!isAuthenticated || !userId) {
      setUnreadCount(0);
      return;
    }

    notificationService.getNotifications().then((notifs) => {
      const count = notifs.filter((n) => !n.is_read).length;
      setUnreadCount(count);
    }).catch(() => {});

    let channelSubscription: any;
    getEcho().then((echo) => {
      if (!echo || typeof echo.private !== 'function') return;
      channelSubscription = echo.private(`App.Models.User.${userId}`);
      if (channelSubscription && typeof channelSubscription.listen === 'function') {
        channelSubscription.listen('.notification.created', () => {
          setUnreadCount((prev) => prev + 1);
        });
      }
    }).catch(() => {});

    return () => {
      if (channelSubscription?.stopListening) {
        channelSubscription.stopListening('.notification.created');
      }
    };
  }, [isAuthenticated, userId]);

  return (
    <Tabs
      screenOptions={{
        tabBarActiveTintColor: COLORS.primary,
        tabBarInactiveTintColor: COLORS.slate400,
        tabBarStyle: {
          backgroundColor: COLORS.white,
          borderTopColor: COLORS.borderLight,
          borderTopWidth: 1,
          height: Platform.OS === 'ios' ? 88 : 68,
          paddingBottom: Platform.OS === 'ios' ? 26 : 10,
          paddingTop: 8,
          ...SHADOWS.md,
        },
        tabBarLabelStyle: {
          fontSize: 11,
          fontWeight: '700',
          letterSpacing: 0.1,
          marginTop: 2,
        },
        headerStyle: {
          backgroundColor: COLORS.white,
          elevation: 0,
          shadowOpacity: 0,
          borderBottomWidth: 1,
          borderBottomColor: COLORS.borderLight,
        },
        headerTintColor: COLORS.navy,
        headerTitleStyle: {
          fontWeight: '800',
          fontSize: 18,
          letterSpacing: -0.3,
          color: COLORS.navy,
        },
      }}>
      <Tabs.Screen
        name="index"
        options={{
          title: 'Explore',
          headerTitle: 'TrabaGo Cebu Jobs',
          tabBarIcon: ({ color, focused }) => (
            <Ionicons name={focused ? 'briefcase' : 'briefcase-outline'} size={22} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="applications"
        options={{
          title: 'Applications',
          headerTitle: 'My Applications',
          tabBarIcon: ({ color, focused }) => (
            <Ionicons name={focused ? 'document-text' : 'document-text-outline'} size={22} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="training"
        options={{
          title: 'Training',
          headerTitle: 'DMDP Skills & Courses',
          tabBarIcon: ({ color, focused }) => (
            <Ionicons name={focused ? 'school' : 'school-outline'} size={22} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="notifications"
        options={{
          title: 'Alerts',
          headerTitle: 'Notifications',
          tabBarBadge: unreadCount > 0 ? (unreadCount > 99 ? '99+' : unreadCount) : undefined,
          tabBarBadgeStyle: {
            backgroundColor: COLORS.danger,
            color: COLORS.white,
            fontSize: 10,
            fontWeight: 'bold',
          },
          tabBarIcon: ({ color, focused }) => (
            <Ionicons name={focused ? 'notifications' : 'notifications-outline'} size={22} color={color} />
          ),
        }}
        listeners={{
          tabPress: () => {
            setUnreadCount(0);
          },
        }}
      />
      <Tabs.Screen
        name="profile"
        options={{
          title: 'Profile',
          headerTitle: 'Jobseeker Profile',
          tabBarIcon: ({ color, focused }) => (
            <Ionicons name={focused ? 'person' : 'person-outline'} size={22} color={color} />
          ),
        }}
      />
    </Tabs>
  );
}

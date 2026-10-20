import { apiClient } from './apiClient';
import { NotificationItem } from '../types';

export const notificationService = {
  async getNotifications(category?: string): Promise<NotificationItem[]> {
    try {
      const response = await apiClient.get('/notifications', { params: { category } });
      if (Array.isArray(response.data)) {
        return response.data;
      }
      if (response.data && Array.isArray(response.data.data)) {
        return response.data.data;
      }
      return [];
    } catch (err: any) {
      console.warn('Error fetching notifications:', err.message);
      return [];
    }
  },

  async markAsRead(notificationId: number | string): Promise<boolean> {
    try {
      await apiClient.patch(`/notifications/${notificationId}/read`);
      return true;
    } catch (err: any) {
      console.warn(`Error marking notification ${notificationId} as read:`, err.message);
      return false;
    }
  },

  async markAllAsRead(): Promise<boolean> {
    try {
      await apiClient.post('/notifications/mark-all-read');
      return true;
    } catch (err: any) {
      console.warn('Error marking all notifications as read:', err.message);
      return false;
    }
  },
};

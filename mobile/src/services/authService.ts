import { apiClient, AUTH_TOKEN_KEY, USER_DATA_KEY } from './apiClient';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { UserProfile } from '../types';

export interface LoginResponse {
  success: boolean;
  token?: string;
  user?: UserProfile;
  message?: string;
}

export interface RegisterData {
  first_name: string;
  last_name: string;
  middle_name?: string;
  email: string;
  password: string;
  password_confirmation: string;
  mobile_number?: string;
  education?: string;
  skills?: string[];
  sex?: string;
  civil_status?: string;
  birth_date?: string;
}

export const authService = {
  async login(email: string, password: string): Promise<LoginResponse> {
    try {
      const response = await apiClient.post('/auth/login', { email, password });
      const data = response.data;
      if (data.token && data.user) {
        await AsyncStorage.setItem(AUTH_TOKEN_KEY, data.token);
        await AsyncStorage.setItem(USER_DATA_KEY, JSON.stringify(data.user));
      }
      return data;
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || err.message || 'Login failed. Please check credentials.',
      };
    }
  },

  async register(registerData: RegisterData): Promise<LoginResponse> {
    try {
      const response = await apiClient.post('/auth/register', registerData);
      const data = response.data;
      if (data.token && data.user) {
        await AsyncStorage.setItem(AUTH_TOKEN_KEY, data.token);
        await AsyncStorage.setItem(USER_DATA_KEY, JSON.stringify(data.user));
      }
      return data;
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || err.message || 'Registration failed.',
      };
    }
  },

  async getProfile(): Promise<{ success: boolean; user?: UserProfile; message?: string }> {
    try {
      const response = await apiClient.get('/auth/profile');
      const userData = response.data.user || response.data.data;
      if (userData) {
        await AsyncStorage.setItem(USER_DATA_KEY, JSON.stringify(userData));
      }
      return { success: true, user: userData };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to fetch profile',
      };
    }
  },

  async updateProfile(updates: Partial<UserProfile>): Promise<{ success: boolean; user?: UserProfile; message?: string }> {
    try {
      const response = await apiClient.put('/auth/profile', updates);
      const userData = response.data.user || response.data.data;
      if (userData) {
        await AsyncStorage.setItem(USER_DATA_KEY, JSON.stringify(userData));
      }
      return { success: true, user: userData };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to update profile',
      };
    }
  },

  async addSkill(skillName: string): Promise<{ success: boolean; skills?: string[]; message?: string }> {
    try {
      const response = await apiClient.post('/auth/skills/add', { skill_name: skillName });
      return {
        success: true,
        skills: response.data.skills,
        message: response.data.message,
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to add skill',
      };
    }
  },

  async removeSkill(idOrName: string | number): Promise<{ success: boolean; skills?: string[]; message?: string }> {
    try {
      const response = await apiClient.delete(`/auth/skills/${idOrName}`);
      return {
        success: true,
        skills: response.data.skills,
        message: response.data.message,
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to remove skill',
      };
    }
  },

  async changePassword(currentPassword: string, newPassword: string, confirmPassword: string): Promise<{ success: boolean; message?: string }> {
    try {
      const response = await apiClient.post('/auth/change-password', {
        current_password: currentPassword,
        password: newPassword,
        password_confirmation: confirmPassword,
      });
      return {
        success: true,
        message: response.data?.message || 'Password changed successfully.',
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to change password.',
      };
    }
  },

  async logout(): Promise<void> {
    try {
      await apiClient.post('/auth/logout');
    } catch {
      // Ignore network errors on logout
    } finally {
      await AsyncStorage.removeItem(AUTH_TOKEN_KEY);
      await AsyncStorage.removeItem(USER_DATA_KEY);
    }
  },

  async getStoredUser(): Promise<UserProfile | null> {
    try {
      const raw = await AsyncStorage.getItem(USER_DATA_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch {
      return null;
    }
  },

  async getStoredToken(): Promise<string | null> {
    try {
      return await AsyncStorage.getItem(AUTH_TOKEN_KEY);
    } catch {
      return null;
    }
  },
};

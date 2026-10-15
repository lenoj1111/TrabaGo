import axios, { AxiosError } from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { getDefaultApiUrl, getApiBaseUrl } from '../config/api';

export const AUTH_TOKEN_KEY = 'TRABAGO_AUTH_TOKEN';
export const USER_DATA_KEY = 'TRABAGO_USER_DATA';

export const apiClient = axios.create({
  baseURL: getDefaultApiUrl(),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 15000,
});

// Dynamic Base URL and Bearer Token Interceptor
apiClient.interceptors.request.use(async (config) => {
  const dynamicUrl = await getApiBaseUrl();
  config.baseURL = dynamicUrl;

  const token = await AsyncStorage.getItem(AUTH_TOKEN_KEY);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => Promise.reject(error));

// Error Normalization Interceptor (error-handling-patterns)
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError<any>) => {
    // 1. Session expiration
    if (error.response?.status === 401) {
      await AsyncStorage.removeItem(AUTH_TOKEN_KEY);
      error.message = 'Session expired. Please log in again to continue.';
      return Promise.reject(error);
    }

    // 2. Network / Offline errors
    if (!error.response) {
      if (error.code === 'ECONNABORTED' || error.message?.includes('timeout')) {
        error.message = 'Connection timed out. TrabaGo server took too long to respond.';
      } else {
        error.message = 'Cannot connect to TrabaGo server. Please check your Wi-Fi or mobile data.';
      }
      return Promise.reject(error);
    }

    // 3. HTTP status-specific normalization
    const serverMessage = error.response.data?.message || error.response.data?.error;
    if (serverMessage && typeof serverMessage === 'string') {
      error.message = serverMessage;
    } else if (error.response.status === 403) {
      error.message = 'Access forbidden. Your account does not have permission for this action.';
    } else if (error.response.status === 404) {
      error.message = 'The requested record or resource was not found.';
    } else if (error.response.status === 422) {
      const errors = error.response.data?.errors;
      if (errors && typeof errors === 'object') {
        const firstErr = Object.values(errors).flat()[0];
        error.message = (firstErr as string) || 'Validation error. Please check your input.';
      } else {
        error.message = 'Validation failed. Please review the submitted details.';
      }
    } else if (error.response.status >= 500) {
      error.message = 'A server error occurred. Our engineering team has been notified.';
    }

    return Promise.reject(error);
  }
);

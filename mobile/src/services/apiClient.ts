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

// Error Interceptor
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    if (error.response?.status === 401) {
      // Unauthenticated, clear cached token
      await AsyncStorage.removeItem(AUTH_TOKEN_KEY);
    }
    return Promise.reject(error);
  }
);

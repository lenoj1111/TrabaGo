import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Local Machine IP on Ethernet/Wi-Fi for physical device testing
export const DEFAULT_LAN_IP = '192.168.100.9';
export const DEFAULT_PORT = '8000';

const STORAGE_KEY_BASE_URL = 'TRABAGO_API_BASE_URL';

/**
 * Determine default API base URL depending on platform
 */
export const getDefaultApiUrl = (): string => {
  if (Platform.OS === 'web') {
    return `http://localhost:${DEFAULT_PORT}/api`;
  }
  
  // Physical device or network emulator default to host machine LAN IP
  return `http://${DEFAULT_LAN_IP}:${DEFAULT_PORT}/api`;
};

let currentApiUrl = getDefaultApiUrl();

export const getApiBaseUrl = async (): Promise<string> => {
  try {
    const saved = await AsyncStorage.getItem(STORAGE_KEY_BASE_URL);
    if (saved) {
      currentApiUrl = saved;
      return saved;
    }
  } catch {
    // fallback
  }
  return currentApiUrl;
};

export const setApiBaseUrl = async (url: string): Promise<void> => {
  currentApiUrl = url.replace(/\/+$/, '');
  await AsyncStorage.setItem(STORAGE_KEY_BASE_URL, currentApiUrl);
};

export const getCurrentApiUrlSync = (): string => currentApiUrl;

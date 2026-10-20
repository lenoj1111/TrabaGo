import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Constants from 'expo-constants';

// Local Machine IP on Ethernet/Wi-Fi for physical device testing
export const DEFAULT_LAN_IP = '192.168.1.6';
export const DEFAULT_PORT = '8000';

const STORAGE_KEY_BASE_URL = 'TRABAGO_API_BASE_URL';

/**
 * Dynamically extract host IP from Expo Metro connection if running in Expo Go
 */
const getHostFromExpo = (): string | null => {
  try {
    const hostUri =
      Constants.expoConfig?.hostUri ||
      (Constants as any).manifest?.debuggerHost ||
      (Constants as any).manifest2?.extra?.expoClient?.hostUri;
    if (hostUri) {
      const host = hostUri.split(':')[0];
      if (host && host !== 'localhost' && host !== '127.0.0.1') {
        return host;
      }
    }
  } catch {
    // Ignore error
  }
  return null;
};

/**
 * Determine default API base URL depending on platform & active packager host
 */
export const getDefaultApiUrl = (): string => {
  if (process.env.EXPO_PUBLIC_API_URL) {
    return process.env.EXPO_PUBLIC_API_URL;
  }

  if (Platform.OS === 'web') {
    return `http://localhost:${DEFAULT_PORT}/api`;
  }

  // Detect host from active Expo packager if available
  const expoHost = getHostFromExpo();
  if (expoHost) {
    return `http://${expoHost}:${DEFAULT_PORT}/api`;
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

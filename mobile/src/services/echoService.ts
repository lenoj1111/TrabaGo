import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { AUTH_TOKEN_KEY } from './apiClient';
import { getApiBaseUrl, DEFAULT_LAN_IP } from '../config/api';

// Safely resolve the Pusher constructor function (handles CJS/ESM interop)
const resolvePusherConstructor = (importedPusher: any) => {
  if (typeof importedPusher === 'function') {
    return importedPusher;
  }
  if (importedPusher && typeof importedPusher.default === 'function') {
    return importedPusher.default;
  }
  if (importedPusher && typeof importedPusher.Pusher === 'function') {
    return importedPusher.Pusher;
  }
  return importedPusher;
};

const PusherClient = resolvePusherConstructor(Pusher);

// Register constructor in global scope for Laravel Echo
if (typeof PusherClient === 'function') {
  (globalThis as any).Pusher = PusherClient;
}

let echoInstance: Echo<'reverb'> | null = null;

export const getEcho = async (): Promise<Echo<'reverb'> | null> => {
  if (echoInstance) return echoInstance;

  try {
    if (typeof PusherClient !== 'function') {
      console.warn('Pusher constructor could not be resolved.');
      return null;
    }

    const token = await AsyncStorage.getItem(AUTH_TOKEN_KEY);
    const baseUrl = await getApiBaseUrl();

    let host = DEFAULT_LAN_IP;
    try {
      const matches = baseUrl.match(/^https?:\/\/([^:/]+)/);
      if (matches && matches[1]) {
        host = matches[1];
      }
    } catch {
      host = DEFAULT_LAN_IP;
    }

    echoInstance = new Echo({
      broadcaster: 'reverb',
      Pusher: PusherClient,
      key: 'ckzj1ifqrv7u8cszy3tq',
      wsHost: host,
      wsPort: 8080,
      wssPort: 8080,
      forceTLS: false,
      enabledTransports: ['ws'],
      authEndpoint: `${baseUrl.replace(/\/+$/, '')}/broadcasting/auth`,
      auth: {
        headers: {
          Accept: 'application/json',
          Authorization: token ? `Bearer ${token}` : '',
        },
      },
    });

    return echoInstance;
  } catch (error) {
    console.warn('Echo/Pusher initialization skipped or failed:', error);
    return null;
  }
};

export const disconnectEcho = () => {
  try {
    if (echoInstance) {
      echoInstance.disconnect();
      echoInstance = null;
    }
  } catch {
    // Ignore disconnect error
  }
};

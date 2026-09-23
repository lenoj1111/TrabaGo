import { useFonts } from 'expo-font';
import { Stack } from 'expo-router';
import * as SplashScreen from 'expo-splash-screen';
import { useEffect } from 'react';
import { StatusBar } from 'expo-status-bar';
import 'react-native-reanimated';

import { AuthProvider } from '../src/context/AuthContext';
import { COLORS } from '../src/constants/theme';

export {
  ErrorBoundary,
} from 'expo-router';

export const unstable_settings = {
  initialRouteName: '(tabs)',
};

SplashScreen.preventAutoHideAsync();

export default function RootLayout() {
  const [loaded, error] = useFonts({
    SpaceMono: require('../assets/fonts/SpaceMono-Regular.ttf'),
  });

  useEffect(() => {
    if (error) throw error;
  }, [error]);

  useEffect(() => {
    if (loaded) {
      SplashScreen.hideAsync();
    }
  }, [loaded]);

  if (!loaded) {
    return null;
  }

  return (
    <AuthProvider>
      <StatusBar style="light" />
      <Stack
        screenOptions={{
          headerStyle: {
            backgroundColor: COLORS.navy,
          },
          headerTintColor: COLORS.white,
          headerTitleStyle: {
            fontWeight: 'bold',
          },
          contentStyle: {
            backgroundColor: COLORS.background,
          },
        }}>
        <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
        <Stack.Screen
          name="auth/login"
          options={{
            headerShown: false,
            animation: 'fade',
          }}
        />
        <Stack.Screen
          name="auth/register"
          options={{
            title: 'Jobseeker Registration',
            headerBackTitle: 'Back',
          }}
        />
        <Stack.Screen
          name="jobs/[id]"
          options={{
            title: 'Job Details',
            headerBackTitle: 'Jobs',
          }}
        />
        <Stack.Screen
          name="jobs/apply"
          options={{
            title: 'Apply for Position',
            presentation: 'modal',
          }}
        />
        <Stack.Screen
          name="training/[id]"
          options={{
            title: 'DMDP Course Assessment',
          }}
        />
        <Stack.Screen
          name="profile/vault"
          options={{
            title: 'Credentials Vault',
          }}
        />
        <Stack.Screen
          name="profile/edit"
          options={{
            title: 'Edit Profile',
          }}
        />
      </Stack>
    </AuthProvider>
  );
}

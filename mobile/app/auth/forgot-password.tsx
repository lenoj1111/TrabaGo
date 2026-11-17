import React, { useState } from 'react';
import {
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { authService } from '../../src/services/authService';

export default function ForgotPasswordScreen() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async () => {
    const normalizedEmail = email.trim();
    if (!normalizedEmail) {
      setError('Enter the email address used for your account.');
      return;
    }

    setLoading(true);
    setError('');
    const result = await authService.requestPasswordReset(normalizedEmail);
    setLoading(false);

    if (result.success) {
      setSubmitted(true);
    } else {
      setError(result.message || 'Unable to request a password reset.');
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
        <View style={styles.header}>
          <View style={styles.logoBadge}>
            <Ionicons name="key-outline" size={30} color={COLORS.primary} />
          </View>
          <Text style={styles.title}>Reset your password</Text>
          <Text style={styles.subtitle}>We’ll email a secure link to the address on your account.</Text>
        </View>

        <View style={styles.card}>
          {submitted ? (
            <View style={styles.confirmation}>
              <Ionicons name="mail-open-outline" size={30} color={COLORS.primary} />
              <Text style={styles.confirmationText}>
                If an account exists for that email, a password reset link has been sent.
              </Text>
            </View>
          ) : (
            <>
              <Text style={styles.label}>Email address</Text>
              <View style={styles.inputContainer}>
                <Ionicons name="mail-outline" size={19} color={COLORS.slate400} style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="jobseeker@example.com"
                  placeholderTextColor={COLORS.slate400}
                  value={email}
                  onChangeText={setEmail}
                  keyboardType="email-address"
                  autoCapitalize="none"
                  autoCorrect={false}
                  autoComplete="email"
                  accessibilityLabel="Email address"
                  onSubmitEditing={handleSubmit}
                  returnKeyType="send"
                />
              </View>
              {error ? <Text style={styles.errorText}>{error}</Text> : null}

              <TouchableOpacity
                style={[styles.submitButton, loading && styles.submitButtonDisabled]}
                activeOpacity={0.88}
                onPress={handleSubmit}
                disabled={loading}>
                {loading ? (
                  <ActivityIndicator color={COLORS.white} />
                ) : (
                  <Text style={styles.submitButtonText}>Send reset link</Text>
                )}
              </TouchableOpacity>
            </>
          )}

          <TouchableOpacity style={styles.backButton} onPress={() => router.replace('/auth/login')}>
            <Ionicons name="arrow-back" size={16} color={COLORS.primary} />
            <Text style={styles.backButtonText}>Back to sign in</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: SPACING.xl,
  },
  header: {
    alignItems: 'center',
    marginBottom: SPACING.xl,
  },
  logoBadge: {
    width: 64,
    height: 64,
    borderRadius: BORDER_RADIUS.xl,
    backgroundColor: COLORS.primaryUltraLight,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.md,
    ...SHADOWS.sm,
  },
  title: {
    color: COLORS.slate900,
    fontSize: 24,
    fontWeight: '700',
    textAlign: 'center',
  },
  subtitle: {
    color: COLORS.slate500,
    fontSize: 14,
    lineHeight: 20,
    marginTop: SPACING.sm,
    maxWidth: 300,
    textAlign: 'center',
  },
  card: {
    backgroundColor: COLORS.white,
    borderColor: COLORS.slate200,
    borderRadius: BORDER_RADIUS.lg,
    borderWidth: 1,
    padding: SPACING.xl,
    ...SHADOWS.sm,
  },
  label: {
    color: COLORS.slate700,
    fontSize: 13,
    fontWeight: '600',
    marginBottom: SPACING.sm,
  },
  inputContainer: {
    alignItems: 'center',
    borderColor: COLORS.slate200,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    flexDirection: 'row',
    minHeight: 50,
    paddingHorizontal: SPACING.md,
  },
  inputIcon: {
    marginRight: SPACING.sm,
  },
  input: {
    color: COLORS.slate900,
    flex: 1,
    fontSize: 15,
    minHeight: 48,
  },
  errorText: {
    color: COLORS.danger,
    fontSize: 12,
    marginTop: SPACING.sm,
  },
  submitButton: {
    alignItems: 'center',
    backgroundColor: COLORS.primary,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
    marginTop: SPACING.lg,
    minHeight: 50,
  },
  submitButtonDisabled: {
    opacity: 0.7,
  },
  submitButtonText: {
    color: COLORS.white,
    fontSize: 15,
    fontWeight: '700',
  },
  confirmation: {
    alignItems: 'center',
    gap: SPACING.md,
    paddingVertical: SPACING.md,
  },
  confirmationText: {
    color: COLORS.slate700,
    fontSize: 14,
    lineHeight: 21,
    textAlign: 'center',
  },
  backButton: {
    alignItems: 'center',
    flexDirection: 'row',
    gap: SPACING.sm,
    justifyContent: 'center',
    marginTop: SPACING.lg,
    minHeight: 40,
  },
  backButtonText: {
    color: COLORS.primary,
    fontSize: 14,
    fontWeight: '600',
  },
});
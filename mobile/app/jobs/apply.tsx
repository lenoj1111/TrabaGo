import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  ScrollView,
  Platform,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as DocumentPicker from 'expo-document-picker';
import { Ionicons } from '@expo/vector-icons';

import { applicationService } from '../../src/services/applicationService';
import { documentService } from '../../src/services/documentService';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function ApplyJobModal() {
  const params = useLocalSearchParams<{ jobId: string; jobTitle?: string; company?: string }>();
  const router = useRouter();
  const { user } = useAuth();

  const [coverNote, setCoverNote] = useState('');
  const [selectedFile, setSelectedFile] = useState<{ uri: string; name: string; size?: number; mimeType?: string } | null>(null);
  const [loading, setLoading] = useState(false);

  const handlePickDocument = async () => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        copyToCacheDirectory: true,
      });

      if (!result.canceled && result.assets && result.assets.length > 0) {
        const file = result.assets[0];
        setSelectedFile({
          uri: file.uri,
          name: file.name,
          size: file.size,
          mimeType: file.mimeType,
        });
      }
    } catch (err: any) {
      Alert.alert('Error', 'Unable to pick document: ' + err.message);
    }
  };

  const handleSubmit = async () => {
    if (!params.jobId) {
      Alert.alert('Error', 'Job ID is missing.');
      return;
    }

    setLoading(true);
    try {
      let uploadedFilePath: string | undefined;

      // If document was selected, upload first
      if (selectedFile) {
        const uploadRes = await documentService.uploadDocument(
          selectedFile.uri,
          selectedFile.name,
          selectedFile.mimeType || 'application/pdf',
          'resume'
        );
        if (uploadRes.success && uploadRes.filePath) {
          uploadedFilePath = uploadRes.filePath;
        }
      }

      const res = await applicationService.apply({
        jobId: params.jobId,
        coverNote: coverNote.trim(),
        resumePath: uploadedFilePath,
      });

      if (res.success) {
        Alert.alert(
          'Application Submitted!',
          'Your profile and application have been forwarded to the employer and Cebu DMDP.',
          [
            {
              text: 'View Applications',
              onPress: () => {
                router.replace('/(tabs)/applications');
              },
            },
          ]
        );
      } else {
        Alert.alert('Submission Failed', res.message || 'Could not submit application.');
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'An unexpected error occurred.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent}>
      {/* Position Header */}
      <View style={styles.headerBox}>
        <Text style={styles.headerLabel}>Applying for</Text>
        <Text style={styles.headerTitle}>{params.jobTitle || 'Job Position'}</Text>
        <Text style={styles.headerCompany}>{params.company || 'DMDP Employer'}</Text>
      </View>

      {/* Applicant Card */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Applicant Profile</Text>
        <Text style={styles.cardSubtitle}>Your registered DMDP credentials will be submitted</Text>

        <View style={styles.profileRow}>
          <Ionicons name="person-outline" size={16} color={COLORS.primary} />
          <Text style={styles.profileText}>{user?.fullName || 'Registered Jobseeker'}</Text>
        </View>
        <View style={styles.profileRow}>
          <Ionicons name="mail-outline" size={16} color={COLORS.primary} />
          <Text style={styles.profileText}>{user?.email}</Text>
        </View>
        <View style={styles.profileRow}>
          <Ionicons name="call-outline" size={16} color={COLORS.primary} />
          <Text style={styles.profileText}>{user?.phone || user?.mobile_number || 'No contact number'}</Text>
        </View>
      </View>

      {/* Cover Note */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Message / Cover Note (Optional)</Text>
        <Text style={styles.cardSubtitle}>
          Highlight relevant experience or vocational certificates
        </Text>
        <TextInput
          style={styles.textArea}
          placeholder="Briefly describe your background, availability, and why you are a great fit for this role..."
          placeholderTextColor={COLORS.textMuted}
          multiline
          numberOfLines={4}
          value={coverNote}
          onChangeText={setCoverNote}
          textAlignVertical="top"
        />
      </View>

      {/* Resume Attachment Picker */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Attach Resume / CV</Text>
        <Text style={styles.cardSubtitle}>PDF or DOCX document (Max 5MB)</Text>

        {selectedFile ? (
          <View style={styles.fileBox}>
            <Ionicons name="document-text" size={28} color={COLORS.primary} />
            <View style={{ flex: 1, marginLeft: 10 }}>
              <Text style={styles.fileName} numberOfLines={1}>
                {selectedFile.name}
              </Text>
              {selectedFile.size && (
                <Text style={styles.fileSize}>
                  {(selectedFile.size / 1024).toFixed(1)} KB
                </Text>
              )}
            </View>
            <TouchableOpacity onPress={() => setSelectedFile(null)}>
              <Ionicons name="close-circle" size={20} color={COLORS.danger} />
            </TouchableOpacity>
          </View>
        ) : (
          <TouchableOpacity style={styles.uploadBtn} onPress={handlePickDocument}>
            <Ionicons name="cloud-upload-outline" size={24} color={COLORS.primary} />
            <Text style={styles.uploadBtnText}>Choose File from Device</Text>
          </TouchableOpacity>
        )}
      </View>

      {/* Submit Button */}
      <TouchableOpacity
        style={styles.submitBtn}
        onPress={handleSubmit}
        disabled={loading}>
        {loading ? (
          <ActivityIndicator color={COLORS.white} />
        ) : (
          <>
            <Text style={styles.submitBtnText}>Confirm & Submit Application</Text>
            <Ionicons name="checkmark-circle" size={18} color={COLORS.white} />
          </>
        )}
      </TouchableOpacity>

      <TouchableOpacity style={styles.cancelBtn} onPress={() => router.back()}>
        <Text style={styles.cancelBtnText}>Cancel</Text>
      </TouchableOpacity>

      <View style={{ height: 40 }} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  scrollContent: {
    padding: SPACING.lg,
    gap: SPACING.md,
  },
  headerBox: {
    backgroundColor: COLORS.navy,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.lg,
  },
  headerLabel: {
    fontSize: 11,
    color: COLORS.primaryLight,
    fontWeight: 'bold',
    textTransform: 'uppercase',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 2,
  },
  headerCompany: {
    fontSize: 13,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  cardTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  cardSubtitle: {
    fontSize: 12,
    color: COLORS.textMuted,
    marginTop: 2,
    marginBottom: SPACING.md,
  },
  profileRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingVertical: 4,
  },
  profileText: {
    fontSize: 13,
    color: COLORS.text,
  },
  textArea: {
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    padding: SPACING.md,
    fontSize: 13,
    color: COLORS.text,
    minHeight: 100,
  },
  uploadBtn: {
    borderWidth: 1.5,
    borderStyle: 'dashed',
    borderColor: COLORS.primaryLight,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.primaryUltraLight,
    paddingVertical: 18,
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
  },
  uploadBtnText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.primaryDark,
  },
  fileBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  fileName: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.navy,
  },
  fileSize: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  submitBtn: {
    backgroundColor: COLORS.primary,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 14,
    borderRadius: BORDER_RADIUS.md,
    gap: 8,
    marginTop: SPACING.sm,
  },
  submitBtnText: {
    color: COLORS.white,
    fontSize: 15,
    fontWeight: 'bold',
  },
  cancelBtn: {
    alignItems: 'center',
    paddingVertical: 10,
  },
  cancelBtnText: {
    color: COLORS.textMuted,
    fontSize: 13,
  },
});

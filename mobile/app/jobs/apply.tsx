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
        const isRequirementsError = res.message?.toLowerCase().includes('missing') ||
          res.message?.toLowerCase().includes('profile details') ||
          res.message?.toLowerCase().includes('document') ||
          res.message?.toLowerCase().includes('completed');

        if (isRequirementsError) {
          Alert.alert(
            'Application Locked',
            res.message || 'Please complete your profile details and upload required documents before applying.',
            [
              { text: 'Cancel', style: 'cancel' },
              { text: 'Complete Profile', onPress: () => router.push('/profile/edit') },
              { text: 'Document Vault', onPress: () => router.push('/profile/vault') },
            ]
          );
        } else {
          Alert.alert('Submission Failed', res.message || 'Could not submit application.');
        }
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'An unexpected error occurred.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
      {/* Position Header */}
      <View style={styles.headerBox}>
        <View style={styles.badgePill}>
          <Text style={styles.badgePillText}>APPLYING FOR POSITION</Text>
        </View>
        <Text style={styles.headerTitle}>{params.jobTitle || 'Job Position'}</Text>
        <Text style={styles.headerCompany}>{params.company || 'DMDP Employer'}</Text>
      </View>

      {/* Applicant Card */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Applicant Profile</Text>
        <Text style={styles.cardSubtitle}>Your registered DMDP credentials will be submitted</Text>

        <View style={styles.profileRow}>
          <View style={styles.profileIconBox}>
            <Ionicons name="person-outline" size={16} color={COLORS.primary} />
          </View>
          <Text style={styles.profileText}>{user?.fullName || 'Registered Jobseeker'}</Text>
        </View>
        <View style={styles.profileRow}>
          <View style={styles.profileIconBox}>
            <Ionicons name="mail-outline" size={16} color={COLORS.primary} />
          </View>
          <Text style={styles.profileText}>{user?.email}</Text>
        </View>
        <View style={styles.profileRow}>
          <View style={styles.profileIconBox}>
            <Ionicons name="call-outline" size={16} color={COLORS.primary} />
          </View>
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
          placeholderTextColor={COLORS.slate400}
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
            <View style={styles.fileIconBox}>
              <Ionicons name="document-text" size={24} color={COLORS.primary} />
            </View>
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
            <TouchableOpacity onPress={() => setSelectedFile(null)} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="close-circle" size={22} color={COLORS.danger} />
            </TouchableOpacity>
          </View>
        ) : (
          <TouchableOpacity style={styles.uploadBtn} activeOpacity={0.85} onPress={handlePickDocument}>
            <View style={styles.uploadIconCircle}>
              <Ionicons name="cloud-upload-outline" size={22} color={COLORS.primary} />
            </View>
            <Text style={styles.uploadBtnText}>Choose File from Device</Text>
            <Text style={styles.uploadBtnSub}>Supported formats: PDF, DOC, DOCX</Text>
          </TouchableOpacity>
        )}
      </View>

      {/* Submit Button */}
      <TouchableOpacity
        style={styles.submitBtn}
        activeOpacity={0.88}
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
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    ...SHADOWS.md,
  },
  badgePill: {
    alignSelf: 'flex-start',
    backgroundColor: 'rgba(5, 150, 105, 0.25)',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.full,
    marginBottom: 6,
  },
  badgePillText: {
    fontSize: 10,
    color: COLORS.primaryLight,
    fontWeight: '700',
    letterSpacing: 0.5,
  },
  headerTitle: {
    fontSize: 19,
    fontWeight: '700',
    color: COLORS.white,
    letterSpacing: -0.2,
  },
  headerCompany: {
    fontSize: 13,
    color: COLORS.slate300,
    marginTop: 2,
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  cardTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: COLORS.navy,
  },
  cardSubtitle: {
    fontSize: 12,
    color: COLORS.slate400,
    marginTop: 2,
    marginBottom: SPACING.md,
  },
  profileRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 6,
  },
  profileIconBox: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: COLORS.primaryUltraLight,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: SPACING.sm,
  },
  profileText: {
    fontSize: 13,
    color: COLORS.text,
    fontWeight: '500',
  },
  textArea: {
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.lg,
    borderWidth: 1,
    borderColor: COLORS.border,
    padding: SPACING.md,
    fontSize: 13,
    color: COLORS.text,
    minHeight: 110,
    lineHeight: 19,
  },
  uploadBtn: {
    borderWidth: 1.5,
    borderStyle: 'dashed',
    borderColor: COLORS.primaryLight,
    borderRadius: BORDER_RADIUS.xl,
    backgroundColor: COLORS.emerald50,
    paddingVertical: 20,
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
  },
  uploadIconCircle: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 4,
    borderWidth: 1,
    borderColor: COLORS.successBorder,
  },
  uploadBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.primaryDark,
  },
  uploadBtnSub: {
    fontSize: 11,
    color: COLORS.slate500,
  },
  fileBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  fileIconBox: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: COLORS.primaryUltraLight,
    justifyContent: 'center',
    alignItems: 'center',
  },
  fileName: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.navy,
  },
  fileSize: {
    fontSize: 11,
    color: COLORS.slate400,
    marginTop: 2,
  },
  submitBtn: {
    backgroundColor: COLORS.primary,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 14,
    borderRadius: BORDER_RADIUS.xl,
    gap: 8,
    marginTop: SPACING.sm,
    ...SHADOWS.sm,
  },
  submitBtnText: {
    color: COLORS.white,
    fontSize: 15,
    fontWeight: '700',
  },
  cancelBtn: {
    alignItems: 'center',
    paddingVertical: 12,
  },
  cancelBtnText: {
    color: COLORS.slate400,
    fontSize: 13,
    fontWeight: '600',
  },
});

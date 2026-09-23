import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import * as DocumentPicker from 'expo-document-picker';
import { Ionicons } from '@expo/vector-icons';

import { documentService } from '../../src/services/documentService';
import { VaultDocument } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';

export default function DocumentVaultScreen() {
  const { user } = useAuth();
  const [documents, setDocuments] = useState<VaultDocument[]>([]);
  const [categories, setCategories] = useState<Record<string, VaultDocument | null>>({});
  const [loading, setLoading] = useState<boolean>(true);
  const [uploading, setUploading] = useState<boolean>(false);

  const fetchVault = useCallback(async () => {
    try {
      const res = await documentService.getAll();
      setDocuments(res.documents);
      setCategories(res.categories);
    } catch (err) {
      console.warn('Error fetching vault documents:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchVault();
  }, [fetchVault]);

  const handleUpload = async (category: 'resume' | 'valid_id' | 'certificate' | 'pwd_id') => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: ['application/pdf', 'image/*'],
        copyToCacheDirectory: true,
      });

      if (!result.canceled && result.assets && result.assets.length > 0) {
        const file = result.assets[0];
        setUploading(true);

        const res = await documentService.uploadDocument(
          file.uri,
          file.name,
          file.mimeType || 'application/pdf',
          category
        );

        if (res.success) {
          Alert.alert('Success', res.message || 'Document uploaded successfully to vault.');
          fetchVault();
        } else {
          Alert.alert('Upload Failed', res.message || 'Unable to upload document.');
        }
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Document selection error.');
    } finally {
      setUploading(false);
    }
  };

  const handleDelete = (docId: string, name: string) => {
    Alert.alert(
      'Remove Document',
      `Are you sure you want to remove "${name}" from your Document Hub?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Remove',
          style: 'destructive',
          onPress: async () => {
            const res = await documentService.deleteDocument(docId);
            if (res.success) {
              Alert.alert('Removed', 'Document deleted.');
              fetchVault();
            } else {
              Alert.alert('Error', res.message || 'Failed to remove document.');
            }
          },
        },
      ]
    );
  };

  const getCategoryConfig = (cat: string) => {
    switch (cat) {
      case 'resume':
        return { title: 'Curriculum Vitae / Resume', icon: 'document-text', color: COLORS.primary, bg: COLORS.primaryUltraLight };
      case 'valid_id':
        return { title: 'Valid Government ID', icon: 'card', color: COLORS.warning, bg: COLORS.warningBg };
      case 'certificate':
        return { title: 'DMDP & Professional Certifications', icon: 'ribbon', color: COLORS.purple, bg: COLORS.purpleBg };
      case 'pwd_id':
        return { title: 'PWD Identification Card', icon: 'accessibility', color: COLORS.info, bg: COLORS.infoBg };
      default:
        return { title: 'General Document', icon: 'folder', color: COLORS.textSecondary, bg: COLORS.surfaceSubtle };
    }
  };

  const storedCategories: ('resume' | 'valid_id' | 'certificate' | 'pwd_id')[] = ['resume', 'valid_id', 'certificate', 'pwd_id'];
  const storedCount = storedCategories.filter(
    (cat) => !!categories[cat] || documents.some((d) => d.category === cat)
  ).length;
  const vaultPercentage = Math.round((storedCount / 4) * 100);

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent}>
      {/* Information Header */}
      <View style={styles.headerCard}>
        <View style={styles.headerRow}>
          <View style={styles.headerLeft}>
            <View style={styles.badgeRow}>
              <View style={styles.pulseDot} />
              <Text style={styles.badgeText}>DMDP CREDENTIAL & VERIFICATION VAULT</Text>
            </View>
            <Text style={styles.headerTitle}>Document Hub</Text>
            <Text style={styles.headerSubtitle}>
              Upload your primary resume, government ID, and vocational certificates to earn official verified status.
            </Text>
          </View>
          <View style={styles.vaultMeterBox}>
            <Text style={styles.vaultMeterLabel}>VAULT STATUS</Text>
            <Text style={styles.vaultMeterCount}>{storedCount} of 4 Stored</Text>
            <View style={styles.vaultMeterBarBg}>
              <View style={[styles.vaultMeterBarFill, { width: `${vaultPercentage}%` }]} />
            </View>
          </View>
        </View>
      </View>

      {/* Explainer Info Card (Web Sync) */}
      <View style={styles.infoCard}>
        <View style={styles.infoIconBox}>
          <Text style={{ fontSize: 20 }}>💡</Text>
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.infoTitle}>How the Document Hub Works:</Text>
          <Text style={styles.infoText}>
            • <Text style={styles.infoBold}>Resume:</Text> Attached automatically to job applications so employers can evaluate qualifications.{'\n'}
            • <Text style={styles.infoBold}>Valid ID:</Text> Confirms identity with Public Employment Service Office (PESO/DMDP).{'\n'}
            • <Text style={styles.infoBold}>Certifications:</Text> Boosts your AI Cosine-Similarity Match Score for skilled vacancies.{'\n'}
            • <Text style={styles.infoBold}>PWD ID:</Text> Enables priority matching for disability-inclusive opportunities.
          </Text>
        </View>
      </View>

      {/* Upload Quick Actions Grid matching web categories */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Document Categories</Text>

        <View style={styles.uploadGrid}>
          <TouchableOpacity
            style={styles.actionCard}
            onPress={() => handleUpload('resume')}
            disabled={uploading}>
            <View style={[styles.actionIconBox, { backgroundColor: COLORS.primaryUltraLight }]}>
              <Ionicons name="document-attach-outline" size={24} color={COLORS.primary} />
            </View>
            <Text style={styles.actionLabel}>Upload Resume</Text>
            <Text style={styles.actionSub}>PDF / Word Doc</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.actionCard}
            onPress={() => handleUpload('valid_id')}
            disabled={uploading}>
            <View style={[styles.actionIconBox, { backgroundColor: COLORS.warningBg }]}>
              <Ionicons name="card-outline" size={24} color={COLORS.warning} />
            </View>
            <Text style={styles.actionLabel}>Valid Govt ID</Text>
            <Text style={styles.actionSub}>National ID, UMID, Driver</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.actionCard}
            onPress={() => handleUpload('certificate')}
            disabled={uploading}>
            <View style={[styles.actionIconBox, { backgroundColor: COLORS.purpleBg }]}>
              <Ionicons name="ribbon-outline" size={24} color={COLORS.purple} />
            </View>
            <Text style={styles.actionLabel}>Certificate</Text>
            <Text style={styles.actionSub}>DMDP or TESDA NC</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.actionCard}
            onPress={() => handleUpload('pwd_id')}
            disabled={uploading}>
            <View style={[styles.actionIconBox, { backgroundColor: COLORS.infoBg }]}>
              <Ionicons name="accessibility-outline" size={24} color={COLORS.info} />
            </View>
            <Text style={styles.actionLabel}>PWD ID</Text>
            <Text style={styles.actionSub}>If PWD registered</Text>
          </TouchableOpacity>
        </View>

        {uploading && (
          <View style={styles.uploadingBox}>
            <ActivityIndicator size="small" color={COLORS.primary} />
            <Text style={styles.uploadingText}>Uploading document to vault...</Text>
          </View>
        )}
      </View>

      {/* Vault Items List */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Stored Documents ({documents.length})</Text>

        {loading ? (
          <ActivityIndicator size="small" color={COLORS.primary} />
        ) : documents.length > 0 ? (
          documents.map((item, idx) => {
            const config = getCategoryConfig(item.category);
            const isVerified = item.status === 'verified';

            return (
              <View key={item.id || idx} style={styles.itemRow}>
                <View style={[styles.itemIcon, { backgroundColor: config.bg }]}>
                  <Ionicons name={config.icon as any} size={20} color={config.color} />
                </View>
                <View style={{ flex: 1, marginLeft: 12 }}>
                  <Text style={styles.itemName} numberOfLines={1}>{item.name}</Text>
                  <Text style={styles.itemDate}>
                    {config.title} • {item.uploaded_at ? item.uploaded_at.split('T')[0] : 'Saved'}
                  </Text>
                  <View style={styles.statusRow}>
                    <View style={[styles.statusTag, isVerified ? styles.verifiedTag : styles.reviewTag]}>
                      <Text style={[styles.statusTagText, isVerified ? styles.verifiedText : styles.reviewText]}>
                        {isVerified ? 'Verified' : 'Under Review'}
                      </Text>
                    </View>
                    {item.certificate_no && (
                      <Text style={styles.certNo}>#{item.certificate_no}</Text>
                    )}
                  </View>
                </View>

                <TouchableOpacity
                  style={styles.deleteBtn}
                  onPress={() => handleDelete(item.id, item.name)}>
                  <Ionicons name="trash-outline" size={18} color={COLORS.danger} />
                </TouchableOpacity>
              </View>
            );
          })
        ) : (
          <Text style={styles.emptyText}>No documents in your vault yet. Tap any category above to upload.</Text>
        )}
      </View>
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
  headerCard: {
    backgroundColor: COLORS.navy,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    ...SHADOWS.md,
  },
  headerRow: {
    flexDirection: 'column',
    gap: SPACING.md,
  },
  headerLeft: {
    flex: 1,
  },
  badgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: 'rgba(5, 150, 105, 0.25)',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.full,
    alignSelf: 'flex-start',
    marginBottom: 6,
  },
  pulseDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: COLORS.primaryLight,
  },
  badgeText: {
    fontSize: 9,
    fontWeight: 'bold',
    color: COLORS.primaryLight,
    letterSpacing: 0.5,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  headerSubtitle: {
    fontSize: 12,
    color: COLORS.primaryUltraLight,
    marginTop: 4,
    lineHeight: 17,
  },
  vaultMeterBox: {
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.15)',
  },
  vaultMeterLabel: {
    fontSize: 10,
    fontWeight: 'bold',
    color: COLORS.primaryLight,
    letterSpacing: 0.5,
  },
  vaultMeterCount: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 2,
    marginBottom: 6,
  },
  vaultMeterBarBg: {
    height: 6,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    borderRadius: 3,
    overflow: 'hidden',
  },
  vaultMeterBarFill: {
    height: '100%',
    backgroundColor: COLORS.primaryLight,
    borderRadius: 3,
  },
  infoCard: {
    flexDirection: 'row',
    backgroundColor: COLORS.primaryUltraLight,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: 'rgba(5, 150, 105, 0.2)',
    gap: SPACING.sm,
  },
  infoIconBox: {
    width: 36,
    height: 36,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
  },
  infoTitle: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
    marginBottom: 4,
  },
  infoText: {
    fontSize: 11,
    color: COLORS.textSecondary,
    lineHeight: 17,
  },
  infoBold: {
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  section: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: SPACING.md,
  },
  uploadGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.md,
  },
  actionCard: {
    width: '47%',
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  actionIconBox: {
    width: 44,
    height: 44,
    borderRadius: 22,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: SPACING.xs,
  },
  actionLabel: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.navy,
    textAlign: 'center',
  },
  actionSub: {
    fontSize: 10,
    color: COLORS.textMuted,
    textAlign: 'center',
    marginTop: 2,
  },
  uploadingBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    marginTop: SPACING.md,
    padding: SPACING.sm,
  },
  uploadingText: {
    fontSize: 12,
    color: COLORS.primaryDark,
    fontWeight: '600',
  },
  itemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: SPACING.md,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.borderLight,
  },
  itemIcon: {
    width: 40,
    height: 40,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
    alignItems: 'center',
  },
  itemName: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  itemDate: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  statusRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginTop: 4,
  },
  statusTag: {
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.sm,
  },
  verifiedTag: {
    backgroundColor: COLORS.successBg,
  },
  reviewTag: {
    backgroundColor: COLORS.warningBg,
  },
  statusTagText: {
    fontSize: 10,
    fontWeight: 'bold',
  },
  verifiedText: {
    color: COLORS.success,
  },
  reviewText: {
    color: COLORS.warning,
  },
  certNo: {
    fontSize: 10,
    fontFamily: 'monospace',
    color: COLORS.primary,
    fontWeight: 'bold',
  },
  deleteBtn: {
    padding: 8,
  },
  emptyText: {
    fontSize: 12,
    color: COLORS.textMuted,
    fontStyle: 'italic',
    textAlign: 'center',
    paddingVertical: SPACING.lg,
  },
});

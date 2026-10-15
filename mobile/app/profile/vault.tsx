import React, { useState, useEffect, useCallback, useMemo } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  Modal,
  Image,
} from 'react-native';
import * as DocumentPicker from 'expo-document-picker';
import * as WebBrowser from 'expo-web-browser';
import { Ionicons } from '@expo/vector-icons';

import { documentService } from '../../src/services/documentService';
import { VaultDocument } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';
import { useAuth } from '../../src/context/AuthContext';
import { getDefaultApiUrl } from '../../src/config/api';

type DocCategory = 'resume' | 'valid_id' | 'certificate' | 'pwd_id';

export default function DocumentVaultScreen() {
  const { user, isAuthenticated } = useAuth();
  const userId = user?.id || user?.user_id;

  const [documents, setDocuments] = useState<VaultDocument[]>([]);
  const [categories, setCategories] = useState<Record<string, VaultDocument | null>>({});
  const [loading, setLoading] = useState<boolean>(true);
  const [uploadingCategory, setUploadingCategory] = useState<DocCategory | null>(null);
  const [selectedPreviewDoc, setSelectedPreviewDoc] = useState<VaultDocument | null>(null);
  const [activeFilter, setActiveFilter] = useState<'all' | 'verified' | 'review' | 'rejected'>('all');

  const resolveDocUrl = (rawUrl?: string): string => {
    if (!rawUrl) return '';
    if (rawUrl.startsWith('http://') || rawUrl.startsWith('https://')) return rawUrl;
    const base = getDefaultApiUrl().replace(/\/api\/?$/, '');
    return `${base}${rawUrl.startsWith('/') ? '' : '/'}${rawUrl}`;
  };

  const fetchVault = useCallback(async () => {
    try {
      const res = await documentService.getAll();
      setDocuments(res.documents || []);
      setCategories(res.categories || {});
    } catch (err) {
      console.warn('Error fetching vault documents:', err);
      setDocuments([]);
      setCategories({});
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated && userId) {
      setLoading(true);
      fetchVault();
    } else {
      setDocuments([]);
      setCategories({});
      setLoading(false);
    }
  }, [isAuthenticated, userId, fetchVault]);

  const handleUpload = async (category: DocCategory) => {
    try {
      const result = await DocumentPicker.getDocumentAsync({
        type: ['application/pdf', 'image/*'],
        copyToCacheDirectory: true,
      });

      if (!result.canceled && result.assets && result.assets.length > 0) {
        const file = result.assets[0];
        if (file.size && file.size > 5 * 1024 * 1024) {
          Alert.alert('File Too Large', 'The selected file exceeds the 5MB maximum limit. Please choose a smaller file.');
          return;
        }

        setUploadingCategory(category);

        const res = await documentService.uploadDocument(
          file.uri,
          file.name,
          file.mimeType || 'application/pdf',
          category
        );

        if (res.success) {
          Alert.alert('Upload Successful', res.message || 'Your credential has been uploaded to the DMDP Vault.');
          fetchVault();
        } else {
          Alert.alert('Upload Failed', res.message || 'Unable to upload document. Please check the file format.');
        }
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Error occurred while picking document.');
    } finally {
      setUploadingCategory(null);
    }
  };

  const handleDelete = (docId: string, name: string) => {
    Alert.alert(
      'Remove Credential',
      `Are you sure you want to remove "${name}" from your Document Hub?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Remove',
          style: 'destructive',
          onPress: async () => {
            const res = await documentService.deleteDocument(docId);
            if (res.success) {
              if (selectedPreviewDoc?.id === docId) {
                setSelectedPreviewDoc(null);
              }
              Alert.alert('Removed', 'Document has been removed.');
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
        return {
          title: 'Curriculum Vitae / Resume',
          shortTitle: 'Resume / CV',
          icon: 'document-text',
          color: COLORS.primary,
          bg: COLORS.primaryUltraLight,
          badge: 'Auto-attached to job applications',
        };
      case 'valid_id':
        return {
          title: 'Government Issued ID',
          shortTitle: 'Valid Govt ID',
          icon: 'card',
          color: COLORS.warning,
          bg: COLORS.warningBg,
          badge: 'PESO / DMDP Identity Verification',
        };
      case 'certificate':
        return {
          title: 'Certificates & Licensures',
          shortTitle: 'Certificate',
          icon: 'ribbon',
          color: COLORS.purple,
          bg: COLORS.purpleBg,
          badge: 'Boosts AI Matching Score',
        };
      case 'pwd_id':
        return {
          title: 'PWD Identification Card',
          shortTitle: 'PWD ID',
          icon: 'accessibility',
          color: COLORS.info,
          bg: COLORS.infoBg,
          badge: 'Priority PWD Placement',
        };
      default:
        return {
          title: 'General Credential',
          shortTitle: 'Document',
          icon: 'folder',
          color: COLORS.textSecondary,
          bg: COLORS.surfaceSubtle,
          badge: 'Stored Document',
        };
    }
  };

  const storedCategories: DocCategory[] = ['resume', 'valid_id', 'certificate', 'pwd_id'];
  const storedCount = storedCategories.filter(
    (cat) => !!categories[cat] || documents.some((d) => d.category === cat)
  ).length;
  const vaultPercentage = Math.round((storedCount / 4) * 100);

  const filteredDocs = useMemo(() => {
    if (activeFilter === 'verified') {
      return documents.filter((d) => d.status === 'verified' || d.status === 'approved');
    }
    if (activeFilter === 'review') {
      return documents.filter((d) => d.status !== 'verified' && d.status !== 'approved' && d.status !== 'rejected');
    }
    if (activeFilter === 'rejected') {
      return documents.filter((d) => d.status === 'rejected');
    }
    return documents;
  }, [documents, activeFilter]);

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
      {/* Information Header */}
      <View style={styles.headerCard}>
        <View style={styles.headerRow}>
          <View style={styles.headerLeft}>
            <View style={styles.badgeRow}>
              <View style={styles.pulseDot} />
              <Text style={styles.badgeText}>DOLE-DMDP OFFICIAL CREDENTIAL VAULT</Text>
            </View>
            <Text style={styles.headerTitle}>Document Hub</Text>
            <Text style={styles.headerSubtitle}>
              Store your official resume, government ID, and vocational certificates to earn PESO verified status.
            </Text>
          </View>
          <View style={styles.vaultMeterBox}>
            <Text style={styles.vaultMeterLabel}>COMPLETION</Text>
            <Text style={styles.vaultMeterCount}>{vaultPercentage}%</Text>
            <Text style={styles.vaultMeterSub}>{storedCount} of 4 Stored</Text>
            <View style={styles.vaultMeterBarBg}>
              <View style={[styles.vaultMeterBarFill, { width: `${vaultPercentage}%` }]} />
            </View>
          </View>
        </View>
      </View>

      {/* Explainer Card */}
      <View style={styles.infoCard}>
        <View style={styles.infoIconBox}>
          <Ionicons name="shield-checkmark" size={22} color={COLORS.primary} />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.infoTitle}>Verified Document Standards:</Text>
          <Text style={styles.infoText}>
            • <Text style={styles.infoBold}>Resume:</Text> Evaluated by Cebu City employers.{'\n'}
            • <Text style={styles.infoBold}>Valid ID:</Text> Unlocks your DOLE-certified badge.{'\n'}
            • <Text style={styles.infoBold}>Certificates:</Text> Automatically awarded upon completing DMDP online course quizzes.{'\n'}
            • <Text style={styles.infoBold}>PWD ID:</Text> Priority matching for inclusive roles.
          </Text>
        </View>
      </View>

      {/* Category Upload Actions */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Document Categories</Text>
        <Text style={styles.sectionSubtitle}>Tap any category to upload a PDF or image document (Max 5MB)</Text>

        <View style={styles.uploadGrid}>
          {storedCategories.map((cat) => {
            const config = getCategoryConfig(cat);
            const isStored = !!categories[cat] || documents.some((d) => d.category === cat);
            const isUploading = uploadingCategory === cat;

            return (
              <TouchableOpacity
                key={cat}
                style={[styles.actionCard, isStored && styles.actionCardStored]}
                onPress={() => handleUpload(cat)}
                disabled={!!uploadingCategory}
                activeOpacity={0.85}>
                <View style={[styles.actionIconBox, { backgroundColor: config.bg }]}>
                  {isUploading ? (
                    <ActivityIndicator size="small" color={config.color} />
                  ) : (
                    <Ionicons name={config.icon as any} size={24} color={config.color} />
                  )}
                </View>

                <Text style={styles.actionLabel} numberOfLines={1}>
                  {config.shortTitle}
                </Text>

                <View style={styles.actionStatusRow}>
                  {isStored ? (
                    <View style={styles.storedIndicator}>
                      <Ionicons name="checkmark-circle" size={12} color={COLORS.emerald700} />
                      <Text style={styles.storedIndicatorText}>Stored</Text>
                    </View>
                  ) : (
                    <Text style={styles.actionSub}>Tap to upload</Text>
                  )}
                </View>
              </TouchableOpacity>
            );
          })}
        </View>

        {uploadingCategory && (
          <View style={styles.uploadingBox}>
            <ActivityIndicator size="small" color={COLORS.primary} />
            <Text style={styles.uploadingText}>
              Uploading {getCategoryConfig(uploadingCategory).shortTitle} to secure vault...
            </Text>
          </View>
        )}
      </View>

      {/* Vault Items List */}
      <View style={styles.section}>
        <View style={styles.sectionHeaderRow}>
          <Text style={styles.sectionTitle}>Stored Documents ({documents.length})</Text>
        </View>

        {/* Filter Pills */}
        <View style={styles.filterRow}>
          {(['all', 'verified', 'review', 'rejected'] as const).map((f) => {
            const count =
              f === 'all'
                ? documents.length
                : f === 'verified'
                ? documents.filter((d) => d.status === 'verified' || d.status === 'approved').length
                : f === 'review'
                ? documents.filter((d) => d.status !== 'verified' && d.status !== 'approved' && d.status !== 'rejected').length
                : documents.filter((d) => d.status === 'rejected').length;

            return (
              <TouchableOpacity
                key={f}
                style={[styles.filterPill, activeFilter === f && styles.filterPillActive]}
                onPress={() => setActiveFilter(f)}>
                <Text style={[styles.filterPillText, activeFilter === f && styles.filterPillTextActive]}>
                  {f.charAt(0).toUpperCase() + f.slice(1)} ({count})
                </Text>
              </TouchableOpacity>
            );
          })}
        </View>

        {loading ? (
          <View style={styles.loaderBox}>
            <ActivityIndicator size="small" color={COLORS.primary} />
            <Text style={styles.loaderText}>Loading vault documents...</Text>
          </View>
        ) : filteredDocs.length > 0 ? (
          filteredDocs.map((item, idx) => {
            const config = getCategoryConfig(item.category);
            const isVerified = item.status === 'verified' || item.status === 'approved';
            const isRejected = item.status === 'rejected';

            return (
              <TouchableOpacity
                key={item.id || idx}
                style={styles.itemRow}
                activeOpacity={0.88}
                onPress={() => setSelectedPreviewDoc(item)}>
                <View style={[styles.itemIcon, { backgroundColor: isRejected ? COLORS.dangerBg : config.bg }]}>
                  <Ionicons
                    name={isRejected ? 'alert-circle' : (config.icon as any)}
                    size={22}
                    color={isRejected ? COLORS.danger : config.color}
                  />
                </View>

                <View style={{ flex: 1, marginLeft: 12 }}>
                  <Text style={styles.itemName} numberOfLines={1}>
                    {item.name}
                  </Text>
                  <Text style={styles.itemDate}>
                    {config.title} • {item.uploaded_at ? item.uploaded_at.split('T')[0] : 'Saved'}
                  </Text>

                  <View style={styles.statusRow}>
                    <View
                      style={[
                        styles.statusTag,
                        isVerified ? styles.verifiedTag : isRejected ? styles.rejectedTag : styles.reviewTag,
                      ]}>
                      <Ionicons
                        name={isVerified ? 'shield-checkmark' : isRejected ? 'alert-circle' : 'time-outline'}
                        size={11}
                        color={isVerified ? COLORS.emerald800 : isRejected ? COLORS.danger : COLORS.amberDark}
                      />
                      <Text
                        style={[
                          styles.statusTagText,
                          isVerified ? styles.verifiedText : isRejected ? styles.rejectedText : styles.reviewText,
                        ]}>
                        {isVerified ? 'Verified' : isRejected ? 'Rejected' : 'Under Review'}
                      </Text>
                    </View>

                    {item.certificate_no && (
                      <View style={styles.certBadge}>
                        <Ionicons name="ribbon-outline" size={11} color={COLORS.purple} />
                        <Text style={styles.certNo}>#{item.certificate_no}</Text>
                      </View>
                    )}

                    <View style={styles.previewPill}>
                      <Ionicons name="eye-outline" size={12} color={COLORS.primary} />
                      <Text style={styles.previewPillText}>Preview</Text>
                    </View>
                  </View>

                  {isRejected && (
                    <View style={styles.rejectionNotice}>
                      <Text style={styles.rejectionNoticeText}>
                        Reason: {item.rejection_reason || 'Document does not meet requirements. Please re-upload.'}
                      </Text>
                    </View>
                  )}
                </View>

                <TouchableOpacity
                  style={styles.deleteBtn}
                  onPress={(e) => {
                    e.stopPropagation?.();
                    handleDelete(item.id, item.name);
                  }}>
                  <Ionicons name="trash-outline" size={18} color={COLORS.danger} />
                </TouchableOpacity>
              </TouchableOpacity>
            );
          })
        ) : (
          <View style={styles.emptyState}>
            <Ionicons name="folder-open-outline" size={40} color={COLORS.slate300} />
            <Text style={styles.emptyTitle}>No Documents in this View</Text>
            <Text style={styles.emptyText}>Tap any category tile above to upload official records to your vault.</Text>
          </View>
        )}
      </View>

      {/* Document In-App Preview Modal */}
      <Modal
        visible={!!selectedPreviewDoc}
        transparent
        animationType="fade"
        onRequestClose={() => setSelectedPreviewDoc(null)}>
        <View style={styles.modalOverlay}>
          <View style={styles.previewModalCard}>
            <View style={styles.previewModalHeader}>
              <View style={{ flex: 1, paddingRight: 8 }}>
                <Text style={styles.previewModalTitle} numberOfLines={1}>
                  {selectedPreviewDoc?.name}
                </Text>
                <Text style={styles.previewModalSub}>
                  {selectedPreviewDoc ? getCategoryConfig(selectedPreviewDoc.category).title : 'Official Document'}
                </Text>
              </View>
              <TouchableOpacity onPress={() => setSelectedPreviewDoc(null)} style={styles.modalCloseBtn}>
                <Ionicons name="close" size={22} color={COLORS.slate600} />
              </TouchableOpacity>
            </View>

            {/* Document Body / Image Preview */}
            <View style={styles.previewBody}>
              {selectedPreviewDoc &&
              (selectedPreviewDoc.file_url || selectedPreviewDoc.url) &&
              (selectedPreviewDoc.name.match(/\.(png|jpe?g|webp)$/i) || selectedPreviewDoc.mime_type?.includes('image')) ? (
                <Image
                  source={{ uri: resolveDocUrl(selectedPreviewDoc.file_url || selectedPreviewDoc.url) }}
                  style={styles.previewImage}
                  resizeMode="contain"
                />
              ) : (
                <View style={styles.pdfPlaceholder}>
                  <View style={styles.pdfIconCircle}>
                    <Ionicons name="document-text" size={48} color={COLORS.primary} />
                  </View>
                  <Text style={styles.pdfPlaceholderTitle}>
                    {selectedPreviewDoc?.certificate_no ? 'Verified DMDP Certificate' : 'Official Document File'}
                  </Text>
                  <Text style={styles.pdfPlaceholderSub}>
                    {selectedPreviewDoc?.file_size
                      ? `${(selectedPreviewDoc.file_size / (1024 * 1024)).toFixed(2)} MB • `
                      : ''}
                    Stored securely in Cebu City DMDP Vault
                  </Text>
                  {selectedPreviewDoc?.certificate_no && (
                    <View style={styles.modalCertBox}>
                      <Ionicons name="ribbon" size={16} color={COLORS.purple} />
                      <Text style={styles.modalCertText}>#{selectedPreviewDoc.certificate_no}</Text>
                    </View>
                  )}
                </View>
              )}
            </View>

            {/* Rejection Notice in Modal */}
            {selectedPreviewDoc?.status === 'rejected' && (
              <View style={styles.modalRejectionNotice}>
                <Ionicons name="alert-circle" size={16} color={COLORS.danger} />
                <Text style={styles.modalRejectionText}>
                  Rejection Reason: {selectedPreviewDoc.rejection_reason || 'Does not meet requirements.'}
                </Text>
              </View>
            )}

            {/* Actions */}
            <View style={styles.previewActionRow}>
              {(selectedPreviewDoc?.file_url || selectedPreviewDoc?.url) && (
                <TouchableOpacity
                  style={styles.openExternalBtn}
                  onPress={async () => {
                    const url = resolveDocUrl(selectedPreviewDoc?.file_url || selectedPreviewDoc?.url);
                    if (url) {
                      try {
                        await WebBrowser.openBrowserAsync(url, {
                          toolbarColor: COLORS.navy,
                          controlsColor: COLORS.white,
                        });
                      } catch {
                        Alert.alert('File Link', url);
                      }
                    }
                  }}>
                  <Ionicons name="open-outline" size={16} color={COLORS.white} />
                  <Text style={styles.openExternalBtnText}>Open in Viewer</Text>
                </TouchableOpacity>
              )}

              <TouchableOpacity
                style={styles.deleteModalBtn}
                onPress={() => {
                  if (selectedPreviewDoc) {
                    handleDelete(selectedPreviewDoc.id, selectedPreviewDoc.name);
                  }
                }}>
                <Ionicons name="trash-outline" size={16} color={COLORS.danger} />
                <Text style={styles.deleteModalBtnText}>Delete</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.closeModalBtn} onPress={() => setSelectedPreviewDoc(null)}>
                <Text style={styles.closeModalBtnText}>Close</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

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
    padding: SPACING.md,
  },
  headerCard: {
    backgroundColor: COLORS.navy,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    marginBottom: SPACING.md,
    ...SHADOWS.md,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    gap: 12,
  },
  headerLeft: {
    flex: 1,
  },
  badgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 4,
  },
  pulseDot: {
    width: 7,
    height: 7,
    borderRadius: 4,
    backgroundColor: COLORS.emerald400,
  },
  badgeText: {
    fontSize: 9,
    fontWeight: '800',
    color: COLORS.emerald400,
    letterSpacing: 0.5,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '800',
    color: COLORS.white,
    letterSpacing: -0.3,
  },
  headerSubtitle: {
    fontSize: 12,
    color: COLORS.slate300,
    marginTop: 4,
    lineHeight: 16,
  },
  vaultMeterBox: {
    backgroundColor: 'rgba(255,255,255,0.1)',
    borderRadius: BORDER_RADIUS.lg,
    padding: 10,
    alignItems: 'center',
    minWidth: 90,
  },
  vaultMeterLabel: {
    fontSize: 9,
    fontWeight: '800',
    color: COLORS.slate300,
  },
  vaultMeterCount: {
    fontSize: 18,
    fontWeight: '800',
    color: COLORS.white,
    marginTop: 2,
  },
  vaultMeterSub: {
    fontSize: 9,
    color: COLORS.slate400,
    marginTop: 1,
  },
  vaultMeterBarBg: {
    width: '100%',
    height: 4,
    backgroundColor: 'rgba(255,255,255,0.2)',
    borderRadius: 2,
    marginTop: 6,
    overflow: 'hidden',
  },
  vaultMeterBarFill: {
    height: '100%',
    backgroundColor: COLORS.emerald400,
    borderRadius: 2,
  },
  infoCard: {
    flexDirection: 'row',
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    marginBottom: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    gap: 12,
    ...SHADOWS.sm,
  },
  infoIconBox: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: COLORS.primaryUltraLight,
    alignItems: 'center',
    justifyContent: 'center',
  },
  infoTitle: {
    fontSize: 13,
    fontWeight: '800',
    color: COLORS.navy,
    marginBottom: 4,
  },
  infoText: {
    fontSize: 11,
    color: COLORS.slate600,
    lineHeight: 16,
  },
  infoBold: {
    fontWeight: '700',
    color: COLORS.slate900,
  },
  section: {
    marginBottom: SPACING.lg,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.navy,
  },
  sectionSubtitle: {
    fontSize: 12,
    color: COLORS.slate500,
    marginTop: 2,
    marginBottom: SPACING.sm,
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.xs,
  },
  uploadGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
  },
  actionCard: {
    width: '48.5%',
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    ...SHADOWS.sm,
  },
  actionCardStored: {
    borderColor: COLORS.emerald200,
    backgroundColor: '#F8FAF9',
  },
  actionIconBox: {
    width: 44,
    height: 44,
    borderRadius: BORDER_RADIUS.md,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 8,
  },
  actionLabel: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.slate900,
  },
  actionStatusRow: {
    marginTop: 4,
  },
  actionSub: {
    fontSize: 11,
    color: COLORS.slate400,
  },
  storedIndicator: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  storedIndicatorText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.emerald800,
  },
  uploadingBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: COLORS.primaryUltraLight,
    padding: 10,
    borderRadius: BORDER_RADIUS.md,
    marginTop: 10,
  },
  uploadingText: {
    fontSize: 12,
    color: COLORS.primaryDark,
    fontWeight: '600',
  },
  filterRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
    marginVertical: SPACING.sm,
  },
  filterPill: {
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: BORDER_RADIUS.full,
    backgroundColor: COLORS.white,
    borderWidth: 1,
    borderColor: COLORS.slate200,
  },
  filterPillActive: {
    backgroundColor: COLORS.navy,
    borderColor: COLORS.navy,
  },
  filterPillText: {
    fontSize: 11,
    fontWeight: '600',
    color: COLORS.slate600,
  },
  filterPillTextActive: {
    color: COLORS.white,
    fontWeight: '700',
  },
  loaderBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    paddingVertical: 24,
  },
  loaderText: {
    fontSize: 13,
    color: COLORS.slate500,
  },
  itemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    ...SHADOWS.sm,
  },
  itemIcon: {
    width: 44,
    height: 44,
    borderRadius: BORDER_RADIUS.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  itemName: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.slate900,
  },
  itemDate: {
    fontSize: 11,
    color: COLORS.slate500,
    marginTop: 2,
  },
  statusRow: {
    flexDirection: 'row',
    alignItems: 'center',
    flexWrap: 'wrap',
    gap: 6,
    marginTop: 6,
  },
  statusTag: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 3,
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.full,
  },
  verifiedTag: {
    backgroundColor: COLORS.emerald50,
  },
  reviewTag: {
    backgroundColor: COLORS.warningBg,
  },
  rejectedTag: {
    backgroundColor: COLORS.dangerBg,
  },
  statusTagText: {
    fontSize: 10,
    fontWeight: '700',
  },
  verifiedText: {
    color: COLORS.emerald800,
  },
  reviewText: {
    color: COLORS.amberDark,
  },
  rejectedText: {
    color: COLORS.danger,
  },
  certBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 3,
    backgroundColor: COLORS.purpleBg,
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.full,
  },
  certNo: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.purple,
  },
  previewPill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 3,
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.full,
  },
  previewPillText: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.primary,
  },
  rejectionNotice: {
    backgroundColor: COLORS.dangerBg,
    borderRadius: BORDER_RADIUS.sm,
    padding: 6,
    marginTop: 6,
  },
  rejectionNoticeText: {
    fontSize: 11,
    color: COLORS.danger,
    lineHeight: 14,
  },
  deleteBtn: {
    padding: 8,
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.slate200,
  },
  emptyTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.slate700,
    marginTop: 8,
  },
  emptyText: {
    fontSize: 12,
    color: COLORS.slate400,
    textAlign: 'center',
    marginTop: 4,
    lineHeight: 16,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.65)',
    justifyContent: 'center',
    padding: SPACING.lg,
  },
  previewModalCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    maxHeight: '85%',
    ...SHADOWS.lg,
  },
  previewModalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    borderBottomWidth: 1,
    borderBottomColor: COLORS.slate100,
    paddingBottom: SPACING.sm,
  },
  previewModalTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.navy,
  },
  previewModalSub: {
    fontSize: 12,
    color: COLORS.slate500,
    marginTop: 2,
  },
  modalCloseBtn: {
    padding: 4,
  },
  previewBody: {
    alignItems: 'center',
    justifyContent: 'center',
    minHeight: 220,
    marginVertical: SPACING.md,
  },
  previewImage: {
    width: '100%',
    height: 260,
    borderRadius: BORDER_RADIUS.md,
  },
  pdfPlaceholder: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: SPACING.lg,
  },
  pdfIconCircle: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: COLORS.primaryUltraLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  pdfPlaceholderTitle: {
    fontSize: 15,
    fontWeight: '800',
    color: COLORS.navy,
    textAlign: 'center',
  },
  pdfPlaceholderSub: {
    fontSize: 12,
    color: COLORS.slate500,
    textAlign: 'center',
    marginTop: 4,
  },
  modalCertBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: COLORS.purpleBg,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.full,
    marginTop: 12,
  },
  modalCertText: {
    fontSize: 12,
    fontWeight: '800',
    color: COLORS.purple,
  },
  modalRejectionNotice: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: COLORS.dangerBg,
    borderRadius: BORDER_RADIUS.md,
    padding: 10,
    marginBottom: SPACING.md,
  },
  modalRejectionText: {
    flex: 1,
    fontSize: 12,
    color: COLORS.danger,
  },
  previewActionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginTop: SPACING.sm,
  },
  openExternalBtn: {
    flex: 2,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: COLORS.primary,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
    ...SHADOWS.sm,
  },
  openExternalBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.white,
  },
  deleteModalBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    paddingVertical: 12,
    paddingHorizontal: 14,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.dangerBg,
  },
  deleteModalBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.danger,
  },
  closeModalBtn: {
    paddingVertical: 12,
    paddingHorizontal: 14,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.slate100,
  },
  closeModalBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.slate700,
  },
});

import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ScrollView,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { useAuth } from '../../src/context/AuthContext';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';

export default function EditProfileScreen() {
  const router = useRouter();
  const { user, updateProfile } = useAuth();

  const [phone, setPhone] = useState(user?.phone || user?.mobile_number || '');
  const [education, setEducation] = useState(user?.education || '');
  const [newSkill, setNewSkill] = useState('');
  const [skills, setSkills] = useState<string[]>(Array.isArray(user?.skills) ? user!.skills : []);
  const [saving, setSaving] = useState(false);

  const handleAddSkill = () => {
    if (newSkill.trim() && !skills.includes(newSkill.trim())) {
      setSkills([...skills, newSkill.trim()]);
      setNewSkill('');
    }
  };

  const handleRemoveSkill = (indexToRemove: number) => {
    setSkills(skills.filter((_, idx) => idx !== indexToRemove));
  };

  const handleSave = async () => {
    setSaving(true);
    try {
      const res = await updateProfile({
        phone: phone.trim(),
        mobile_number: phone.trim(),
        education: education.trim(),
        skills,
      });

      if (res.success) {
        Alert.alert('Success', 'Profile details updated.', [
          { text: 'OK', onPress: () => router.back() },
        ]);
      } else {
        Alert.alert('Error', res.message || 'Failed to update profile.');
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Unable to save profile changes.');
    } finally {
      setSaving(false);
    }
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent}>
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Edit Profile Information</Text>

        {/* Mobile Phone */}
        <View style={styles.fieldGroup}>
          <Text style={styles.label}>Mobile Phone</Text>
          <TextInput
            style={styles.input}
            value={phone}
            onChangeText={setPhone}
            placeholder="09123456789"
            placeholderTextColor={COLORS.textMuted}
            keyboardType="phone-pad"
          />
        </View>

        {/* Education */}
        <View style={styles.fieldGroup}>
          <Text style={styles.label}>Educational Background</Text>
          <TextInput
            style={styles.input}
            value={education}
            onChangeText={setEducation}
            placeholder="e.g. BS Computer Science, Cebu Institute of Technology"
            placeholderTextColor={COLORS.textMuted}
          />
        </View>

        {/* Skills Management */}
        <View style={styles.fieldGroup}>
          <Text style={styles.label}>Registered Skills & Competencies</Text>
          <View style={styles.skillInputRow}>
            <TextInput
              style={[styles.input, { flex: 1 }]}
              value={newSkill}
              onChangeText={setNewSkill}
              placeholder="Add skill (e.g. Forklift, React)"
              placeholderTextColor={COLORS.textMuted}
              onSubmitEditing={handleAddSkill}
            />
            <TouchableOpacity style={styles.addSkillBtn} onPress={handleAddSkill}>
              <Ionicons name="add" size={20} color={COLORS.white} />
            </TouchableOpacity>
          </View>

          <View style={styles.chipsContainer}>
            {skills.map((skill, index) => (
              <View key={index} style={styles.chip}>
                <Text style={styles.chipText}>{skill}</Text>
                <TouchableOpacity onPress={() => handleRemoveSkill(index)}>
                  <Ionicons name="close-circle" size={16} color={COLORS.primaryDark} style={{ marginLeft: 4 }} />
                </TouchableOpacity>
              </View>
            ))}
          </View>
        </View>

        {/* Save Button */}
        <TouchableOpacity style={styles.saveBtn} onPress={handleSave} disabled={saving}>
          {saving ? (
            <ActivityIndicator color={COLORS.white} />
          ) : (
            <Text style={styles.saveBtnText}>Save Profile Changes</Text>
          )}
        </TouchableOpacity>
      </View>
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
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: SPACING.lg,
  },
  fieldGroup: {
    marginBottom: SPACING.lg,
  },
  label: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: 6,
  },
  input: {
    backgroundColor: COLORS.surfaceSubtle,
    borderRadius: BORDER_RADIUS.md,
    paddingHorizontal: SPACING.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    height: 46,
    fontSize: 14,
    color: COLORS.text,
  },
  skillInputRow: {
    flexDirection: 'row',
    gap: SPACING.sm,
  },
  addSkillBtn: {
    width: 46,
    height: 46,
    backgroundColor: COLORS.primary,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
    alignItems: 'center',
  },
  chipsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: SPACING.sm,
    marginTop: SPACING.md,
  },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: SPACING.md,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.full,
  },
  chipText: {
    fontSize: 12,
    color: COLORS.primaryDark,
    fontWeight: '600',
  },
  saveBtn: {
    backgroundColor: COLORS.primary,
    height: 48,
    borderRadius: BORDER_RADIUS.md,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: SPACING.md,
  },
  saveBtnText: {
    color: COLORS.white,
    fontSize: 15,
    fontWeight: 'bold',
  },
});

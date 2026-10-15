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
  Switch,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { useAuth } from '../../src/context/AuthContext';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';

type EditTab = 'personal' | 'address' | 'education' | 'preferences' | 'eligibility' | 'skills';

const QUICK_SKILL_SUGGESTIONS = [
  'Customer Service',
  'Food & Beverage',
  'Welding NC II',
  'Electrical Installation',
  'Bookkeeping',
  'Administrative Support',
  'Software Development',
  'Driving (Professional)',
];

export default function EditProfileScreen() {
  const router = useRouter();
  const { user, updateProfile } = useAuth();

  // Active sub-section tab
  const [activeTab, setActiveTab] = useState<EditTab>('personal');

  // 1. Personal & Contact
  const [firstName, setFirstName] = useState(user?.firstName || user?.first_name || '');
  const [lastName, setLastName] = useState(user?.lastName || user?.last_name || '');
  const [middleName, setMiddleName] = useState(user?.middleName || user?.middle_name || '');
  const [phone, setPhone] = useState(user?.phone || user?.mobile_number || '');
  const [birthDate, setBirthDate] = useState(user?.birthDate || (user as any)?.birth_date || '');
  const [sex, setSex] = useState(user?.sex || 'Male');
  const [civilStatus, setCivilStatus] = useState(user?.civilStatus || user?.civil_status || 'Single');
  const [citizenship, setCitizenship] = useState(user?.citizenship || 'Filipino');

  // 2. Address & Barangay
  const addr = user?.address || {};
  const [barangay, setBarangay] = useState(addr.barangay || '');
  const [street, setStreet] = useState(addr.street || '');
  const [city, setCity] = useState(addr.city || 'Cebu City');
  const [province, setProvince] = useState(addr.province || 'Cebu');
  const [zip, setZip] = useState(addr.zip || '6000');

  // 3. Education & Work Experience
  const eduObj = user?.education_details || {};
  const [eduLevel, setEduLevel] = useState(eduObj.level || 'College Graduate');
  const [eduSchool, setEduSchool] = useState(eduObj.school || '');
  const [eduCourse, setEduCourse] = useState(eduObj.course || user?.education || '');
  const [eduYear, setEduYear] = useState(eduObj.year_graduated || '');

  const workObj = user?.work_experience || {};
  const [workCompany, setWorkCompany] = useState(workObj.company || '');
  const [workPosition, setWorkPosition] = useState(workObj.position || '');
  const [workDuration, setWorkDuration] = useState(workObj.duration || '');
  const [bio, setBio] = useState(workObj.summary || user?.bio || '');

  // 4. Career Preferences
  const prefObj = user?.preferences || {};
  const [occupation1, setOccupation1] = useState(prefObj.occupation1 || '');
  const [occupation2, setOccupation2] = useState(prefObj.occupation2 || '');
  const [industry1, setIndustry1] = useState(prefObj.industry1 || '');
  const [salaryExpectation, setSalaryExpectation] = useState(prefObj.salary_expectation || '');
  const [preferredLocation, setPreferredLocation] = useState(prefObj.preferred_location || 'Cebu City');

  // 5. Eligibilities & Social Status
  const eligObj = user?.eligibility || {};
  const [civilService, setCivilService] = useState(eligObj.civil_service || '');
  const [prcLicense, setPrcLicense] = useState(eligObj.prc_license || '');
  const [tesdaNc, setTesdaNc] = useState(eligObj.tesda_nc || '');
  const [driverLicense, setDriverLicense] = useState(eligObj.driver_license || '');

  const socObj = user?.social_status || {};
  const [is4ps, setIs4ps] = useState(!!socObj.is_4ps);
  const [isPwd, setIsPwd] = useState(!!socObj.is_pwd);
  const [pwdType, setPwdType] = useState(socObj.pwd_type || '');

  // 6. Skills
  const [newSkill, setNewSkill] = useState('');
  const initialSkills = Array.isArray(user?.skills)
    ? user!.skills.map((s: any) => (typeof s === 'string' ? s : s?.skill_name || '')).filter(Boolean)
    : [];
  const [skills, setSkills] = useState<string[]>(initialSkills);

  const [saving, setSaving] = useState(false);

  const handleAddSkill = (skillToAdd?: string) => {
    const target = (skillToAdd || newSkill).trim();
    if (target && !skills.some((s) => s.toLowerCase() === target.toLowerCase())) {
      setSkills([...skills, target]);
      if (!skillToAdd) {
        setNewSkill('');
      }
    }
  };

  const handleRemoveSkill = (indexToRemove: number) => {
    setSkills(skills.filter((_, idx) => idx !== indexToRemove));
  };

  const handleSave = async () => {
    if (!firstName.trim() || !lastName.trim()) {
      Alert.alert('Required Information', 'First Name and Last Name are required.');
      setActiveTab('personal');
      return;
    }

    if (!phone.trim()) {
      Alert.alert('Required Information', 'Please provide a valid contact mobile number.');
      setActiveTab('personal');
      return;
    }

    setSaving(true);
    try {
      const payload = {
        firstName: firstName.trim(),
        first_name: firstName.trim(),
        lastName: lastName.trim(),
        last_name: lastName.trim(),
        middleName: middleName.trim(),
        middle_name: middleName.trim(),
        phone: phone.trim(),
        mobile_number: phone.trim(),
        birthDate: birthDate.trim() || null,
        birth_date: birthDate.trim() || null,
        sex,
        civilStatus,
        civil_status: civilStatus,
        citizenship: citizenship.trim() || 'Filipino',
        address: {
          barangay: barangay.trim(),
          street: street.trim(),
          city: city.trim(),
          province: province.trim(),
          zip: zip.trim(),
          full: `${street.trim() ? street.trim() + ', ' : ''}${barangay.trim() ? barangay.trim() + ', ' : ''}${city.trim()}`,
        },
        education_details: {
          level: eduLevel,
          school: eduSchool.trim(),
          course: eduCourse.trim(),
          year_graduated: eduYear.trim(),
        },
        education: `${eduCourse.trim()} ${eduSchool.trim() ? ' - ' + eduSchool.trim() : ''}`.trim() || eduLevel,
        work_experience: {
          company: workCompany.trim(),
          position: workPosition.trim(),
          duration: workDuration.trim(),
          summary: bio.trim(),
        },
        bio: bio.trim(),
        preferences: {
          occupation1: occupation1.trim(),
          occupation2: occupation2.trim(),
          industry1: industry1.trim(),
          salary_expectation: salaryExpectation.trim(),
          preferred_location: preferredLocation.trim(),
        },
        eligibility: {
          civil_service: civilService.trim(),
          prc_license: prcLicense.trim(),
          tesda_nc: tesdaNc.trim(),
          driver_license: driverLicense.trim(),
        },
        social_status: {
          is_4ps: is4ps,
          is_pwd: isPwd,
          pwd_type: isPwd ? pwdType.trim() : '',
        },
        skills,
      };

      const res = await updateProfile(payload as any);

      if (res.success) {
        Alert.alert('Profile Saved', 'Your official DOLE-DMDP NSRP Form 1 profile has been saved successfully.', [
          { text: 'OK', onPress: () => router.back() },
        ]);
      } else {
        Alert.alert('Save Failed', res.message || 'Unable to update profile. Please verify your inputs.');
      }
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Failed to save profile changes.');
    } finally {
      setSaving(false);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.keyboardContainer}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      keyboardVerticalOffset={Platform.OS === 'ios' ? 88 : 0}>
      <View style={styles.container}>
        <ScrollView
          style={styles.mainScroll}
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
          keyboardShouldPersistTaps="handled">
          {/* Header Card */}
          <View style={styles.headerCard}>
            <View style={styles.badgeRow}>
              <View style={styles.nsrpBadge}>
                <Ionicons name="document-text" size={12} color={COLORS.emerald700} />
                <Text style={styles.nsrpBadgeText}>DOLE-DMDP NSRP Form 1</Text>
              </View>
              <View style={styles.officialSeal}>
                <Ionicons name="shield-checkmark" size={12} color={COLORS.white} />
                <Text style={styles.officialSealText}>Cebu City LGU</Text>
              </View>
            </View>
            <Text style={styles.headerTitle}>National Skills Profile</Text>
            <Text style={styles.headerSub}>
              Ensure your employment profile and preferences are accurate to unlock priority AI Cosine-Similarity job matching.
            </Text>
          </View>

          {/* Segmented Section Tabs */}
          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            style={styles.tabScroll}
            contentContainerStyle={styles.tabScrollContent}>
            <TouchableOpacity
              style={[styles.sectionTab, activeTab === 'personal' && styles.sectionTabActive]}
              onPress={() => setActiveTab('personal')}
              activeOpacity={0.8}>
              <Ionicons name="person-outline" size={15} color={activeTab === 'personal' ? COLORS.white : COLORS.slate600} />
              <Text style={[styles.sectionTabText, activeTab === 'personal' && styles.sectionTabTextActive]}>Personal</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.sectionTab, activeTab === 'address' && styles.sectionTabActive]}
              onPress={() => setActiveTab('address')}
              activeOpacity={0.8}>
              <Ionicons name="location-outline" size={15} color={activeTab === 'address' ? COLORS.white : COLORS.slate600} />
              <Text style={[styles.sectionTabText, activeTab === 'address' && styles.sectionTabTextActive]}>Address</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.sectionTab, activeTab === 'education' && styles.sectionTabActive]}
              onPress={() => setActiveTab('education')}
              activeOpacity={0.8}>
              <Ionicons name="school-outline" size={15} color={activeTab === 'education' ? COLORS.white : COLORS.slate600} />
              <Text style={[styles.sectionTabText, activeTab === 'education' && styles.sectionTabTextActive]}>Edu & Work</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.sectionTab, activeTab === 'preferences' && styles.sectionTabActive]}
              onPress={() => setActiveTab('preferences')}
              activeOpacity={0.8}>
              <Ionicons name="compass-outline" size={15} color={activeTab === 'preferences' ? COLORS.white : COLORS.slate600} />
              <Text style={[styles.sectionTabText, activeTab === 'preferences' && styles.sectionTabTextActive]}>Preferences</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.sectionTab, activeTab === 'eligibility' && styles.sectionTabActive]}
              onPress={() => setActiveTab('eligibility')}
              activeOpacity={0.8}>
              <Ionicons name="ribbon-outline" size={15} color={activeTab === 'eligibility' ? COLORS.white : COLORS.slate600} />
              <Text style={[styles.sectionTabText, activeTab === 'eligibility' && styles.sectionTabTextActive]}>Licenses & 4Ps</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.sectionTab, activeTab === 'skills' && styles.sectionTabActive]}
              onPress={() => setActiveTab('skills')}
              activeOpacity={0.8}>
              <Ionicons name="sparkles-outline" size={15} color={activeTab === 'skills' ? COLORS.white : COLORS.slate600} />
              <Text style={[styles.sectionTabText, activeTab === 'skills' && styles.sectionTabTextActive]}>
                Skills ({skills.length})
              </Text>
            </TouchableOpacity>
          </ScrollView>

          {/* Form Card Content */}
          <View style={styles.card}>
            {/* TAB 1: PERSONAL & CONTACT */}
            {activeTab === 'personal' && (
              <View>
                <Text style={styles.sectionHeading}>Personal Identification</Text>
                <Text style={styles.sectionSub}>Official identity details as printed on DOLE NSRP records.</Text>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>First Name *</Text>
                  <View style={styles.inputContainer}>
                    <TextInput
                      style={styles.input}
                      value={firstName}
                      onChangeText={setFirstName}
                      placeholder="e.g. Juan"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Middle Name</Text>
                  <View style={styles.inputContainer}>
                    <TextInput
                      style={styles.input}
                      value={middleName}
                      onChangeText={setMiddleName}
                      placeholder="e.g. Santos (leave blank if none)"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Last Name *</Text>
                  <View style={styles.inputContainer}>
                    <TextInput
                      style={styles.input}
                      value={lastName}
                      onChangeText={setLastName}
                      placeholder="e.g. Dela Cruz"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Mobile Phone Number *</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="call-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={phone}
                      onChangeText={setPhone}
                      placeholder="09123456789"
                      placeholderTextColor={COLORS.slate400}
                      keyboardType="phone-pad"
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Date of Birth (YYYY-MM-DD)</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="calendar-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={birthDate}
                      onChangeText={setBirthDate}
                      placeholder="1998-05-20"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Citizenship</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="flag-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={citizenship}
                      onChangeText={setCitizenship}
                      placeholder="Filipino"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Sex</Text>
                  <View style={styles.pillSelectorRow}>
                    {['Male', 'Female'].map((opt) => (
                      <TouchableOpacity
                        key={opt}
                        style={[styles.selectorPill, sex === opt && styles.selectorPillActive]}
                        onPress={() => setSex(opt)}>
                        <Text style={[styles.selectorPillText, sex === opt && styles.selectorPillTextActive]}>{opt}</Text>
                      </TouchableOpacity>
                    ))}
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Civil Status</Text>
                  <View style={styles.pillSelectorRow}>
                    {['Single', 'Married', 'Widowed', 'Separated'].map((opt) => (
                      <TouchableOpacity
                        key={opt}
                        style={[styles.selectorPill, civilStatus === opt && styles.selectorPillActive]}
                        onPress={() => setCivilStatus(opt)}>
                        <Text style={[styles.selectorPillText, civilStatus === opt && styles.selectorPillTextActive]}>{opt}</Text>
                      </TouchableOpacity>
                    ))}
                  </View>
                </View>
              </View>
            )}

            {/* TAB 2: ADDRESS & CEBU BARANGAY */}
            {activeTab === 'address' && (
              <View>
                <Text style={styles.sectionHeading}>Cebu City Residence</Text>
                <Text style={styles.sectionSub}>Used to match vacancies within your specific barangay or vicinity.</Text>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Barangay *</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="business-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={barangay}
                      onChangeText={setBarangay}
                      placeholder="e.g. Lahug, Guadalupe, Mabolo, Apas, Talamban..."
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>House No. / Street / Sitio</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="home-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={street}
                      onChangeText={setStreet}
                      placeholder="e.g. Salinas Drive, Sitio Mahiga"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.twoColRow}>
                  <View style={[styles.fieldGroup, { flex: 1 }]}>
                    <Text style={styles.label}>City / Municipality</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={city}
                        onChangeText={setCity}
                        placeholder="Cebu City"
                        placeholderTextColor={COLORS.slate400}
                      />
                    </View>
                  </View>

                  <View style={[styles.fieldGroup, { flex: 1 }]}>
                    <Text style={styles.label}>Postal Zip Code</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={zip}
                        onChangeText={setZip}
                        keyboardType="numeric"
                        placeholder="6000"
                        placeholderTextColor={COLORS.slate400}
                      />
                    </View>
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Province</Text>
                  <View style={styles.inputContainer}>
                    <TextInput
                      style={styles.input}
                      value={province}
                      onChangeText={setProvince}
                      placeholder="Cebu"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>
              </View>
            )}

            {/* TAB 3: EDUCATION & WORK EXPERIENCE */}
            {activeTab === 'education' && (
              <View>
                <Text style={styles.sectionHeading}>Educational Background</Text>
                <Text style={styles.sectionSub}>Highest level of schooling completed.</Text>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Highest Educational Attainment</Text>
                  <View style={styles.pillSelectorRow}>
                    {['High School', 'Vocational / TVET', 'College Level', 'College Graduate', 'Post Graduate'].map((lvl) => (
                      <TouchableOpacity
                        key={lvl}
                        style={[styles.selectorPill, eduLevel === lvl && styles.selectorPillActive]}
                        onPress={() => setEduLevel(lvl)}>
                        <Text style={[styles.selectorPillText, eduLevel === lvl && styles.selectorPillTextActive]}>{lvl}</Text>
                      </TouchableOpacity>
                    ))}
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>School / College / University</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="school-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={eduSchool}
                      onChangeText={setEduSchool}
                      placeholder="e.g. CIT-University, USC, UC, CTU"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.twoColRow}>
                  <View style={[styles.fieldGroup, { flex: 2 }]}>
                    <Text style={styles.label}>Degree / Course</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={eduCourse}
                        onChangeText={setEduCourse}
                        placeholder="e.g. BS Information Technology, Cookery NC II"
                        placeholderTextColor={COLORS.slate400}
                      />
                    </View>
                  </View>
                  <View style={[styles.fieldGroup, { flex: 1 }]}>
                    <Text style={styles.label}>Year Graduated</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={eduYear}
                        onChangeText={setEduYear}
                        placeholder="2024"
                        placeholderTextColor={COLORS.slate400}
                        keyboardType="numeric"
                      />
                    </View>
                  </View>
                </View>

                <View style={styles.divider} />

                <Text style={styles.sectionHeading}>Work Experience & Bio</Text>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Most Recent Employer / Company</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="briefcase-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={workCompany}
                      onChangeText={setWorkCompany}
                      placeholder="Company or freelance"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.twoColRow}>
                  <View style={[styles.fieldGroup, { flex: 2 }]}>
                    <Text style={styles.label}>Position / Role</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={workPosition}
                        onChangeText={setWorkPosition}
                        placeholder="e.g. Service Crew, IT Specialist"
                        placeholderTextColor={COLORS.slate400}
                      />
                    </View>
                  </View>
                  <View style={[styles.fieldGroup, { flex: 1 }]}>
                    <Text style={styles.label}>Duration</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={workDuration}
                        onChangeText={setWorkDuration}
                        placeholder="e.g. 1 yr 6 mos"
                        placeholderTextColor={COLORS.slate400}
                      />
                    </View>
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Professional Bio & Career Objective</Text>
                  <TextInput
                    style={[styles.input, styles.textArea]}
                    value={bio}
                    onChangeText={setBio}
                    placeholder="Brief summary of your skills, background, and career aspirations..."
                    placeholderTextColor={COLORS.slate400}
                    multiline
                    numberOfLines={4}
                  />
                </View>
              </View>
            )}

            {/* TAB 4: CAREER PREFERENCES */}
            {activeTab === 'preferences' && (
              <View>
                <Text style={styles.sectionHeading}>Job Preferences & Targets</Text>
                <Text style={styles.sectionSub}>Defines your preferred employment fields for targeted notifications.</Text>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Preferred Occupation 1 (Primary)</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="briefcase-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={occupation1}
                      onChangeText={setOccupation1}
                      placeholder="e.g. Web Developer, Customer Support Specialist"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Preferred Occupation 2 (Alternative)</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="briefcase-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={occupation2}
                      onChangeText={setOccupation2}
                      placeholder="e.g. Data Encoder, Sales Associate"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Preferred Industry</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="business-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={industry1}
                      onChangeText={setIndustry1}
                      placeholder="e.g. BPO / IT, Hospitality, Construction"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.twoColRow}>
                  <View style={[styles.fieldGroup, { flex: 1 }]}>
                    <Text style={styles.label}>Expected Salary (PHP)</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={salaryExpectation}
                        onChangeText={setSalaryExpectation}
                        placeholder="e.g. 25,000"
                        placeholderTextColor={COLORS.slate400}
                        keyboardType="numeric"
                      />
                    </View>
                  </View>

                  <View style={[styles.fieldGroup, { flex: 1 }]}>
                    <Text style={styles.label}>Preferred Location</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={preferredLocation}
                        onChangeText={setPreferredLocation}
                        placeholder="e.g. Cebu City"
                        placeholderTextColor={COLORS.slate400}
                      />
                    </View>
                  </View>
                </View>
              </View>
            )}

            {/* TAB 5: ELIGIBILITIES & SOCIAL WELFARE */}
            {activeTab === 'eligibility' && (
              <View>
                <Text style={styles.sectionHeading}>Licenses & Government Eligibilities</Text>
                <Text style={styles.sectionSub}>Verified civil and technical certifications.</Text>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Civil Service Eligibility</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="ribbon-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={civilService}
                      onChangeText={setCivilService}
                      placeholder="e.g. Sub-professional, Professional"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>PRC Board License</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="id-card-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={prcLicense}
                      onChangeText={setPrcLicense}
                      placeholder="License title or registration number"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>TESDA National Certificate (NC)</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="medal-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={tesdaNc}
                      onChangeText={setTesdaNc}
                      placeholder="e.g. Electrical Installation NC II, SMAW NC II"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.fieldGroup}>
                  <Text style={styles.label}>Driver's License Restriction</Text>
                  <View style={styles.inputContainer}>
                    <Ionicons name="car-outline" size={18} color={COLORS.slate400} style={styles.inputIcon} />
                    <TextInput
                      style={styles.input}
                      value={driverLicense}
                      onChangeText={setDriverLicense}
                      placeholder="e.g. Code A, B (Motorcycle / Light Vehicle)"
                      placeholderTextColor={COLORS.slate400}
                    />
                  </View>
                </View>

                <View style={styles.divider} />

                <Text style={styles.sectionHeading}>Social Welfare Classifications</Text>

                <View style={styles.switchRow}>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.switchTitle}>4Ps Beneficiary</Text>
                    <Text style={styles.switchSub}>Pantawid Pamilyang Pilipino Program registered</Text>
                  </View>
                  <Switch value={is4ps} onValueChange={setIs4ps} trackColor={{ false: COLORS.slate200, true: COLORS.primary }} />
                </View>

                <View style={[styles.switchRow, { marginTop: SPACING.md }]}>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.switchTitle}>Person with Disability (PWD)</Text>
                    <Text style={styles.switchSub}>Eligible for PWD-inclusive employment placement</Text>
                  </View>
                  <Switch value={isPwd} onValueChange={setIsPwd} trackColor={{ false: COLORS.slate200, true: COLORS.purple }} />
                </View>

                {isPwd && (
                  <View style={[styles.fieldGroup, { marginTop: SPACING.sm }]}>
                    <Text style={styles.label}>Disability Type / Classification</Text>
                    <View style={styles.inputContainer}>
                      <TextInput
                        style={styles.input}
                        value={pwdType}
                        onChangeText={setPwdType}
                        placeholder="e.g. Visual, Hearing, Orthopedic, Speech"
                        placeholderTextColor={COLORS.slate400}
                      />
                    </View>
                  </View>
                )}
              </View>
            )}

            {/* TAB 6: SKILLS MANAGEMENT */}
            {activeTab === 'skills' && (
              <View>
                <Text style={styles.sectionHeading}>Skills & Competencies</Text>
                <Text style={styles.sectionSub}>These skills directly feed the AI Cosine Similarity Matching engine.</Text>

                <View style={styles.skillInputRow}>
                  <TextInput
                    style={[styles.input, styles.skillInput]}
                    value={newSkill}
                    onChangeText={setNewSkill}
                    placeholder="Add skill (e.g. Customer Service, React)"
                    placeholderTextColor={COLORS.slate400}
                    onSubmitEditing={() => handleAddSkill()}
                  />
                  <TouchableOpacity style={styles.addSkillBtn} activeOpacity={0.85} onPress={() => handleAddSkill()}>
                    <Ionicons name="add" size={22} color={COLORS.white} />
                  </TouchableOpacity>
                </View>

                {/* Quick Add Suggestions */}
                <Text style={styles.suggestionTitle}>Popular DOLE Competencies:</Text>
                <View style={styles.suggestionRow}>
                  {QUICK_SKILL_SUGGESTIONS.map((sug) => {
                    const isAdded = skills.some((s) => s.toLowerCase() === sug.toLowerCase());
                    return (
                      <TouchableOpacity
                        key={sug}
                        style={[styles.suggestionPill, isAdded && styles.suggestionPillAdded]}
                        disabled={isAdded}
                        onPress={() => handleAddSkill(sug)}>
                        <Ionicons
                          name={isAdded ? 'checkmark' : 'add'}
                          size={12}
                          color={isAdded ? COLORS.emerald700 : COLORS.slate600}
                        />
                        <Text style={[styles.suggestionText, isAdded && styles.suggestionTextAdded]}>{sug}</Text>
                      </TouchableOpacity>
                    );
                  })}
                </View>

                <View style={styles.divider} />

                <Text style={styles.activeSkillsLabel}>Your Profile Skills ({skills.length})</Text>
                <View style={styles.chipsContainer}>
                  {skills.length > 0 ? (
                    skills.map((skill, index) => (
                      <View key={index} style={styles.chip}>
                        <Ionicons name="checkmark-circle" size={14} color={COLORS.primary} style={{ marginRight: 4 }} />
                        <Text style={styles.chipText}>{skill}</Text>
                        <TouchableOpacity
                          onPress={() => handleRemoveSkill(index)}
                          hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
                          style={{ marginLeft: 4 }}>
                          <Ionicons name="close-circle" size={16} color={COLORS.primaryDark} />
                        </TouchableOpacity>
                      </View>
                    ))
                  ) : (
                    <Text style={styles.noSkillsText}>No skills added yet. Add at least 3 skills to reach 80% profile strength.</Text>
                  )}
                </View>
              </View>
            )}
          </View>
          <View style={{ height: 100 }} />
        </ScrollView>

        {/* Sticky Bottom Action Bar */}
        <View style={styles.stickyFooter}>
          <TouchableOpacity
            style={[styles.saveBtn, saving && { opacity: 0.8 }]}
            activeOpacity={0.88}
            onPress={handleSave}
            disabled={saving}>
            {saving ? (
              <View style={styles.savingRow}>
                <ActivityIndicator color={COLORS.white} size="small" />
                <Text style={styles.saveBtnText}>Saving NSRP Profile...</Text>
              </View>
            ) : (
              <View style={styles.savingRow}>
                <Text style={styles.saveBtnText}>Save Profile Changes</Text>
                <Ionicons name="checkmark-circle" size={18} color={COLORS.white} />
              </View>
            )}
          </TouchableOpacity>
        </View>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  keyboardContainer: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  mainScroll: {
    flex: 1,
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
  badgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: SPACING.xs,
  },
  nsrpBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: COLORS.emerald50,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.full,
  },
  nsrpBadgeText: {
    fontSize: 10,
    fontWeight: '800',
    color: COLORS.emerald800,
  },
  officialSeal: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: 'rgba(255,255,255,0.15)',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: BORDER_RADIUS.full,
  },
  officialSealText: {
    fontSize: 10,
    fontWeight: '700',
    color: COLORS.white,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '800',
    color: COLORS.white,
    marginTop: 4,
  },
  headerSub: {
    fontSize: 12,
    color: COLORS.slate300,
    marginTop: 4,
    lineHeight: 16,
  },
  tabScroll: {
    maxHeight: 46,
    marginBottom: SPACING.md,
  },
  tabScrollContent: {
    gap: 8,
    paddingRight: SPACING.md,
  },
  sectionTab: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: 14,
    paddingVertical: 10,
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
    borderColor: COLORS.slate200,
  },
  sectionTabActive: {
    backgroundColor: COLORS.navy,
    borderColor: COLORS.navy,
  },
  sectionTabText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.slate600,
  },
  sectionTabTextActive: {
    color: COLORS.white,
  },
  card: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    ...SHADOWS.sm,
    borderWidth: 1,
    borderColor: COLORS.slate100,
  },
  sectionHeading: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.navy,
    letterSpacing: -0.2,
  },
  sectionSub: {
    fontSize: 12,
    color: COLORS.slate500,
    marginTop: 2,
    marginBottom: SPACING.md,
  },
  fieldGroup: {
    marginBottom: SPACING.md,
  },
  label: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.slate700,
    marginBottom: 6,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.slate50,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    borderRadius: BORDER_RADIUS.md,
    paddingHorizontal: SPACING.md,
    minHeight: 46,
  },
  inputIcon: {
    marginRight: 8,
  },
  input: {
    flex: 1,
    fontSize: 14,
    color: COLORS.slate900,
    paddingVertical: 10,
  },
  textArea: {
    backgroundColor: COLORS.slate50,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    borderRadius: BORDER_RADIUS.md,
    padding: SPACING.md,
    textAlignVertical: 'top',
    minHeight: 80,
  },
  twoColRow: {
    flexDirection: 'row',
    gap: SPACING.md,
  },
  pillSelectorRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  selectorPill: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.slate100,
    borderWidth: 1,
    borderColor: COLORS.slate200,
  },
  selectorPillActive: {
    backgroundColor: COLORS.primaryUltraLight,
    borderColor: COLORS.primary,
  },
  selectorPillText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.slate700,
  },
  selectorPillTextActive: {
    color: COLORS.primary,
    fontWeight: '800',
  },
  divider: {
    height: 1,
    backgroundColor: COLORS.slate100,
    marginVertical: SPACING.lg,
  },
  switchRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 6,
  },
  switchTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.slate900,
  },
  switchSub: {
    fontSize: 11,
    color: COLORS.slate500,
    marginTop: 2,
  },
  skillInputRow: {
    flexDirection: 'row',
    gap: 8,
    marginBottom: SPACING.md,
  },
  skillInput: {
    backgroundColor: COLORS.slate50,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    borderRadius: BORDER_RADIUS.md,
    paddingHorizontal: SPACING.md,
    minHeight: 46,
  },
  addSkillBtn: {
    backgroundColor: COLORS.primary,
    width: 46,
    height: 46,
    borderRadius: BORDER_RADIUS.md,
    alignItems: 'center',
    justifyContent: 'center',
    ...SHADOWS.sm,
  },
  suggestionTitle: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.slate500,
    marginBottom: 6,
    textTransform: 'uppercase',
  },
  suggestionRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 6,
    marginBottom: SPACING.md,
  },
  suggestionPill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: COLORS.slate100,
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.full,
    borderWidth: 1,
    borderColor: COLORS.slate200,
  },
  suggestionPillAdded: {
    backgroundColor: COLORS.emerald50,
    borderColor: COLORS.emerald200,
  },
  suggestionText: {
    fontSize: 11,
    color: COLORS.slate700,
    fontWeight: '600',
  },
  suggestionTextAdded: {
    color: COLORS.emerald800,
  },
  activeSkillsLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.slate700,
    marginBottom: 8,
  },
  chipsContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.primaryUltraLight,
    borderColor: COLORS.primaryLight,
    borderWidth: 1,
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.full,
  },
  chipText: {
    fontSize: 12,
    color: COLORS.primaryDark,
    fontWeight: '700',
  },
  noSkillsText: {
    fontSize: 12,
    color: COLORS.slate400,
    fontStyle: 'italic',
    paddingVertical: 8,
  },
  stickyFooter: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: COLORS.white,
    paddingHorizontal: SPACING.md,
    paddingVertical: SPACING.md,
    borderTopWidth: 1,
    borderTopColor: COLORS.slate100,
    ...SHADOWS.lg,
  },
  saveBtn: {
    backgroundColor: COLORS.primary,
    borderRadius: BORDER_RADIUS.lg,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    ...SHADOWS.md,
  },
  savingRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  saveBtnText: {
    fontSize: 15,
    fontWeight: '800',
    color: COLORS.white,
  },
});

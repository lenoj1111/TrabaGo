import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';

import { trainingService } from '../../src/services/trainingService';
import { TrainingProgramItem, QuizQuestion } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';

export default function TrainingDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();

  const [training, setTraining] = useState<TrainingProgramItem | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  // Quiz state
  const [quizMode, setQuizMode] = useState<boolean>(false);
  const [currentQuestionIndex, setCurrentQuestionIndex] = useState<number>(0);
  const [selectedAnswers, setSelectedAnswers] = useState<Record<number, number>>({});
  const [quizFinished, setQuizFinished] = useState<boolean>(false);
  const [quizResult, setQuizResult] = useState<{ score: number; total: number; passed: boolean } | null>(null);
  const [submittingQuiz, setSubmittingQuiz] = useState<boolean>(false);

  useEffect(() => {
    if (id) {
      trainingService.getTrainingById(id).then((data) => {
        setTraining(data);
        setLoading(false);
      });
    }
  }, [id]);

  // Consolidate questions from topics or directly from program
  const getQuestions = (): QuizQuestion[] => {
    if (!training) return [];
    if (training.questions && training.questions.length > 0) {
      return training.questions;
    }
    const topicQs: QuizQuestion[] = [];
    training.topics?.forEach((t) => {
      if (t.questions && t.questions.length > 0) {
        topicQs.push(...t.questions);
      }
    });
    return topicQs;
  };

  const questions = getQuestions();

  const handleSelectOption = (qIndex: number, optionIndex: number) => {
    setSelectedAnswers((prev) => ({
      ...prev,
      [qIndex]: optionIndex,
    }));
  };

  const handleNext = () => {
    if (currentQuestionIndex < questions.length - 1) {
      setCurrentQuestionIndex((prev) => prev + 1);
    }
  };

  const handlePrev = () => {
    if (currentQuestionIndex > 0) {
      setCurrentQuestionIndex((prev) => prev - 1);
    }
  };

  const handleSubmitQuiz = async () => {
    let score = 0;
    questions.forEach((q, idx) => {
      if (selectedAnswers[idx] === q.answer) {
        score += 1;
      }
    });

    setSubmittingQuiz(true);
    try {
      const res = await trainingService.submitQuiz(id, {
        score,
        total: questions.length,
        answers: selectedAnswers,
      });

      setQuizResult({
        score,
        total: questions.length,
        passed: res.passed ?? score >= questions.length * 0.7,
      });
      setQuizFinished(true);
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Failed to submit quiz.');
    } finally {
      setSubmittingQuiz(false);
    }
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={COLORS.primary} />
        <Text style={styles.loadingText}>Loading training module...</Text>
      </View>
    );
  }

  if (!training) {
    return (
      <View style={styles.centerContainer}>
        <Ionicons name="alert-circle-outline" size={64} color={COLORS.danger} />
        <Text style={styles.emptyTitle}>Course Not Found</Text>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Text style={styles.backBtnText}>Back to Training</Text>
        </TouchableOpacity>
      </View>
    );
  }

  // If Quiz Completed View
  if (quizFinished && quizResult) {
    const percentage = Math.round((quizResult.score / quizResult.total) * 100);
    return (
      <View style={styles.container}>
        <View style={styles.resultCard}>
          <View
            style={[
              styles.resultIconBox,
              { backgroundColor: quizResult.passed ? COLORS.successBg : COLORS.dangerBg },
            ]}>
            <Ionicons
              name={quizResult.passed ? 'ribbon' : 'close-circle'}
              size={56}
              color={quizResult.passed ? COLORS.success : COLORS.danger}
            />
          </View>

          <Text style={styles.resultTitle}>
            {quizResult.passed ? 'Assessment Passed! 🎉' : 'Assessment Incomplete'}
          </Text>
          <Text style={styles.resultScore}>
            Score: {quizResult.score} / {quizResult.total} ({percentage}%)
          </Text>

          <Text style={styles.resultDesc}>
            {quizResult.passed
              ? 'Congratulations! Your competency has been verified and registered with the Cebu DMDP vocational facilitation desk.'
              : 'You need at least 70% to receive a verified DMDP course completion certificate. You may review the topics and try again.'}
          </Text>

          <TouchableOpacity
            style={styles.retakeBtn}
            onPress={() => {
              setQuizMode(false);
              setQuizFinished(false);
              setSelectedAnswers({});
              setCurrentQuestionIndex(0);
            }}>
            <Text style={styles.retakeBtnText}>Review Course Material</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.doneBtn} onPress={() => router.replace('/(tabs)/training')}>
            <Text style={styles.doneBtnText}>Return to Courses</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  // If Active Quiz Mode
  if (quizMode && questions.length > 0) {
    const currentQ = questions[currentQuestionIndex];
    const choices = currentQ.choices || currentQ.options || [];

    return (
      <View style={styles.container}>
        <ScrollView contentContainerStyle={styles.scrollContent}>
          {/* Progress Header */}
          <View style={styles.quizHeader}>
            <Text style={styles.quizProgressText}>
              Question {currentQuestionIndex + 1} of {questions.length}
            </Text>
            <View style={styles.progressBarBg}>
              <View
                style={[
                  styles.progressBarFill,
                  { width: `${((currentQuestionIndex + 1) / questions.length) * 100}%` },
                ]}
              />
            </View>
          </View>

          {/* Question Card */}
          <View style={styles.questionCard}>
            <Text style={styles.questionText}>{currentQ.question}</Text>

            <View style={styles.choicesList}>
              {choices.map((choice, cIndex) => {
                const isSelected = selectedAnswers[currentQuestionIndex] === cIndex;
                return (
                  <TouchableOpacity
                    key={cIndex}
                    style={[styles.choiceItem, isSelected && styles.choiceItemSelected]}
                    onPress={() => handleSelectOption(currentQuestionIndex, cIndex)}>
                    <View style={[styles.choiceBullet, isSelected && styles.choiceBulletSelected]}>
                      <Text style={[styles.choiceLetter, isSelected && styles.choiceLetterSelected]}>
                        {String.fromCharCode(65 + cIndex)}
                      </Text>
                    </View>
                    <Text style={[styles.choiceText, isSelected && styles.choiceTextSelected]}>
                      {choice}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </View>
          </View>

          {/* Navigation Controls */}
          <View style={styles.quizNavRow}>
            {currentQuestionIndex > 0 ? (
              <TouchableOpacity style={styles.navBtnPrev} onPress={handlePrev}>
                <Ionicons name="arrow-back" size={16} color={COLORS.navy} />
                <Text style={styles.navBtnPrevText}>Previous</Text>
              </TouchableOpacity>
            ) : <View style={{ flex: 1 }} />}

            {currentQuestionIndex < questions.length - 1 ? (
              <TouchableOpacity
                style={[
                  styles.navBtnNext,
                  selectedAnswers[currentQuestionIndex] === undefined && { opacity: 0.5 },
                ]}
                disabled={selectedAnswers[currentQuestionIndex] === undefined}
                onPress={handleNext}>
                <Text style={styles.navBtnNextText}>Next</Text>
                <Ionicons name="arrow-forward" size={16} color={COLORS.white} />
              </TouchableOpacity>
            ) : (
              <TouchableOpacity
                style={[styles.navBtnNext, submittingQuiz && { opacity: 0.7 }]}
                disabled={submittingQuiz}
                onPress={handleSubmitQuiz}>
                {submittingQuiz ? (
                  <ActivityIndicator color={COLORS.white} size="small" />
                ) : (
                  <>
                    <Text style={styles.navBtnNextText}>Submit Quiz</Text>
                    <Ionicons name="checkmark-circle" size={16} color={COLORS.white} />
                  </>
                )}
              </TouchableOpacity>
            )}
          </View>
        </ScrollView>
      </View>
    );
  }

  // Course Overview Screen
  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Banner */}
        <View style={styles.courseHeader}>
          <View style={styles.badgeRow}>
            <View style={styles.badge}>
              <Text style={styles.badgeText}>{training.trainingType || 'Online'}</Text>
            </View>
            <View style={[styles.badge, styles.dmdpBadge]}>
              <Text style={styles.dmdpBadgeText}>DMDP Verified</Text>
            </View>
          </View>
          <Text style={styles.courseTitle}>{training.title}</Text>
          <Text style={styles.courseMeta}>
            Duration: {training.durationMonths || training.duration_months || 1} Month(s) • Cebu City DMDP
          </Text>
        </View>

        {/* Modules / Topics */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Course Syllabus & Topics</Text>
          {training.topics && training.topics.length > 0 ? (
            training.topics.map((topic, index) => (
              <View key={topic.id || index} style={styles.topicItem}>
                <View style={styles.topicNumber}>
                  <Text style={styles.topicNumberText}>{index + 1}</Text>
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={styles.topicTitle}>{topic.title}</Text>
                  {topic.videoUrl && (
                    <View style={styles.videoBadge}>
                      <Ionicons name="videocam-outline" size={12} color={COLORS.primary} />
                      <Text style={styles.videoBadgeText}>Includes DMDP Lecture Video</Text>
                    </View>
                  )}
                </View>
              </View>
            ))
          ) : (
            <Text style={styles.emptyTopicText}>Full syllabus will be unlocked upon session start.</Text>
          )}
        </View>

        {/* Assessment Card */}
        <View style={styles.assessmentCard}>
          <View style={styles.assessmentIconBox}>
            <Ionicons name="ribbon-outline" size={32} color={COLORS.primary} />
          </View>
          <Text style={styles.assessmentTitle}>Competency Assessment & Quiz</Text>
          <Text style={styles.assessmentDesc}>
            Test your comprehension to receive a DMDP verified digital certificate.
            {questions.length > 0 ? ` (${questions.length} questions available)` : ''}
          </Text>

          <TouchableOpacity
            style={styles.startQuizBtn}
            onPress={() => {
              if (questions.length === 0) {
                Alert.alert('Notice', 'No quiz questions currently available for this module.');
              } else {
                setQuizMode(true);
              }
            }}>
            <Text style={styles.startQuizBtnText}>Start Competency Quiz</Text>
            <Ionicons name="play" size={16} color={COLORS.white} />
          </TouchableOpacity>
        </View>

        <View style={{ height: 40 }} />
      </ScrollView>
    </View>
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
  courseHeader: {
    backgroundColor: COLORS.navy,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
  },
  badgeRow: {
    flexDirection: 'row',
    gap: SPACING.sm,
    marginBottom: SPACING.sm,
  },
  badge: {
    backgroundColor: 'rgba(255,255,255,0.15)',
    paddingHorizontal: SPACING.sm,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
  },
  badgeText: {
    fontSize: 11,
    color: COLORS.white,
    fontWeight: 'bold',
  },
  dmdpBadge: {
    backgroundColor: COLORS.primary,
  },
  dmdpBadgeText: {
    fontSize: 11,
    color: COLORS.white,
    fontWeight: 'bold',
  },
  courseTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 4,
  },
  courseMeta: {
    fontSize: 12,
    color: COLORS.primaryUltraLight,
    marginTop: 6,
  },
  section: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: SPACING.md,
  },
  topicItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: SPACING.sm,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.borderLight,
    gap: SPACING.md,
  },
  topicNumber: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: COLORS.primaryUltraLight,
    justifyContent: 'center',
    alignItems: 'center',
  },
  topicNumberText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
  },
  topicTitle: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.navy,
  },
  videoBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: 2,
  },
  videoBadgeText: {
    fontSize: 11,
    color: COLORS.primary,
  },
  emptyTopicText: {
    fontSize: 13,
    color: COLORS.textMuted,
    fontStyle: 'italic',
  },
  assessmentCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.xl,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.primaryLight,
    ...SHADOWS.sm,
  },
  assessmentIconBox: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: COLORS.primaryUltraLight,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  assessmentTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  assessmentDesc: {
    fontSize: 12,
    color: COLORS.textSecondary,
    textAlign: 'center',
    marginTop: 4,
    marginBottom: SPACING.lg,
    lineHeight: 18,
  },
  startQuizBtn: {
    backgroundColor: COLORS.primary,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingHorizontal: SPACING.xl,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
  },
  startQuizBtnText: {
    color: COLORS.white,
    fontSize: 14,
    fontWeight: 'bold',
  },
  // Active Quiz
  quizHeader: {
    marginBottom: SPACING.md,
  },
  quizProgressText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginBottom: 6,
  },
  progressBarBg: {
    height: 6,
    backgroundColor: COLORS.border,
    borderRadius: 3,
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    backgroundColor: COLORS.primary,
  },
  questionCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.xl,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.sm,
  },
  questionText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.navy,
    lineHeight: 22,
    marginBottom: SPACING.lg,
  },
  choicesList: {
    gap: SPACING.md,
  },
  choiceItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: SPACING.md,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    backgroundColor: COLORS.surfaceSubtle,
    gap: SPACING.md,
  },
  choiceItemSelected: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.primaryUltraLight,
  },
  choiceBullet: {
    width: 28,
    height: 28,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: COLORS.border,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
  },
  choiceBulletSelected: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.primary,
  },
  choiceLetter: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.textSecondary,
  },
  choiceLetterSelected: {
    color: COLORS.white,
  },
  choiceText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.text,
  },
  choiceTextSelected: {
    fontWeight: 'bold',
    color: COLORS.primaryDark,
  },
  quizNavRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: SPACING.lg,
    gap: SPACING.md,
  },
  navBtnPrev: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
    borderWidth: 1,
    borderColor: COLORS.border,
    backgroundColor: COLORS.white,
    gap: 6,
  },
  navBtnPrevText: {
    color: COLORS.navy,
    fontSize: 13,
    fontWeight: 'bold',
  },
  navBtnNext: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.primary,
    gap: 6,
  },
  navBtnNextText: {
    color: COLORS.white,
    fontSize: 13,
    fontWeight: 'bold',
  },
  // Quiz Result
  resultCard: {
    backgroundColor: COLORS.white,
    margin: SPACING.lg,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    alignItems: 'center',
    ...SHADOWS.md,
  },
  resultIconBox: {
    width: 88,
    height: 88,
    borderRadius: 44,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: SPACING.lg,
  },
  resultTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.navy,
  },
  resultScore: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primaryDark,
    marginTop: 6,
  },
  resultDesc: {
    fontSize: 13,
    color: COLORS.textSecondary,
    textAlign: 'center',
    marginTop: SPACING.md,
    marginBottom: SPACING.xl,
    lineHeight: 18,
  },
  retakeBtn: {
    backgroundColor: COLORS.surfaceSubtle,
    width: '100%',
    paddingVertical: 14,
    borderRadius: BORDER_RADIUS.md,
    alignItems: 'center',
    marginBottom: SPACING.sm,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  retakeBtnText: {
    color: COLORS.navy,
    fontWeight: 'bold',
    fontSize: 14,
  },
  doneBtn: {
    backgroundColor: COLORS.primary,
    width: '100%',
    paddingVertical: 14,
    borderRadius: BORDER_RADIUS.md,
    alignItems: 'center',
  },
  doneBtnText: {
    color: COLORS.white,
    fontWeight: 'bold',
    fontSize: 14,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: SPACING.xxl,
  },
  loadingText: {
    marginTop: SPACING.md,
    fontSize: 14,
    color: COLORS.textSecondary,
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.navy,
    marginTop: SPACING.md,
  },
  backBtn: {
    marginTop: SPACING.md,
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.sm,
    borderRadius: BORDER_RADIUS.md,
  },
  backBtnText: {
    color: COLORS.white,
    fontWeight: 'bold',
  },
});

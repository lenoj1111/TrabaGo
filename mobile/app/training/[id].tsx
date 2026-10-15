import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  Linking,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { Ionicons } from '@expo/vector-icons';
import * as WebBrowser from 'expo-web-browser';

import { trainingService } from '../../src/services/trainingService';
import { TrainingProgramItem, QuizQuestion } from '../../src/types';
import { COLORS, SPACING, BORDER_RADIUS, SHADOWS } from '../../src/constants/theme';

interface QuizResultState {
  score: number;
  total: number;
  percentage: number;
  passed: boolean;
  certificateNo?: string | null;
}

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
  const [quizResult, setQuizResult] = useState<QuizResultState | null>(null);
  const [submittingQuiz, setSubmittingQuiz] = useState<boolean>(false);
  const [showReview, setShowReview] = useState<boolean>(false);

  useEffect(() => {
    if (id) {
      setLoading(true);
      trainingService
        .getTrainingById(id)
        .then((data) => {
          setTraining(data);
        })
        .catch((err) => {
          console.warn('[ErrorHandling] Failed to fetch training details:', err);
          setTraining(null);
        })
        .finally(() => {
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

    const total = questions.length;
    const percentage = total > 0 ? Math.round((score / total) * 100) : 0;
    const passingThreshold = training?.passing_score ?? 80;

    setSubmittingQuiz(true);
    try {
      const res = await trainingService.submitQuiz(id, {
        score,
        total,
        answers: selectedAnswers,
      });

      const passed = res.passed ?? percentage >= passingThreshold;

      setQuizResult({
        score,
        total,
        percentage,
        passed,
        certificateNo: res.certificate_no || null,
      });
      setQuizFinished(true);
      setShowReview(!passed); // Auto-show review if failed to help the user learn
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Failed to submit quiz.');
    } finally {
      setSubmittingQuiz(false);
    }
  };

  // Direct In-App Video Playback with fallbacks
  const handleWatchVideo = async (url: string) => {
    try {
      await WebBrowser.openBrowserAsync(url, {
        presentationStyle: WebBrowser.WebBrowserPresentationStyle.FULL_SCREEN,
        toolbarColor: COLORS.navy,
        controlsColor: COLORS.white,
      });
    } catch {
      try {
        await Linking.openURL(url);
      } catch (e: any) {
        Alert.alert('Unable to Play Video', e.message || 'Could not open video URL.');
      }
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
        <View style={styles.emptyIconCircle}>
          <Ionicons name="alert-circle-outline" size={36} color={COLORS.danger} />
        </View>
        <Text style={styles.emptyTitle}>Course Not Found</Text>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Text style={styles.backBtnText}>Back to Training</Text>
        </TouchableOpacity>
      </View>
    );
  }

  // If Quiz Completed View
  if (quizFinished && quizResult) {
    const passingThreshold = training.passing_score ?? 80;

    return (
      <ScrollView style={styles.container} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <View style={styles.resultCard}>
          <View
            style={[
              styles.resultIconBox,
              { backgroundColor: quizResult.passed ? COLORS.emerald50 : COLORS.dangerBg },
            ]}>
            <Ionicons
              name={quizResult.passed ? 'ribbon' : 'close-circle'}
              size={56}
              color={quizResult.passed ? COLORS.emerald600 : COLORS.danger}
            />
          </View>

          <Text style={styles.resultTitle}>
            {quizResult.passed ? 'Competency Assessment Passed!' : 'Assessment Incomplete'}
          </Text>

          <View style={styles.scoreBadge}>
            <Text style={styles.resultScore}>
              Score: {quizResult.score} / {quizResult.total} ({quizResult.percentage}%)
            </Text>
          </View>

          <Text style={styles.resultDesc}>
            {quizResult.passed
              ? `Congratulations! You scored ${quizResult.percentage}%, exceeding the required ${passingThreshold}% benchmark. Your official DMDP competency credential has been issued.`
              : `You scored ${quizResult.percentage}%. A minimum passing score of ${passingThreshold}% is required to earn the official DMDP certificate. Review the questions and lecture materials below.`}
          </Text>

          {/* Certificate Awarded Card if Passed */}
          {quizResult.passed && (
            <View style={styles.certificateBanner}>
              <View style={styles.certBadgeHeader}>
                <Ionicons name="shield-checkmark" size={18} color={COLORS.emerald700} />
                <Text style={styles.certBadgeHeaderText}>OFFICIAL CERTIFICATE ISSUED</Text>
              </View>
              {quizResult.certificateNo ? (
                <Text style={styles.certBadgeNo}>#{quizResult.certificateNo}</Text>
              ) : (
                <Text style={styles.certBadgeNo}>#DMDP-CERT-ISSUED</Text>
              )}
              <Text style={styles.certBadgeCourse}>{training.title}</Text>
              <Text style={styles.certBadgeSub}>
                Automatically added to your Profile Skills & Credentials Vault.
              </Text>

              <TouchableOpacity
                style={styles.viewVaultBtn}
                activeOpacity={0.85}
                onPress={() => router.push('/profile/vault')}>
                <Ionicons name="folder-open-outline" size={16} color={COLORS.white} />
                <Text style={styles.viewVaultBtnText}>View in Credentials Vault</Text>
              </TouchableOpacity>
            </View>
          )}

          {/* Action Buttons */}
          <View style={styles.resultActionRow}>
            <TouchableOpacity
              style={styles.toggleReviewBtn}
              activeOpacity={0.85}
              onPress={() => setShowReview(!showReview)}>
              <Ionicons
                name={showReview ? 'chevron-up-circle-outline' : 'help-circle-outline'}
                size={16}
                color={COLORS.navy}
              />
              <Text style={styles.toggleReviewBtnText}>
                {showReview ? 'Hide Answer Review' : 'Review Assessment Answers'}
              </Text>
            </TouchableOpacity>

            {!quizResult.passed && (
              <TouchableOpacity
                style={styles.retakeBtn}
                activeOpacity={0.85}
                onPress={() => {
                  setQuizMode(true);
                  setQuizFinished(false);
                  setSelectedAnswers({});
                  setCurrentQuestionIndex(0);
                  setShowReview(false);
                }}>
                <Ionicons name="refresh" size={16} color={COLORS.white} />
                <Text style={styles.retakeBtnText}>Retake Assessment</Text>
              </TouchableOpacity>
            )}

            <TouchableOpacity
              style={styles.doneBtn}
              activeOpacity={0.85}
              onPress={() => {
                setQuizMode(false);
                setQuizFinished(false);
                setSelectedAnswers({});
                setCurrentQuestionIndex(0);
                router.replace('/(tabs)/training');
              }}>
              <Text style={styles.doneBtnText}>Return to Training Hub</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Detailed Answer Review Section */}
        {showReview && (
          <View style={styles.reviewSection}>
            <Text style={styles.reviewSectionTitle}>Question-by-Question Review</Text>
            <Text style={styles.reviewSectionSub}>
              Review your answers against official DOLE-DMDP criteria.
            </Text>

            {questions.map((q, qIdx) => {
              const userChoice = selectedAnswers[qIdx];
              const isCorrect = userChoice === q.answer;
              const choices = q.choices || q.options || [];

              return (
                <View
                  key={qIdx}
                  style={[styles.reviewCard, isCorrect ? styles.reviewCardCorrect : styles.reviewCardIncorrect]}>
                  <View style={styles.reviewCardHeader}>
                    <View style={[styles.qNumberPill, isCorrect ? styles.qPillCorrect : styles.qPillIncorrect]}>
                      <Text style={[styles.qNumberText, isCorrect ? styles.qTextCorrect : styles.qTextIncorrect]}>
                        Q{qIdx + 1}
                      </Text>
                    </View>
                    <View style={styles.reviewStatusTag}>
                      <Ionicons
                        name={isCorrect ? 'checkmark-circle' : 'close-circle'}
                        size={14}
                        color={isCorrect ? COLORS.emerald700 : COLORS.danger}
                      />
                      <Text
                        style={[
                          styles.reviewStatusText,
                          { color: isCorrect ? COLORS.emerald700 : COLORS.danger },
                        ]}>
                        {isCorrect ? 'Correct' : 'Incorrect'}
                      </Text>
                    </View>
                  </View>

                  <Text style={styles.reviewQuestionText}>{q.question}</Text>

                  <View style={styles.reviewChoicesList}>
                    {choices.map((choice, cIdx) => {
                      const isUserSelected = userChoice === cIdx;
                      const isTargetCorrect = q.answer === cIdx;

                      let itemStyle = styles.reviewChoiceItem;
                      let textStyle = styles.reviewChoiceText;

                      if (isTargetCorrect) {
                        itemStyle = styles.reviewChoiceCorrect;
                        textStyle = styles.reviewChoiceTextCorrect;
                      } else if (isUserSelected && !isCorrect) {
                        itemStyle = styles.reviewChoiceWrong;
                        textStyle = styles.reviewChoiceTextWrong;
                      }

                      return (
                        <View key={cIdx} style={itemStyle}>
                          <View style={styles.choiceBullet}>
                            <Text style={styles.choiceLetter}>{String.fromCharCode(65 + cIdx)}</Text>
                          </View>
                          <Text style={textStyle}>{choice}</Text>
                          {isTargetCorrect && (
                            <Ionicons name="checkmark-circle" size={16} color={COLORS.emerald700} style={{ marginLeft: 'auto' }} />
                          )}
                          {isUserSelected && !isCorrect && (
                            <Ionicons name="close-circle" size={16} color={COLORS.danger} style={{ marginLeft: 'auto' }} />
                          )}
                        </View>
                      );
                    })}
                  </View>
                </View>
              );
            })}
          </View>
        )}

        <View style={{ height: 40 }} />
      </ScrollView>
    );
  }

  // If Active Quiz Mode
  if (quizMode && questions.length > 0) {
    const currentQ = questions[currentQuestionIndex];
    const choices = currentQ.choices || currentQ.options || [];
    const answeredCount = Object.keys(selectedAnswers).length;

    return (
      <View style={styles.container}>
        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          {/* Progress Header */}
          <View style={styles.quizHeader}>
            <View style={styles.quizHeaderTop}>
              <Text style={styles.quizProgressText}>
                Question {currentQuestionIndex + 1} of {questions.length}
              </Text>
              <Text style={styles.answeredCountText}>
                {answeredCount}/{questions.length} Answered
              </Text>
            </View>
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
            <View style={styles.qBadgeRow}>
              <View style={styles.qNumBadge}>
                <Text style={styles.qNumBadgeText}>QUESTION {currentQuestionIndex + 1}</Text>
              </View>
              <Text style={styles.passingNote}>Passing: {training.passing_score ?? 80}%</Text>
            </View>

            <Text style={styles.questionText}>{currentQ.question}</Text>

            <View style={styles.choicesList}>
              {choices.map((choice, cIndex) => {
                const isSelected = selectedAnswers[currentQuestionIndex] === cIndex;
                return (
                  <TouchableOpacity
                    key={cIndex}
                    activeOpacity={0.85}
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
              <TouchableOpacity style={styles.navBtnPrev} activeOpacity={0.85} onPress={handlePrev}>
                <Ionicons name="arrow-back" size={16} color={COLORS.navy} />
                <Text style={styles.navBtnPrevText}>Previous</Text>
              </TouchableOpacity>
            ) : (
              <TouchableOpacity
                style={styles.cancelQuizBtn}
                activeOpacity={0.85}
                onPress={() => {
                  Alert.alert('Exit Assessment', 'Are you sure you want to exit? Your progress will not be saved.', [
                    { text: 'Cancel', style: 'cancel' },
                    { text: 'Exit', onPress: () => setQuizMode(false) },
                  ]);
                }}>
                <Ionicons name="close" size={16} color={COLORS.slate500} />
                <Text style={styles.cancelQuizBtnText}>Exit</Text>
              </TouchableOpacity>
            )}

            {currentQuestionIndex < questions.length - 1 ? (
              <TouchableOpacity
                style={[
                  styles.navBtnNext,
                  selectedAnswers[currentQuestionIndex] === undefined && { opacity: 0.5 },
                ]}
                activeOpacity={0.88}
                disabled={selectedAnswers[currentQuestionIndex] === undefined}
                onPress={handleNext}>
                <Text style={styles.navBtnNextText}>Next</Text>
                <Ionicons name="arrow-forward" size={16} color={COLORS.white} />
              </TouchableOpacity>
            ) : (
              <TouchableOpacity
                style={[styles.navBtnSubmit, submittingQuiz && { opacity: 0.7 }]}
                activeOpacity={0.88}
                disabled={submittingQuiz || selectedAnswers[currentQuestionIndex] === undefined}
                onPress={handleSubmitQuiz}>
                {submittingQuiz ? (
                  <ActivityIndicator color={COLORS.white} size="small" />
                ) : (
                  <>
                    <Text style={styles.navBtnSubmitText}>Submit Assessment</Text>
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
        {/* Course Header Banner */}
        <View style={styles.courseHeader}>
          <View style={styles.badgeRow}>
            <View style={styles.badge}>
              <Text style={styles.badgeText}>{training.trainingType || training.training_type || 'Online'}</Text>
            </View>
            <View style={[styles.badge, styles.dmdpBadge]}>
              <Ionicons name="shield-checkmark" size={12} color={COLORS.white} style={{ marginRight: 3 }} />
              <Text style={styles.dmdpBadgeText}>DMDP Verified</Text>
            </View>
          </View>
          <Text style={styles.courseTitle}>{training.title}</Text>
          <Text style={styles.courseMeta}>
            Duration: {training.durationMonths || training.duration_months || 1} Month(s) • Passing Score: {training.passing_score ?? 80}%
          </Text>
        </View>

        {/* Modules / Topics */}
        <View style={styles.section}>
          <View style={styles.sectionHeaderRow}>
            <Text style={styles.sectionTitle}>Course Syllabus & Topics</Text>
            <Text style={styles.sectionSubtitle}>
              {training.topics?.length || 0} {training.topics?.length === 1 ? 'module' : 'modules'}
            </Text>
          </View>

          {training.topics && training.topics.length > 0 ? (
            training.topics.map((topic, index) => {
              const video = topic.videoUrl || topic.video_url;
              return (
                <View key={topic.id || index} style={styles.topicCard}>
                  <View style={styles.topicMainRow}>
                    <View style={styles.topicNumber}>
                      <Text style={styles.topicNumberText}>{index + 1}</Text>
                    </View>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.topicTitle}>{topic.title}</Text>
                      {video ? (
                        <View style={styles.videoBadge}>
                          <Ionicons name="videocam" size={12} color={COLORS.emerald700} />
                          <Text style={styles.videoBadgeText}>DMDP Lecture Video</Text>
                        </View>
                      ) : (
                        <Text style={styles.textOnlyBadge}>Reading & Competency Module</Text>
                      )}
                    </View>
                  </View>

                  {/* Single Direct Watch Video Button (No redundant duplicates) */}
                  {video ? (
                    <TouchableOpacity
                      style={styles.watchVideoBtn}
                      activeOpacity={0.85}
                      onPress={() => handleWatchVideo(video)}>
                      <Ionicons name="play-circle" size={18} color={COLORS.white} />
                      <Text style={styles.watchVideoBtnText}>Watch Lecture Video</Text>
                    </TouchableOpacity>
                  ) : null}
                </View>
              );
            })
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
            Study the course topics and video lectures, then test your comprehension to receive a DMDP verified digital certificate.
            {questions.length > 0 ? ` (${questions.length} questions available)` : ''}
          </Text>

          <TouchableOpacity
            style={styles.startQuizBtn}
            activeOpacity={0.88}
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
  centerContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: SPACING.xl,
    backgroundColor: COLORS.background,
  },
  loadingText: {
    fontSize: 14,
    color: COLORS.slate500,
    marginTop: 12,
  },
  emptyIconCircle: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: COLORS.dangerBg,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: COLORS.navy,
  },
  backBtn: {
    marginTop: SPACING.md,
    backgroundColor: COLORS.primary,
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
  },
  backBtnText: {
    color: COLORS.white,
    fontWeight: '700',
    fontSize: 14,
  },
  courseHeader: {
    backgroundColor: COLORS.navy,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    borderWidth: 1.5,
    borderColor: COLORS.emerald600,
    ...SHADOWS.md,
  },
  badgeRow: {
    flexDirection: 'row',
    gap: SPACING.sm,
    marginBottom: SPACING.sm,
  },
  badge: {
    backgroundColor: 'rgba(255,255,255,0.15)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.sm,
  },
  badgeText: {
    fontSize: 11,
    color: COLORS.white,
    fontWeight: '700',
  },
  dmdpBadge: {
    backgroundColor: COLORS.primary,
    flexDirection: 'row',
    alignItems: 'center',
  },
  dmdpBadgeText: {
    fontSize: 11,
    color: COLORS.white,
    fontWeight: '700',
  },
  courseTitle: {
    fontSize: 20,
    fontWeight: '800',
    color: COLORS.white,
    marginTop: 4,
    letterSpacing: -0.3,
  },
  courseMeta: {
    fontSize: 12,
    color: COLORS.slate300,
    marginTop: 6,
  },
  section: {
    marginTop: SPACING.sm,
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.navy,
  },
  sectionSubtitle: {
    fontSize: 12,
    color: COLORS.slate500,
    fontWeight: '600',
  },
  topicCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    marginBottom: SPACING.sm,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    ...SHADOWS.sm,
  },
  topicMainRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  topicNumber: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: COLORS.primaryUltraLight,
    alignItems: 'center',
    justifyContent: 'center',
  },
  topicNumberText: {
    fontSize: 13,
    fontWeight: '800',
    color: COLORS.primary,
  },
  topicTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.slate900,
  },
  videoBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: 4,
  },
  videoBadgeText: {
    fontSize: 11,
    color: COLORS.emerald700,
    fontWeight: '700',
  },
  textOnlyBadge: {
    fontSize: 11,
    color: COLORS.slate400,
    marginTop: 4,
  },
  watchVideoBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: COLORS.navy,
    borderRadius: BORDER_RADIUS.md,
    paddingVertical: 10,
    marginTop: SPACING.sm,
  },
  watchVideoBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.white,
  },
  emptyTopicText: {
    fontSize: 13,
    color: COLORS.slate400,
    fontStyle: 'italic',
    paddingVertical: SPACING.md,
  },
  assessmentCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.slate200,
    marginTop: SPACING.sm,
    ...SHADOWS.md,
  },
  assessmentIconBox: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: COLORS.primaryUltraLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.md,
  },
  assessmentTitle: {
    fontSize: 17,
    fontWeight: '800',
    color: COLORS.navy,
    textAlign: 'center',
  },
  assessmentDesc: {
    fontSize: 13,
    color: COLORS.slate600,
    textAlign: 'center',
    marginTop: 6,
    lineHeight: 18,
    marginBottom: SPACING.lg,
  },
  startQuizBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    backgroundColor: COLORS.primary,
    paddingVertical: 14,
    paddingHorizontal: 24,
    borderRadius: BORDER_RADIUS.lg,
    width: '100%',
    ...SHADOWS.sm,
  },
  startQuizBtnText: {
    fontSize: 15,
    fontWeight: '800',
    color: COLORS.white,
  },

  // Quiz Mode Styles
  quizHeader: {
    marginBottom: SPACING.md,
  },
  quizHeaderTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  quizProgressText: {
    fontSize: 13,
    fontWeight: '800',
    color: COLORS.navy,
  },
  answeredCountText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.slate500,
  },
  progressBarBg: {
    height: 6,
    backgroundColor: COLORS.slate200,
    borderRadius: 3,
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    backgroundColor: COLORS.primary,
    borderRadius: 3,
  },
  questionCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.lg,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    ...SHADOWS.md,
  },
  qBadgeRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  qNumBadge: {
    backgroundColor: COLORS.primaryUltraLight,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: BORDER_RADIUS.full,
  },
  qNumBadgeText: {
    fontSize: 11,
    fontWeight: '800',
    color: COLORS.primaryDark,
  },
  passingNote: {
    fontSize: 11,
    color: COLORS.slate400,
    fontWeight: '600',
  },
  questionText: {
    fontSize: 16,
    fontWeight: '700',
    color: COLORS.slate900,
    lineHeight: 22,
    marginBottom: SPACING.lg,
  },
  choicesList: {
    gap: 10,
  },
  choiceItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: BORDER_RADIUS.lg,
    backgroundColor: COLORS.slate50,
    borderWidth: 1.5,
    borderColor: COLORS.slate200,
    gap: 12,
  },
  choiceItemSelected: {
    backgroundColor: COLORS.primaryUltraLight,
    borderColor: COLORS.primary,
  },
  choiceBullet: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: COLORS.slate200,
    alignItems: 'center',
    justifyContent: 'center',
  },
  choiceBulletSelected: {
    backgroundColor: COLORS.primary,
  },
  choiceLetter: {
    fontSize: 12,
    fontWeight: '800',
    color: COLORS.slate700,
  },
  choiceLetterSelected: {
    color: COLORS.white,
  },
  choiceText: {
    flex: 1,
    fontSize: 14,
    color: COLORS.slate800,
    fontWeight: '500',
  },
  choiceTextSelected: {
    color: COLORS.primaryDark,
    fontWeight: '700',
  },
  quizNavRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 12,
    marginTop: SPACING.md,
  },
  cancelQuizBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    paddingHorizontal: 14,
    paddingVertical: 10,
  },
  cancelQuizBtnText: {
    fontSize: 13,
    color: COLORS.slate500,
    fontWeight: '600',
  },
  navBtnPrev: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.slate100,
  },
  navBtnPrevText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.navy,
  },
  navBtnNext: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: 20,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.navy,
  },
  navBtnNextText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.white,
  },
  navBtnSubmit: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: 20,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
    backgroundColor: COLORS.primary,
  },
  navBtnSubmitText: {
    fontSize: 13,
    fontWeight: '800',
    color: COLORS.white,
  },

  // Quiz Result Styles
  resultCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.xl,
    padding: SPACING.xl,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.slate200,
    ...SHADOWS.md,
  },
  resultIconBox: {
    width: 88,
    height: 88,
    borderRadius: 44,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.md,
  },
  resultTitle: {
    fontSize: 18,
    fontWeight: '800',
    color: COLORS.navy,
    textAlign: 'center',
  },
  scoreBadge: {
    backgroundColor: COLORS.slate100,
    paddingHorizontal: 14,
    paddingVertical: 6,
    borderRadius: BORDER_RADIUS.full,
    marginVertical: 10,
  },
  resultScore: {
    fontSize: 15,
    fontWeight: '800',
    color: COLORS.navy,
  },
  resultDesc: {
    fontSize: 13,
    color: COLORS.slate600,
    textAlign: 'center',
    lineHeight: 18,
    marginBottom: SPACING.md,
  },
  certificateBanner: {
    backgroundColor: COLORS.emerald50,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    width: '100%',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: COLORS.emerald200,
    marginBottom: SPACING.md,
  },
  certBadgeHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  certBadgeHeaderText: {
    fontSize: 11,
    fontWeight: '800',
    color: COLORS.emerald800,
    letterSpacing: 0.5,
  },
  certBadgeNo: {
    fontSize: 16,
    fontWeight: '900',
    color: COLORS.navy,
    marginTop: 4,
  },
  certBadgeCourse: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.slate700,
    marginTop: 2,
    textAlign: 'center',
  },
  certBadgeSub: {
    fontSize: 11,
    color: COLORS.slate500,
    marginTop: 4,
    textAlign: 'center',
  },
  viewVaultBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    backgroundColor: COLORS.primary,
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: BORDER_RADIUS.md,
    marginTop: 10,
  },
  viewVaultBtnText: {
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.white,
  },
  resultActionRow: {
    width: '100%',
    gap: 10,
    marginTop: 6,
  },
  toggleReviewBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: COLORS.slate100,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
  },
  toggleReviewBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.navy,
  },
  retakeBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    backgroundColor: COLORS.navy,
    paddingVertical: 12,
    borderRadius: BORDER_RADIUS.md,
  },
  retakeBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.white,
  },
  doneBtn: {
    paddingVertical: 12,
    alignItems: 'center',
    justifyContent: 'center',
  },
  doneBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.slate500,
  },

  // Review Breakdown Styles
  reviewSection: {
    marginTop: SPACING.md,
  },
  reviewSectionTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.navy,
  },
  reviewSectionSub: {
    fontSize: 12,
    color: COLORS.slate500,
    marginBottom: SPACING.md,
  },
  reviewCard: {
    backgroundColor: COLORS.white,
    borderRadius: BORDER_RADIUS.lg,
    padding: SPACING.md,
    marginBottom: SPACING.md,
    borderWidth: 1.5,
    ...SHADOWS.sm,
  },
  reviewCardCorrect: {
    borderColor: COLORS.emerald200,
  },
  reviewCardIncorrect: {
    borderColor: '#FECDD3',
  },
  reviewCardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  qNumberPill: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: BORDER_RADIUS.sm,
  },
  qPillCorrect: {
    backgroundColor: COLORS.emerald50,
  },
  qPillIncorrect: {
    backgroundColor: COLORS.dangerBg,
  },
  qNumberText: {
    fontSize: 11,
    fontWeight: '800',
  },
  qTextCorrect: {
    color: COLORS.emerald800,
  },
  qTextIncorrect: {
    color: COLORS.danger,
  },
  reviewStatusTag: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  reviewStatusText: {
    fontSize: 11,
    fontWeight: '700',
  },
  reviewQuestionText: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.slate900,
    lineHeight: 20,
    marginBottom: 10,
  },
  reviewChoicesList: {
    gap: 6,
  },
  reviewChoiceItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 8,
    borderRadius: BORDER_RADIUS.sm,
    backgroundColor: COLORS.slate50,
    borderWidth: 1,
    borderColor: COLORS.slate200,
    gap: 8,
  },
  reviewChoiceCorrect: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 8,
    borderRadius: BORDER_RADIUS.sm,
    backgroundColor: COLORS.emerald50,
    borderWidth: 1,
    borderColor: COLORS.emerald300,
    gap: 8,
  },
  reviewChoiceWrong: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 8,
    borderRadius: BORDER_RADIUS.sm,
    backgroundColor: COLORS.dangerBg,
    borderWidth: 1,
    borderColor: '#FCA5A5',
    gap: 8,
  },
  reviewChoiceText: {
    flex: 1,
    fontSize: 12,
    color: COLORS.slate700,
  },
  reviewChoiceTextCorrect: {
    flex: 1,
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.emerald800,
  },
  reviewChoiceTextWrong: {
    flex: 1,
    fontSize: 12,
    fontWeight: '700',
    color: COLORS.danger,
  },
});

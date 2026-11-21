import { apiClient } from './apiClient';
import { TrainingProgramItem, CertificateItem } from '../types';

export interface QuizSubmission {
  responses: Record<string, string | number>;
}

export interface GradedQuizResponse {
  assessment_id: number;
  question: string;
  question_type: string;
  response: string;
  options: string[];
  correct_answer: string | null;
  is_correct: boolean | null;
  selected_index?: number;
  points: number;
  awarded_points: number | null;
  explanation?: string;
}

export interface QuizSubmissionResult {
  success: boolean;
  message?: string;
  review_status?: 'pending' | 'passed' | 'needs_retake' | string;
  objective_score?: {
    correct_count: number;
    question_count: number;
    earned_points: number;
    total_points: number;
    percentage: number | null;
  };
  assessment_attempts?: number;
  retakes?: number;
  passed?: boolean;
  certificate_issued?: boolean;
  responses?: GradedQuizResponse[];
}

export const trainingService = {
  async getTrainings(filter?: 'all' | 'enrolled' | 'completed'): Promise<TrainingProgramItem[]> {
    try {
      const response = await apiClient.get('/training', { params: { filter } });
      if (Array.isArray(response.data)) {
        return response.data;
      }
      if (response.data && Array.isArray(response.data.data)) {
        return response.data.data;
      }
      return [];
    } catch (err: any) {
      console.warn('Error fetching training programs:', err.message);
      return [];
    }
  },

  async getTrainingById(id: number | string): Promise<TrainingProgramItem | null> {
    try {
      const response = await apiClient.get(`/training/${id}`);
      if (response.data && response.data.data) {
        return response.data.data;
      }
      return response.data || null;
    } catch (err: any) {
      console.warn(`Error fetching training ${id}:`, err.message);
      return null;
    }
  },

  async enroll(trainingId: number | string): Promise<{ success: boolean; message?: string }> {
    try {
      const response = await apiClient.post(`/training/${trainingId}/enroll`);
      return {
        success: true,
        message: response.data?.message || 'Enrolled in training successfully!',
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to enroll in training course.',
      };
    }
  },

  async submitQuiz(
    trainingId: number | string,
    submission: QuizSubmission
  ): Promise<QuizSubmissionResult> {
    try {
      const response = await apiClient.post(`/training/${trainingId}/quiz-result`, submission);
      return { success: true, ...response.data };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to submit quiz.',
      };
    }
  },

  async getCertificates(): Promise<CertificateItem[]> {
    try {
      const response = await apiClient.get('/training/certificates');
      if (Array.isArray(response.data)) {
        return response.data;
      }
      return [];
    } catch (err: any) {
      console.warn('Error fetching certificates:', err.message);
      return [];
    }
  },
};

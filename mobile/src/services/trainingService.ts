import { apiClient } from './apiClient';
import { TrainingProgramItem, CertificateItem } from '../types';

export interface QuizSubmission {
  score: number;
  total: number;
  answers: Record<string | number, number>;
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
  ): Promise<{ success: boolean; message?: string; passed?: boolean; score?: number; certificate_no?: string | null }> {
    try {
      const response = await apiClient.post(`/training/${trainingId}/quiz-result`, submission);
      return {
        success: true,
        message: response.data?.message || 'Quiz submitted successfully!',
        passed: response.data?.passed,
        score: response.data?.score,
        certificate_no: response.data?.certificate_no,
      };
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

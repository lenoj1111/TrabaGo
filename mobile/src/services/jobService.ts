import { apiClient } from './apiClient';
import { JobItem, DashboardData } from '../types';

export interface JobsFilterParams {
  q?: string;
  query?: string;
  location?: string;
  filter?: string;
  sort?: 'match' | 'latest';
  pwd_only?: boolean;
}

export const jobService = {
  async getDashboard(): Promise<DashboardData | null> {
    try {
      const response = await apiClient.get('/dashboard');
      return response.data;
    } catch (err: any) {
      console.warn('Error fetching dashboard:', err.message);
      return null;
    }
  },

  async getJobs(params?: JobsFilterParams): Promise<JobItem[]> {
    try {
      const response = await apiClient.get('/jobs', { params });
      if (Array.isArray(response.data)) {
        return response.data;
      }
      if (response.data && Array.isArray(response.data.data)) {
        return response.data.data;
      }
      return [];
    } catch (err: any) {
      console.warn('Error fetching jobs:', err.message);
      return [];
    }
  },

  async getJobById(id: number | string): Promise<JobItem | null> {
    try {
      const response = await apiClient.get(`/jobs/${id}`);
      if (response.data && response.data.data) {
        return response.data.data;
      }
      return response.data || null;
    } catch (err: any) {
      console.warn(`Error fetching job ${id}:`, err.message);
      return null;
    }
  },
};

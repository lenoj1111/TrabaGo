import { apiClient } from './apiClient';
import { JobApplicationItem, ApplicationCounts } from '../types';

export interface SubmitApplicationPayload {
  jobId: number | string;
  coverNote?: string;
  resumePath?: string;
}

export interface ApplicationsResponse {
  applications: JobApplicationItem[];
  counts: ApplicationCounts;
  is_employed: boolean;
  hired_company?: string | null;
}

export const applicationService = {
  async getApplications(status?: string): Promise<ApplicationsResponse> {
    try {
      const response = await apiClient.get('/applications', { params: { status } });
      const data = response.data;
      const apps = Array.isArray(data) 
        ? data 
        : (Array.isArray(data.data) ? data.data : (Array.isArray(data.applications) ? data.applications : []));

      const counts: ApplicationCounts = data.counts || {
        all: apps.length,
        offered: 0,
        pending: 0,
        reviewed: 0,
        interview: 0,
        hired: 0,
        rejected: 0,
      };

      return {
        applications: apps,
        counts,
        is_employed: !!data.is_employed,
        hired_company: data.hired_company || null,
      };
    } catch (err: any) {
      console.warn('Error fetching applications:', err.message);
      return {
        applications: [],
        counts: { all: 0, offered: 0, pending: 0, reviewed: 0, interview: 0, hired: 0, rejected: 0 },
        is_employed: false,
      };
    }
  },

  async apply(payload: SubmitApplicationPayload): Promise<{ success: boolean; message?: string; data?: any }> {
    try {
      const response = await apiClient.post('/applications', {
        jobId: payload.jobId,
        job_id: payload.jobId,
        cover_note: payload.coverNote,
        resume_path: payload.resumePath,
      });
      return {
        success: true,
        message: response.data?.message || 'Application submitted successfully!',
        data: response.data?.data || response.data,
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to submit application.',
      };
    }
  },

  async acceptOffer(applicationId: number | string): Promise<{ success: boolean; message?: string; hired_company?: string }> {
    try {
      const response = await apiClient.post(`/applications/${applicationId}/accept-offer`);
      return {
        success: true,
        message: response.data?.message || 'Job offer accepted! Congratulations!',
        hired_company: response.data?.hired_company,
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to accept job offer.',
      };
    }
  },

  async declineOffer(applicationId: number | string, reason?: string): Promise<{ success: boolean; message?: string }> {
    try {
      const response = await apiClient.post(`/applications/${applicationId}/decline-offer`, {
        reason,
        decline_reason: reason,
      });
      return {
        success: true,
        message: response.data?.message || 'Job offer declined.',
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to decline job offer.',
      };
    }
  },

  async requestResignation(reason: string): Promise<{ success: boolean; message?: string }> {
    try {
      const response = await apiClient.post('/applications/request-resignation', { reason });
      return {
        success: true,
        message: response.data?.message || 'Resignation request submitted to employer.',
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to submit resignation request.',
      };
    }
  },

  async withdraw(applicationId: number | string): Promise<{ success: boolean; message?: string }> {
    try {
      const response = await apiClient.delete(`/applications/${applicationId}`);
      return {
        success: true,
        message: response.data?.message || 'Application withdrawn.',
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to withdraw application.',
      };
    }
  },
};

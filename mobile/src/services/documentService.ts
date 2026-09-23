import { apiClient } from './apiClient';
import { Platform } from 'react-native';
import { VaultDocument } from '../types';

export interface DocumentUploadResult {
  success: boolean;
  filePath?: string;
  url?: string;
  message?: string;
  id?: string;
}

export const documentService = {
  async getAll(): Promise<{ documents: VaultDocument[]; categories: Record<string, VaultDocument | null> }> {
    try {
      const response = await apiClient.get('/documents');
      return {
        documents: response.data.documents || [],
        categories: response.data.categories || {},
      };
    } catch (err: any) {
      console.warn('Error fetching documents:', err.message);
      return { documents: [], categories: {} };
    }
  },

  async uploadDocument(
    uri: string,
    fileName: string,
    mimeType: string,
    category: 'resume' | 'valid_id' | 'certificate' | 'pwd_id' = 'resume'
  ): Promise<DocumentUploadResult> {
    try {
      const formData = new FormData();

      if (Platform.OS === 'web') {
        const res = await fetch(uri);
        const blob = await res.blob();
        formData.append('file', blob, fileName);
      } else {
        formData.append('file', {
          uri,
          name: fileName,
          type: mimeType || 'application/pdf',
        } as any);
      }

      formData.append('category', category);

      const response = await apiClient.post('/documents/upload', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      return {
        success: true,
        id: response.data.id,
        filePath: response.data.file_url || response.data.fileUrl,
        url: response.data.url || response.data.file_url,
        message: response.data.message || 'Document uploaded successfully to vault.',
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Document upload failed.',
      };
    }
  },

  async deleteDocument(id: string): Promise<{ success: boolean; message?: string }> {
    try {
      const response = await apiClient.delete(`/documents/${id}`);
      return {
        success: true,
        message: response.data?.message || 'Document removed from vault.',
      };
    } catch (err: any) {
      return {
        success: false,
        message: err.response?.data?.message || 'Failed to remove document.',
      };
    }
  },
};

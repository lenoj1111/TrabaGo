import React, { createContext, useContext, useState, useEffect } from 'react';
import { UserProfile } from '../types';
import { authService, RegisterData } from '../services/authService';

interface AuthContextType {
  user: UserProfile | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (email: string, password: string) => Promise<{ success: boolean; message?: string }>;
  register: (data: RegisterData) => Promise<{ success: boolean; message?: string }>;
  logout: () => Promise<void>;
  refreshProfile: () => Promise<void>;
  updateProfile: (updates: Partial<UserProfile>) => Promise<{ success: boolean; message?: string }>;
  addSkill: (skillName: string) => Promise<{ success: boolean; message?: string }>;
  removeSkill: (idOrName: string | number) => Promise<{ success: boolean; message?: string }>;
  changePassword: (current: string, newPass: string, confirmPass: string) => Promise<{ success: boolean; message?: string }>;
}

const AuthContext = createContext<AuthContextType>({
  user: null,
  token: null,
  isAuthenticated: false,
  isLoading: true,
  login: async () => ({ success: false }),
  register: async () => ({ success: false }),
  logout: async () => {},
  refreshProfile: async () => {},
  updateProfile: async () => ({ success: false }),
  addSkill: async () => ({ success: false }),
  removeSkill: async () => ({ success: false }),
  changePassword: async () => ({ success: false }),
});

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<UserProfile | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  // Load stored credentials on mount
  useEffect(() => {
    let isMounted = true;

    const loadSession = async () => {
      try {
        const storedToken = await authService.getStoredToken();
        const storedUser = await authService.getStoredUser();
        if (storedToken && isMounted) {
          setToken(storedToken);
          if (storedUser) {
            setUser(storedUser);
          }
          // Verify with server in background
          const res = await authService.getProfile();
          if (res.success && res.user && isMounted) {
            setUser(res.user);
          }
        }
      } catch (err) {
        console.warn('Failed to load session:', err);
      } finally {
        if (isMounted) {
          setIsLoading(false);
        }
      }
    };

    loadSession();

    return () => {
      isMounted = false;
    };
  }, []);

  const login = async (email: string, password: string) => {
    setIsLoading(true);
    try {
      const res = await authService.login(email, password);
      if (res.success && res.token && res.user) {
        setToken(res.token);
        setUser(res.user);
        return { success: true };
      }
      return { success: false, message: res.message || 'Invalid credentials' };
    } finally {
      setIsLoading(false);
    }
  };

  const register = async (data: RegisterData) => {
    setIsLoading(true);
    try {
      const res = await authService.register(data);
      if (res.success && res.token && res.user) {
        setToken(res.token);
        setUser(res.user);
        return { success: true };
      }
      return { success: false, message: res.message || 'Registration failed' };
    } finally {
      setIsLoading(false);
    }
  };

  const logout = async () => {
    setIsLoading(true);
    try {
      await authService.logout();
      setToken(null);
      setUser(null);
    } finally {
      setIsLoading(false);
    }
  };

  const refreshProfile = async () => {
    const res = await authService.getProfile();
    if (res.success && res.user) {
      setUser(res.user);
    }
  };

  const updateProfile = async (updates: Partial<UserProfile>) => {
    const res = await authService.updateProfile(updates);
    if (res.success && res.user) {
      setUser(res.user);
      return { success: true };
    }
    return { success: false, message: res.message || 'Failed to update profile' };
  };

  const addSkill = async (skillName: string) => {
    const res = await authService.addSkill(skillName);
    if (res.success && res.skills && user) {
      setUser({ ...user, skills: res.skills });
      return { success: true, message: res.message };
    }
    return { success: false, message: res.message || 'Failed to add skill' };
  };

  const removeSkill = async (idOrName: string | number) => {
    const res = await authService.removeSkill(idOrName);
    if (res.success && res.skills && user) {
      setUser({ ...user, skills: res.skills });
      return { success: true, message: res.message };
    }
    return { success: false, message: res.message || 'Failed to remove skill' };
  };

  const changePassword = async (current: string, newPass: string, confirmPass: string) => {
    return await authService.changePassword(current, newPass, confirmPass);
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        token,
        isAuthenticated: !!token && !!user,
        isLoading,
        login,
        register,
        logout,
        refreshProfile,
        updateProfile,
        addSkill,
        removeSkill,
        changePassword,
      }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);

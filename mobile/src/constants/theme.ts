export const COLORS = {
  // Primary brand palette (TrabaGo & Cebu DMDP - Emerald)
  primary: '#059669',        // Emerald 600
  primaryDark: '#047857',    // Emerald 700
  primaryLight: '#10b981',   // Emerald 500
  primaryUltraLight: '#d1fae5', // Emerald 100
  primaryBorder: '#a7f3d0',     // Emerald 200
  emerald50: '#ecfdf5',
  emerald100: '#d1fae5',
  emerald200: '#a7f3d0',
  emerald300: '#6ee7b7',
  emerald400: '#34d399',
  emerald500: '#10b981',
  emerald600: '#059669',
  emerald700: '#047857',
  emerald800: '#065f46',
  emerald900: '#064e3b',

  // Accents & Neutrals (Tailwind Slate)
  navy: '#0f172a',           // Slate 900
  navyMuted: '#1e293b',      // Slate 800
  slate50: '#f8fafc',
  slate100: '#f1f5f9',
  slate200: '#e2e8f0',
  slate300: '#cbd5e1',
  slate400: '#94a3b8',
  slate500: '#64748b',
  slate600: '#475569',
  slate700: '#334155',
  slate800: '#1e293b',
  slate900: '#0f172a',
  slate950: '#020617',

  // Text colors
  text: '#0f172a',
  textSecondary: '#475569',
  textMuted: '#94a3b8',
  white: '#ffffff',
  
  // Backgrounds & Surface
  background: '#f8fafc',     // Slate 50
  surface: '#ffffff',
  surfaceSubtle: '#f1f5f9',
  border: '#e2e8f0',
  borderLight: '#f1f5f9',

  // Status indicators (Semantic tokens)
  success: '#10b981',
  successBg: '#ecfdf5',
  successBorder: '#a7f3d0',
  warning: '#f59e0b',
  warningBg: '#fffbeb',
  warningBorder: '#fde68a',
  danger: '#ef4444',
  dangerBg: '#fef2f2',
  dangerBorder: '#fecaca',
  info: '#3b82f6',
  infoBg: '#eff6ff',
  infoBorder: '#bfdbfe',
  purple: '#8b5cf6',
  purpleBg: '#f5f3ff',
  purpleBorder: '#ddd6fe',
  amber: '#d97706',
  amberDark: '#b45309',
  amberBg: '#fef3c7',
};

export const SPACING = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  xxl: 24,
  xxxl: 32,
};

export const BORDER_RADIUS = {
  xs: 4,
  sm: 6,
  md: 10,
  lg: 14,
  xl: 18,
  xxl: 24,
  full: 9999,
};

export const SHADOWS = {
  none: {
    shadowColor: 'transparent',
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0,
    shadowRadius: 0,
    elevation: 0,
  },
  sm: {
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  md: {
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.08,
    shadowRadius: 6,
    elevation: 3,
  },
  lg: {
    shadowColor: '#0f172a',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.12,
    shadowRadius: 12,
    elevation: 6,
  },
};

export const TYPOGRAPHY = {
  display: {
    fontSize: 28,
    fontWeight: '800' as const,
    color: COLORS.navy,
    letterSpacing: -0.5,
  },
  titleLarge: {
    fontSize: 22,
    fontWeight: '700' as const,
    color: COLORS.navy,
    letterSpacing: -0.3,
  },
  titleMedium: {
    fontSize: 18,
    fontWeight: '700' as const,
    color: COLORS.navy,
  },
  titleSmall: {
    fontSize: 15,
    fontWeight: '600' as const,
    color: COLORS.navy,
  },
  body: {
    fontSize: 14,
    fontWeight: '400' as const,
    color: COLORS.textSecondary,
    lineHeight: 20,
  },
  bodyBold: {
    fontSize: 14,
    fontWeight: '600' as const,
    color: COLORS.navy,
  },
  caption: {
    fontSize: 12,
    fontWeight: '500' as const,
    color: COLORS.textMuted,
  },
  badge: {
    fontSize: 11,
    fontWeight: '600' as const,
  },
};

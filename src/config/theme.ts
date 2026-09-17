import type { ThemeConfig } from 'antd';
import { theme as antdTheme } from 'antd';

export type SLV_Density = 'professional' | 'compact' | 'comfortable' | 'dark';

export const SLV_THEMES: Record<SLV_Density, ThemeConfig> = {
    professional: {
        algorithm: antdTheme.defaultAlgorithm,
        token: { colorPrimary: '#0066ff', borderRadius: 8, fontSize: 14, controlHeight: 36 },
    },
    compact: {
        algorithm: antdTheme.compactAlgorithm,
        token: { colorPrimary: '#0066ff', borderRadius: 6, fontSize: 13, controlHeight: 30, paddingContentVertical: 8 },
    },
    comfortable: {
        algorithm: antdTheme.defaultAlgorithm,
        token: { colorPrimary: '#0066ff', borderRadius: 10, fontSize: 15, controlHeight: 42, paddingContentVertical: 16 },
    },
    dark: {
        algorithm: antdTheme.darkAlgorithm,
        token: { colorPrimary: '#3385ff', borderRadius: 8, fontSize: 14, controlHeight: 36 },
    },
};

export const SLV_DENSITY_LABELS: Record<SLV_Density, string> = {
    professional: '专业（默认）',
    compact: '紧凑',
    comfortable: '舒适',
    dark: '深色',
};
import { useState, useEffect } from 'react';
import type { SLV_Density } from '@/config/theme';

const STORAGE_KEY = 'slv_density';

export function useDensity(): [SLV_Density, (d: SLV_Density) => void] {
    const [density, setDensity] = useState<SLV_Density>(() => {
        try {
            return (localStorage.getItem(STORAGE_KEY) as SLV_Density) || 'professional';
        } catch {
            return 'professional';
        }
    });

    useEffect(() => {
        try {
            localStorage.setItem(STORAGE_KEY, density);
        } catch {}
    }, [density]);

    return [density, setDensity];
}
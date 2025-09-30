import { useCallback, useEffect, useMemo, useState } from 'react';

export type ViewMode = 'table' | 'grid';

const STORAGE_KEY = 'view-mode';

const setCookie = (name: string, value: string, days = 365) => {
  if (typeof document === 'undefined') return;
  const maxAge = days * 24 * 60 * 60;
  document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getInitialMode = (fallback: ViewMode): ViewMode => {
  if (typeof window === 'undefined') return fallback;
  const saved = localStorage.getItem(STORAGE_KEY) as ViewMode | null;
  return saved === 'grid' || saved === 'table' ? saved : fallback;
};

export function initializeViewMode(defaultMode: ViewMode = 'table') {
  const initial = getInitialMode(defaultMode);
  // Persist for SSR downstream usage
  if (typeof document !== 'undefined') {
    setCookie(STORAGE_KEY, initial);
  }
}

export function useViewMode(defaultMode: ViewMode = 'table') {
  const [mode, setMode] = useState<ViewMode>(() => getInitialMode(defaultMode));

  const update = useCallback((next: ViewMode) => {
    setMode(next);
    if (typeof window !== 'undefined') {
      localStorage.setItem(STORAGE_KEY, next);
    }
    setCookie(STORAGE_KEY, next);
  }, []);

  const toggle = useCallback(() => {
    update(mode === 'table' ? 'grid' : 'table');
  }, [mode, update]);

  useEffect(() => {
    // In case defaultMode changes across navigations
    const saved = getInitialMode(defaultMode);
    if (saved !== mode) setMode(saved);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [defaultMode]);

  const flags = useMemo(
    () => ({
      isTable: mode === 'table',
      isGrid: mode === 'grid',
    }),
    [mode],
  );

  return { mode, setMode: update, toggle, ...flags } as const;
}

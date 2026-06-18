import { create } from 'zustand';
import apiClient from '../api/client';

interface AuthState {
  token: string | null;
  isAuthenticated: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
}

/**
 * Store global de autenticación.
 *
 * El token se guarda en localStorage (lo lee el interceptor de
 * apiClient en cada request) y también aquí en memoria, para que
 * los componentes puedan reaccionar a isAuthenticated sin tener
 * que leer localStorage directamente.
 */
export const useAuthStore = create<AuthState>((set) => ({
  token: localStorage.getItem('access_token'),
  isAuthenticated: !!localStorage.getItem('access_token'),

  login: async (email: string, password: string) => {
    const { data } = await apiClient.post('/auth/login', { email, password });
    localStorage.setItem('access_token', data.access_token);
    set({ token: data.access_token, isAuthenticated: true });
  },

  logout: () => {
    localStorage.removeItem('access_token');
    set({ token: null, isAuthenticated: false });
  },
}));

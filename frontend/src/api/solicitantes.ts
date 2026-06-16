import apiClient from './client';
import type { ApiCollection, ApiResource, Solicitante } from '../types';

/**
 * Funciones para consumir los endpoints de /api/solicitantes.
 *
 * GET son públicos (no requieren token); POST/PUT/DELETE necesitan
 * el token JWT, que el interceptor de apiClient añade automáticamente.
 */
export const solicitantesApi = {
  listar: async (): Promise<Solicitante[]> => {
    const { data } = await apiClient.get<ApiCollection<Solicitante>>('/solicitantes');
    return data.data;
  },

  buscar: async (id: string): Promise<Solicitante> => {
    const { data } = await apiClient.get<ApiResource<Solicitante>>(`/solicitantes/${id}`);
    return data.data;
  },

  crear: async (payload: Omit<Solicitante, 'id' | 'nombre_completo'>): Promise<Solicitante> => {
    const { data } = await apiClient.post<ApiResource<Solicitante>>('/solicitantes', payload);
    return data.data;
  },

  actualizar: async (id: string, payload: Partial<Solicitante>): Promise<Solicitante> => {
    const { data } = await apiClient.put<ApiResource<Solicitante>>(`/solicitantes/${id}`, payload);
    return data.data;
  },

  eliminar: async (id: string): Promise<void> => {
    await apiClient.delete(`/solicitantes/${id}`);
  },
};

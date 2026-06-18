import apiClient from './client';
import type { ApiCollection, ApiResource, Solicitud } from '../types';

export const solicitudesApi = {
  listar: async (): Promise<Solicitud[]> => {
    const { data } = await apiClient.get<ApiCollection<Solicitud>>('/solicitudes');
    return data.data;
  },

  buscar: async (id: string): Promise<Solicitud> => {
    const { data } = await apiClient.get<ApiResource<Solicitud>>(`/solicitudes/${id}`);
    return data.data;
  },

  crear: async (payload: Omit<Solicitud, 'id' | 'tipo_ayuda_label' | 'estado_label'>): Promise<Solicitud> => {
    const { data } = await apiClient.post<ApiResource<Solicitud>>('/solicitudes', payload);
    return data.data;
  },

  actualizar: async (id: string, payload: Partial<Solicitud>): Promise<Solicitud> => {
    const { data } = await apiClient.put<ApiResource<Solicitud>>(`/solicitudes/${id}`, payload);
    return data.data;
  },

  eliminar: async (id: string): Promise<void> => {
    await apiClient.delete(`/solicitudes/${id}`);
  },
};

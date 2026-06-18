/**
 * Tipos que reflejan la forma de los datos devueltos por la API
 * (los API Resources de Laravel: SolicitanteResource y SolicitudResource).
 */

export interface Solicitante {
  id: string;
  nombre: string;
  apellidos: string;
  nombre_completo: string;
  email: string;
  telefono: string;
  comunidad_autonoma: string;
  fecha_registro: string;
}

export type TipoAyuda =
  | 'Alquiler'
  | 'ChequeBebe'
  | 'IngresoMinimoVital'
  | 'BonoCulturalJoven';

export type Estado = 'Pendiente' | 'EnRevision' | 'Concedida' | 'Denegada';

export interface Solicitud {
  id: string;
  solicitante_id: string;
  tipo_ayuda: TipoAyuda;
  tipo_ayuda_label: string;
  fecha_solicitud: string;
  fecha_resolucion: string | null;
  importe_estimado: number;
  estado: Estado;
  estado_label: string;
}

export interface ApiCollection<T> {
  data: T[];
}

export interface ApiResource<T> {
  data: T;
}

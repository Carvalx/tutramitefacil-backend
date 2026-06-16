import axios from 'axios';

/**
 * Cliente Axios configurado contra la API de TuTramiteFacil.
 *
 * baseURL apunta al backend Laravel servido por Nginx en el puerto 8010
 * (definido en docker-compose.yml).
 */
const apiClient = axios.create({
  baseURL: 'http://localhost:8010/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

/**
 * Interceptor de request: añade automáticamente el header
 * 'Authorization: Bearer <token>' a TODAS las peticiones, si hay
 * un token JWT guardado (lo guardaremos en el store de Zustand
 * tras el login).
 *
 * Así no tenemos que añadir el header manualmente en cada llamada
 * a un endpoint protegido (POST/PUT/DELETE).
 */
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export default apiClient;

import { createBrowserRouter } from 'react-router-dom';
import SolicitantesListPage from '../pages/SolicitantesListPage';
import SolicitanteDetailPage from '../pages/SolicitanteDetailPage';
import SolicitanteFormPage from '../pages/SolicitanteFormPage';
import LoginPage from '../pages/LoginPage';

/**
 * Definición de rutas de la SPA.
 *
 * '/'                        -> Listado de solicitantes
 * '/login'                   -> Inicio de sesión (JWT)
 * '/solicitantes/nuevo'      -> Crear solicitante
 * '/solicitantes/:id'        -> Detalle de un solicitante + sus solicitudes
 * '/solicitantes/:id/editar' -> Editar solicitante existente
 */
export const router = createBrowserRouter([
  { path: '/', element: <SolicitantesListPage /> },
  { path: '/login', element: <LoginPage /> },
  { path: '/solicitantes/nuevo', element: <SolicitanteFormPage /> },
  { path: '/solicitantes/:id', element: <SolicitanteDetailPage /> },
  { path: '/solicitantes/:id/editar', element: <SolicitanteFormPage /> },
]);

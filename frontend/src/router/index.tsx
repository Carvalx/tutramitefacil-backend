import { createBrowserRouter } from 'react-router-dom';
import SolicitantesListPage from '../pages/SolicitantesListPage';
import SolicitanteDetailPage from '../pages/SolicitanteDetailPage';

/**
 * Definición de rutas de la SPA.
 *
 * '/' -> Listado de solicitantes
 * '/solicitantes/:id' -> Detalle de un solicitante + sus solicitudes
 */
export const router = createBrowserRouter([
  {
    path: '/',
    element: <SolicitantesListPage />,
  },
  {
    path: '/solicitantes/:id',
    element: <SolicitanteDetailPage />,
  },
]);

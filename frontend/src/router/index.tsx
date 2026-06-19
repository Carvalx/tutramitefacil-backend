import { createBrowserRouter } from 'react-router-dom';
import SolicitantesListPage from '../pages/SolicitantesListPage';
import SolicitanteDetailPage from '../pages/SolicitanteDetailPage';
import SolicitanteFormPage from '../pages/SolicitanteFormPage';
import SolicitudFormPage from '../pages/SolicitudFormPage';
import LoginPage from '../pages/LoginPage';
import ProtectedRoute from '../components/common/ProtectedRoute';
import Layout from '../components/common/Layout';

const withLayout = (element: React.ReactNode) => <Layout>{element}</Layout>;

export const router = createBrowserRouter([
  { path: '/', element: withLayout(<SolicitantesListPage />) },
  { path: '/login', element: <LoginPage /> },
  {
    path: '/solicitantes/nuevo',
    element: withLayout(<ProtectedRoute><SolicitanteFormPage /></ProtectedRoute>),
  },
  { path: '/solicitantes/:id', element: withLayout(<SolicitanteDetailPage />) },
  {
    path: '/solicitantes/:id/editar',
    element: withLayout(<ProtectedRoute><SolicitanteFormPage /></ProtectedRoute>),
  },
  {
    path: '/solicitudes/nueva',
    element: withLayout(<ProtectedRoute><SolicitudFormPage /></ProtectedRoute>),
  },
  {
    path: '/solicitudes/:id/editar',
    element: withLayout(<ProtectedRoute><SolicitudFormPage /></ProtectedRoute>),
  },
]);

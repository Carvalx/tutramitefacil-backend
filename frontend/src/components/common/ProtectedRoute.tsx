import type { ReactNode } from 'react';
import { Navigate } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';

interface ProtectedRouteProps {
  children: ReactNode;
}

/**
 * Envuelve rutas que requieren JWT (crear/editar/eliminar).
 * Si el usuario no está autenticado, lo redirige a /login
 * en vez de dejarle ver un formulario que fallará al guardar.
 */
function ProtectedRoute({ children }: ProtectedRouteProps) {
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  return <>{children}</>;
}

export default ProtectedRoute;

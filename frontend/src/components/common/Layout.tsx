import type { ReactNode } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';
import Button from './Button';

interface LayoutProps {
  children: ReactNode;
}

/**
 * Layout compartido: header fijo con el logo de TuTramiteFacil
 * y el estado de sesión (login/logout), envolviendo todas las
 * paginas para evitar repetir esta cabecera en cada una.
 */
function Layout({ children }: LayoutProps) {
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const logout = useAuthStore((state) => state.logout);
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center">
        <Link to="/" className="flex items-center gap-2">
          <span className="bg-tf-teal text-white font-bold w-8 h-8 rounded-lg flex items-center justify-center text-base">
            ttf
          </span>
          <span className="font-bold text-tf-navy">
            TuTrámiteFácil
          </span>
        </Link>

        {isAuthenticated ? (
          <Button variant="secondary" className="text-sm px-3 py-1.5" onClick={handleLogout}>
            Cerrar sesión
          </Button>
        ) : (
          <Link to="/login">
            <Button variant="secondary" className="text-sm px-3 py-1.5">
              Iniciar sesión
            </Button>
          </Link>
        )}
      </header>

      <main>{children}</main>
    </div>
  );
}

export default Layout;

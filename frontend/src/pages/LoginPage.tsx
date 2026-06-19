import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../store/authStore';

const BULLETS = [
  'Centraliza todas las solicitudes de ayuda en un solo lugar',
  'Controla el estado de cada trámite en tiempo real',
  'Accede al historial completo de cada solicitante',
];

function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const login = useAuthStore((state) => state.login);
  const navigate = useNavigate();

  const handleSubmit = async () => {
    setError(null);
    setLoading(true);

    try {
      await login(email, password);
      navigate('/');
    } catch {
      setError('Email o contraseña incorrectos.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex h-screen w-screen overflow-hidden">

      {/* Panel izquierdo: identidad de marca — oculto en móvil */}
      <div className="hidden md:flex md:w-2/5 bg-tf-navy flex-col justify-center px-12">
        <div className="mb-10">
          <div className="w-14 h-14 rounded-2xl bg-tf-teal flex items-center justify-center mb-5">
            <span className="text-white font-bold text-base tracking-tight">ttf</span>
          </div>
          <h1 className="text-white font-bold text-2xl leading-tight">TuTrámiteFácil</h1>
        </div>

        <p className="text-tf-teal font-semibold text-lg leading-snug mb-10">
          Gestiona las ayudas sociales<br />de tus solicitantes
        </p>

        <ul className="space-y-5">
          {BULLETS.map((text) => (
            <li key={text} className="flex items-start gap-3">
              <span className="text-tf-teal font-bold text-base mt-0.5 shrink-0">✓</span>
              <span className="text-white/75 text-sm leading-relaxed">{text}</span>
            </li>
          ))}
        </ul>
      </div>

      {/* Panel derecho: formulario */}
      <div className="flex-1 flex items-center justify-center bg-white overflow-y-auto px-8 py-16">
        <div className="w-full max-w-sm">

          <h2 className="text-tf-navy font-bold text-3xl mb-1">Bienvenido</h2>
          <p className="text-gray-400 text-sm mb-8">Inicia sesión para continuar</p>

          <form onSubmit={(e) => { e.preventDefault(); handleSubmit(); }} className="space-y-5">
            <div>
              <label className="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                Email
              </label>
              <input
                type="email"
                placeholder="tu@email.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-tf-teal focus:outline-none text-tf-navy placeholder:text-gray-300 transition-colors"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">
                Contraseña
              </label>
              <input
                type="password"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
                className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-tf-teal focus:outline-none text-tf-navy placeholder:text-gray-300 transition-colors"
              />
            </div>

            {error && (
              <p className="text-red-500 text-sm">{error}</p>
            )}

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-tf-teal text-tf-navy font-bold py-3 rounded-lg hover:bg-tf-teal-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              {loading ? 'Entrando...' : <><span>Iniciar sesión</span><span>→</span></>}
            </button>
          </form>

          <p className="text-center text-xs text-gray-400 mt-8">
            Demo:{' '}
            <span className="text-gray-500 font-medium">demo@tutramitefacil.com</span>
            {' / '}
            <span className="text-gray-500 font-medium">demo1234</span>
          </p>

        </div>
      </div>

    </div>
  );
}

export default LoginPage;

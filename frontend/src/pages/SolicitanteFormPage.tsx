import { useEffect, useState, type FormEvent } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { solicitantesApi } from '../api/solicitantes';
import Button from '../components/common/Button';
import Card from '../components/common/Card';

const COMUNIDADES = [
  'Andalucía', 'Aragón', 'Asturias', 'Baleares', 'Canarias',
  'Cantabria', 'Castilla-La Mancha', 'Castilla y León',
  'Cataluña', 'Comunidad Valenciana', 'Extremadura', 'Galicia',
  'Madrid', 'Murcia', 'Navarra', 'País Vasco', 'La Rioja',
];

interface AxiosErrorShape {
  response?: {
    status?: number;
    data?: { message?: string };
  };
}

function SolicitanteFormPage() {
  const { id } = useParams<{ id: string }>();
  const isEditing = !!id;
  const navigate = useNavigate();

  const [nombre, setNombre] = useState('');
  const [apellidos, setApellidos] = useState('');
  const [email, setEmail] = useState('');
  const [telefono, setTelefono] = useState('');
  const [comunidadAutonoma, setComunidadAutonoma] = useState(COMUNIDADES[0]);
  const [fechaRegistro, setFechaRegistro] = useState(
    new Date().toISOString().split('T')[0]
  );
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!isEditing || !id) return;

    solicitantesApi.buscar(id).then((s) => {
      setNombre(s.nombre);
      setApellidos(s.apellidos);
      setEmail(s.email);
      setTelefono(s.telefono);
      setComunidadAutonoma(s.comunidad_autonoma);
      setFechaRegistro(s.fecha_registro);
    });
  }, [id, isEditing]);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    const payload = {
      nombre,
      apellidos,
      email,
      telefono,
      comunidad_autonoma: comunidadAutonoma,
      fecha_registro: fechaRegistro,
    };

    try {
      if (isEditing && id) {
        await solicitantesApi.actualizar(id, payload);
      } else {
        await solicitantesApi.crear(payload);
      }
      navigate('/');
    } catch (err) {
      const axiosError = err as AxiosErrorShape;

      if (axiosError.response?.status === 422) {
        setError(axiosError.response.data?.message || 'Datos inválidos. Revisa el formulario.');
      } else if (axiosError.response?.status === 401) {
        setError('Tu sesión ha expirado. Inicia sesión de nuevo.');
      } else {
        setError('No se pudo guardar el solicitante. Inténtalo de nuevo.');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <Card className="max-w-lg mx-auto">
        <h1 className="text-tf-navy font-bold text-2xl mb-6">
          {isEditing ? 'Editar solicitante' : 'Nuevo solicitante'}
        </h1>

        <form onSubmit={handleSubmit} className="space-y-4">
          <input
            type="text"
            placeholder="Nombre"
            value={nombre}
            onChange={(e) => setNombre(e.target.value)}
            required
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          />
          <input
            type="text"
            placeholder="Apellidos"
            value={apellidos}
            onChange={(e) => setApellidos(e.target.value)}
            required
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          />
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          />
          <input
            type="tel"
            placeholder="Teléfono"
            value={telefono}
            onChange={(e) => setTelefono(e.target.value)}
            required
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          />
          <select
            value={comunidadAutonoma}
            onChange={(e) => setComunidadAutonoma(e.target.value)}
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          >
            {COMUNIDADES.map((c) => (
              <option key={c} value={c}>{c}</option>
            ))}
          </select>
          <input
            type="date"
            value={fechaRegistro}
            onChange={(e) => setFechaRegistro(e.target.value)}
            required
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          />

          {error && <p className="text-red-600 text-sm">{error}</p>}

          <div className="flex gap-2 pt-2">
            <Button type="submit" variant="primary" disabled={loading}>
              {loading ? 'Guardando...' : 'Guardar'}
            </Button>
            <Button type="button" variant="secondary" onClick={() => navigate('/')}>
              Cancelar
            </Button>
          </div>
        </form>
      </Card>
    </div>
  );
}

export default SolicitanteFormPage;

import { useEffect, useState, type FormEvent } from 'react';
import { useNavigate, useParams, useSearchParams } from 'react-router-dom';
import { solicitudesApi } from '../api/solicitudes';
import type { TipoAyuda, Estado } from '../types';
import Button from '../components/common/Button';
import Card from '../components/common/Card';

const TIPOS_AYUDA: { value: TipoAyuda; label: string }[] = [
  { value: 'Alquiler', label: 'Ayuda al Alquiler' },
  { value: 'ChequeBebe', label: 'Cheque Bebé' },
  { value: 'IngresoMinimoVital', label: 'Ingreso Mínimo Vital' },
  { value: 'BonoCulturalJoven', label: 'Bono Cultural Joven' },
];

const ESTADOS: { value: Estado; label: string }[] = [
  { value: 'Pendiente', label: 'Pendiente' },
  { value: 'EnRevision', label: 'En Revisión' },
  { value: 'Concedida', label: 'Concedida' },
  { value: 'Denegada', label: 'Denegada' },
];

interface AxiosErrorShape {
  response?: {
    status?: number;
    data?: { message?: string };
  };
}

function SolicitudFormPage() {
  const { id } = useParams<{ id: string }>();
  const [searchParams] = useSearchParams();
  const isEditing = !!id;
  const navigate = useNavigate();

  const [solicitanteId, setSolicitanteId] = useState(
    searchParams.get('solicitante_id') || ''
  );
  const [tipoAyuda, setTipoAyuda] = useState<TipoAyuda>('Alquiler');
  const [fechaSolicitud, setFechaSolicitud] = useState(
    new Date().toISOString().split('T')[0]
  );
  const [fechaResolucion, setFechaResolucion] = useState('');
  const [importeEstimado, setImporteEstimado] = useState('');
  const [estado, setEstado] = useState<Estado>('Pendiente');
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!isEditing || !id) return;

    solicitudesApi.buscar(id).then((s) => {
      setSolicitanteId(s.solicitante_id);
      setTipoAyuda(s.tipo_ayuda);
      setFechaSolicitud(s.fecha_solicitud);
      setFechaResolucion(s.fecha_resolucion || '');
      setImporteEstimado(String(s.importe_estimado));
      setEstado(s.estado);
    });
  }, [id, isEditing]);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    const payload = {
      solicitante_id: solicitanteId,
      tipo_ayuda: tipoAyuda,
      fecha_solicitud: fechaSolicitud,
      fecha_resolucion: fechaResolucion || null,
      importe_estimado: Number(importeEstimado),
      estado,
    };

    try {
      if (isEditing && id) {
        await solicitudesApi.actualizar(id, payload);
      } else {
        await solicitudesApi.crear(payload);
      }
      navigate(`/solicitantes/${solicitanteId}`);
    } catch (err) {
      const axiosError = err as AxiosErrorShape;

      if (axiosError.response?.status === 422) {
        setError(axiosError.response.data?.message || 'Datos inválidos. Revisa el formulario.');
      } else if (axiosError.response?.status === 401) {
        setError('Tu sesión ha expirado. Inicia sesión de nuevo.');
      } else {
        setError('No se pudo guardar la solicitud. Inténtalo de nuevo.');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <Card className="max-w-lg mx-auto">
        <h1 className="text-tf-navy font-bold text-2xl mb-6">
          {isEditing ? 'Editar solicitud' : 'Nueva solicitud'}
        </h1>

        <form onSubmit={handleSubmit} className="space-y-4">
          <select
            value={tipoAyuda}
            onChange={(e) => setTipoAyuda(e.target.value as TipoAyuda)}
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          >
            {TIPOS_AYUDA.map((t) => (
              <option key={t.value} value={t.value}>{t.label}</option>
            ))}
          </select>

          <div>
            <label className="text-sm text-gray-500 ml-2">Fecha de solicitud</label>
            <input
              type="date"
              value={fechaSolicitud}
              onChange={(e) => setFechaSolicitud(e.target.value)}
              required
              className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none mt-1"
            />
          </div>

          <div>
            <label className="text-sm text-gray-500 ml-2">
              Fecha de resolución (opcional)
            </label>
            <input
              type="date"
              value={fechaResolucion}
              onChange={(e) => setFechaResolucion(e.target.value)}
              className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none mt-1"
            />
          </div>

          <input
            type="number"
            step="0.01"
            placeholder="Importe estimado (€)"
            value={importeEstimado}
            onChange={(e) => setImporteEstimado(e.target.value)}
            required
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          />

          <select
            value={estado}
            onChange={(e) => setEstado(e.target.value as Estado)}
            className="w-full px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none"
          >
            {ESTADOS.map((e) => (
              <option key={e.value} value={e.value}>{e.label}</option>
            ))}
          </select>

          {error && <p className="text-red-600 text-sm">{error}</p>}

          <div className="flex gap-2 pt-2">
            <Button type="submit" variant="primary" disabled={loading}>
              {loading ? 'Guardando...' : 'Guardar'}
            </Button>
            <Button
              type="button"
              variant="secondary"
              onClick={() => navigate(solicitanteId ? `/solicitantes/${solicitanteId}` : '/')}
            >
              Cancelar
            </Button>
          </div>
        </form>
      </Card>
    </div>
  );
}

export default SolicitudFormPage;

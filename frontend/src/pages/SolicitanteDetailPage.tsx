import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { solicitantesApi } from '../api/solicitantes';
import { solicitudesApi } from '../api/solicitudes';
import type { Solicitante, Solicitud } from '../types';
import Card from '../components/common/Card';
import Badge from '../components/common/Badge';
import Button from '../components/common/Button';

function SolicitanteDetailPage() {
  const { id } = useParams<{ id: string }>();
  const [solicitante, setSolicitante] = useState<Solicitante | null>(null);
  const [solicitudes, setSolicitudes] = useState<Solicitud[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!id) return;

    Promise.all([
      solicitantesApi.buscar(id),
      solicitudesApi.listar(),
    ])
      .then(([solicitanteData, todasLasSolicitudes]) => {
        setSolicitante(solicitanteData);
        // Filtramos en el cliente las solicitudes de este solicitante,
        // ya que la API no expone (todavia) un endpoint dedicado de
        // filtrado por solicitante_id.
        setSolicitudes(todasLasSolicitudes.filter((s) => s.solicitante_id === id));
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <p className="p-6 text-gray-500">Cargando...</p>;
  if (error) return <p className="p-6 text-red-600">Error: {error}</p>;
  if (!solicitante) return <p className="p-6 text-gray-500">Solicitante no encontrado.</p>;

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <Link to="/" className="text-tf-teal-dark font-semibold mb-4 inline-block">
        ← Volver al listado
      </Link>

      <Card className="mb-6">
        <h1 className="text-tf-navy font-bold text-2xl mb-2">{solicitante.nombre_completo}</h1>
        <p className="text-gray-500">{solicitante.email} — {solicitante.telefono}</p>
        <p className="text-gray-500">{solicitante.comunidad_autonoma}</p>
        <p className="text-sm text-gray-400 mt-2">
          Registrado el {new Date(solicitante.fecha_registro).toLocaleDateString('es-ES')}
        </p>
      </Card>

      <div className="flex justify-between items-center mb-4">
        <h2 className="text-tf-navy font-bold text-xl">
          Solicitudes ({solicitudes.length})
        </h2>
        <Button variant="primary" className="text-sm">+ Nueva solicitud</Button>
      </div>

      {solicitudes.length === 0 ? (
        <p className="text-gray-500">Este solicitante todavía no tiene solicitudes.</p>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {solicitudes.map((s) => (
            <Card key={s.id}>
              <div className="flex justify-between items-start mb-2">
                <span className="font-semibold text-tf-navy">{s.tipo_ayuda_label}</span>
                <Badge estado={s.estado} label={s.estado_label} />
              </div>
              <p className="text-sm text-gray-500">
                Solicitada el {new Date(s.fecha_solicitud).toLocaleDateString('es-ES')}
              </p>
              <p className="text-sm text-gray-500">
                Importe estimado: {s.importe_estimado.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' })}
              </p>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
}

export default SolicitanteDetailPage;

import { useEffect, useState } from 'react';
import { solicitantesApi } from '../api/solicitantes';
import type { Solicitante } from '../types';
import Card from '../components/common/Card';
import Button from '../components/common/Button';

/**
 * Pantalla: Listado de Solicitantes.
 *
 * Trae los solicitantes desde la API y los muestra en tarjetas,
 * con la identidad visual de TuTramiteFacil.
 */
function SolicitantesListPage() {
  const [solicitantes, setSolicitantes] = useState<Solicitante[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    solicitantesApi
      .listar()
      .then((data) => setSolicitantes(data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <p className="p-6 text-gray-500">Cargando solicitantes...</p>;
  }

  if (error) {
    return <p className="p-6 text-red-600">Error: {error}</p>;
  }

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-tf-navy font-bold text-2xl">Solicitantes</h1>
        <Button variant="primary">+ Nuevo solicitante</Button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {solicitantes.map((s) => (
          <Card key={s.id}>
            <h2 className="font-semibold text-tf-navy mb-1">{s.nombre_completo}</h2>
            <p className="text-sm text-gray-500 mb-1">{s.email}</p>
            <p className="text-sm text-gray-500 mb-4">{s.comunidad_autonoma}</p>
            <Button variant="secondary" className="text-sm px-3 py-1.5">
              Ver detalle
            </Button>
          </Card>
        ))}
      </div>
    </div>
  );
}

export default SolicitantesListPage;

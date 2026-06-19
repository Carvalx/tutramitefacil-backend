import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { solicitantesApi } from '../api/solicitantes';
import type { Solicitante } from '../types';
import Card from '../components/common/Card';
import Button from '../components/common/Button';
import KebabMenu from '../components/common/KebabMenu';
import ConfirmModal from '../components/common/ConfirmModal';

function SolicitantesListPage() {
  const [solicitantes, setSolicitantes] = useState<Solicitante[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [busqueda, setBusqueda] = useState('');
  const [pendingDelete, setPendingDelete] = useState<{ id: string; nombre: string } | null>(null);
  const navigate = useNavigate();

  useEffect(() => {
    solicitantesApi
      .listar()
      .then((data) => setSolicitantes(data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  const handleEliminar = (id: string, nombre: string) => {
    setPendingDelete({ id, nombre });
  };

  const confirmEliminar = async () => {
    if (!pendingDelete) return;

    try {
      await solicitantesApi.eliminar(pendingDelete.id);
      setSolicitantes((prev) => prev.filter((s) => s.id !== pendingDelete.id));
    } catch {
      alert('No se pudo eliminar. Asegúrate de haber iniciado sesión.');
    } finally {
      setPendingDelete(null);
    }
  };

  if (loading) {
    return <p className="p-6 text-gray-500">Cargando solicitantes...</p>;
  }

  if (error) {
    return <p className="p-6 text-red-600">Error: {error}</p>;
  }

  const solicitantesFiltrados = solicitantes.filter((s) =>
    s.nombre_completo.toLowerCase().includes(busqueda.toLowerCase()) ||
    s.email.toLowerCase().includes(busqueda.toLowerCase())
  );

  return (
    <div className="bg-gray-50 min-h-screen p-6">
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-tf-navy font-bold text-2xl">Solicitantes</h1>
        <Link to="/solicitantes/nuevo">
          <Button variant="primary">+ Nuevo solicitante</Button>
        </Link>
      </div>

      <input
        type="text"
        placeholder="Buscar por nombre o email..."
        value={busqueda}
        onChange={(e) => setBusqueda(e.target.value)}
        className="w-full max-w-md px-4 py-2.5 rounded-full border border-gray-200 focus:border-tf-teal focus:outline-none mb-6"
      />

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {solicitantesFiltrados.map((s) => (
          <Card key={s.id} className="relative">
            <div className="absolute top-3 right-3">
              <KebabMenu
                options={[
                  { label: 'Ver detalle', onClick: () => navigate(`/solicitantes/${s.id}`) },
                  { label: 'Eliminar', onClick: () => handleEliminar(s.id, s.nombre_completo), danger: true },
                ]}
              />
            </div>
            <h2 className="font-semibold text-tf-navy mb-1 pr-8">{s.nombre_completo}</h2>
            <p className="text-sm text-gray-500 mb-1">{s.email}</p>
            <p className="text-sm text-gray-500">{s.comunidad_autonoma}</p>
          </Card>
        ))}
      </div>

      {solicitantesFiltrados.length === 0 && (
        <p className="text-gray-500 mt-4">No se encontraron solicitantes.</p>
      )}

      <ConfirmModal
        isOpen={pendingDelete !== null}
        title="¿Estás seguro?"
        message={`Vas a eliminar a ${pendingDelete?.nombre}. Esta acción no se puede deshacer.`}
        onConfirm={confirmEliminar}
        onCancel={() => setPendingDelete(null)}
      />
    </div>
  );
}

export default SolicitantesListPage;

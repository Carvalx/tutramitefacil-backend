import type { Estado } from '../../types';

interface BadgeProps {
  estado: Estado;
  label: string;
}

/**
 * Etiqueta de color según el estado de una Solicitud.
 *
 * Pendiente    -> gris
 * EnRevision   -> amarillo/naranja
 * Concedida    -> verde (turquesa de marca)
 * Denegada     -> rojo
 */
function Badge({ estado, label }: BadgeProps) {
  const estadoStyles: Record<Estado, string> = {
    Pendiente: 'bg-gray-100 text-gray-700',
    EnRevision: 'bg-amber-100 text-amber-700',
    Concedida: 'bg-tf-teal/15 text-tf-teal-dark',
    Denegada: 'bg-red-100 text-red-700',
  };

  return (
    <span className={`text-xs font-semibold px-2.5 py-1 rounded-full ${estadoStyles[estado]}`}>
      {label}
    </span>
  );
}

export default Badge;

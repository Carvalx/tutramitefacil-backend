import type { ReactNode } from 'react';

interface CardProps {
  children: ReactNode;
  className?: string;
}

/**
 * Tarjeta blanca con sombra suave, usada para mostrar items
 * de listados (solicitantes, solicitudes) de forma consistente.
 */
function Card({ children, className = '' }: CardProps) {
  return (
    <div className={`bg-white rounded-xl shadow-sm border border-gray-100 p-4 ${className}`}>
      {children}
    </div>
  );
}

export default Card;

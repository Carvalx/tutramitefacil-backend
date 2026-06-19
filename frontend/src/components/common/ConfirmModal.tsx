import { useEffect } from 'react';
import Button from './Button';

interface ConfirmModalProps {
  isOpen: boolean;
  title: string;
  message: string;
  onConfirm: () => void;
  onCancel: () => void;
}

/**
 * Modal de confirmación reutilizable.
 * Se cierra al hacer click en el overlay, en "Cancelar" o al pulsar Escape.
 */
function ConfirmModal({ isOpen, title, message, onConfirm, onCancel }: ConfirmModalProps) {
  useEffect(() => {
    if (!isOpen) return;

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') onCancel();
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, onCancel]);

  if (!isOpen) return null;

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      onClick={onCancel}
    >
      <div
        className="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="p-6">
          <h2 className="text-tf-navy font-bold text-xl mb-2">{title}</h2>
          <p className="text-gray-500 text-sm leading-relaxed">{message}</p>
        </div>

        <hr className="border-gray-100" />

        <div className="px-6 py-4 flex justify-end gap-3">
          <Button variant="secondary" onClick={onCancel}>
            Cancelar
          </Button>
          <Button variant="danger" onClick={onConfirm}>
            Eliminar
          </Button>
        </div>
      </div>
    </div>
  );
}

export default ConfirmModal;

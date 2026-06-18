import { useEffect, useRef, useState } from 'react';

interface KebabOption {
  label: string;
  onClick: () => void;
  danger?: boolean;
}

interface KebabMenuProps {
  options: KebabOption[];
}

/**
 * Menú de 3 puntos reutilizable para acciones en tarjetas.
 * Se cierra automáticamente al hacer click fuera gracias al listener en document.
 */
function KebabMenu({ options }: KebabMenuProps) {
  const [open, setOpen] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!open) return;

    function handleClickOutside(e: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    }

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, [open]);

  return (
    <div ref={containerRef} className="relative">
      <button
        onClick={() => setOpen((prev) => !prev)}
        aria-label="Más opciones"
        className="p-1.5 rounded-full text-gray-400 hover:text-tf-navy hover:bg-gray-100 transition-colors leading-none"
      >
        ⋮
      </button>

      {open && (
        <div className="absolute right-0 top-8 z-10 bg-white rounded-xl shadow-lg border border-gray-100 py-1 min-w-[150px]">
          {options.map((option) => (
            <button
              key={option.label}
              onClick={() => {
                option.onClick();
                setOpen(false);
              }}
              className={`w-full text-left px-4 py-2 text-sm transition-colors ${
                option.danger
                  ? 'text-red-600 hover:bg-red-50'
                  : 'text-tf-navy hover:bg-tf-teal/10'
              }`}
            >
              {option.label}
            </button>
          ))}
        </div>
      )}
    </div>
  );
}

export default KebabMenu;

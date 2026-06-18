import type { ButtonHTMLAttributes, ReactNode } from 'react';

type ButtonVariant = 'primary' | 'secondary' | 'danger';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  children: ReactNode;
  variant?: ButtonVariant;
}

/**
 * Botón reutilizable con la identidad visual de TuTramiteFacil.
 *
 * variant 'primary'   -> verde turquesa (acción principal: crear, guardar)
 * variant 'secondary' -> blanco con borde turquesa (acción secundaria: cancelar)
 * variant 'danger'    -> rojo (acción destructiva: eliminar)
 */
function Button({ children, variant = 'primary', className = '', ...props }: ButtonProps) {
  const baseStyles = 'px-5 py-2.5 rounded-full font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed';

  const variantStyles: Record<ButtonVariant, string> = {
    primary: 'bg-tf-teal text-white hover:bg-tf-teal-dark',
    secondary: 'bg-white text-tf-navy border-2 border-tf-teal hover:bg-tf-teal/10',
    danger: 'bg-red-500 text-white hover:bg-red-600',
  };

  return (
    <button className={`${baseStyles} ${variantStyles[variant]} ${className}`} {...props}>
      {children}
    </button>
  );
}

export default Button;

import { render, screen, fireEvent } from '@testing-library/react'
import { describe, it, expect, vi } from 'vitest'
import Button from '../components/common/Button'

describe('Button', () => {
  it('renderiza el texto correctamente', () => {
    render(<Button>Guardar</Button>)
    expect(screen.getByText('Guardar')).toBeInTheDocument()
  })

  it('aplica variante primary por defecto', () => {
    render(<Button>Guardar</Button>)
    const btn = screen.getByText('Guardar')
    expect(btn.className).toContain('bg-tf-teal')
  })

  it('aplica variante danger correctamente', () => {
    render(<Button variant="danger">Eliminar</Button>)
    const btn = screen.getByText('Eliminar')
    expect(btn.className).toContain('bg-red-500')
  })

  it('llama al onClick cuando se hace click', () => {
    const handleClick = vi.fn()
    render(<Button onClick={handleClick}>Click</Button>)
    fireEvent.click(screen.getByText('Click'))
    expect(handleClick).toHaveBeenCalledTimes(1)
  })

  it('queda deshabilitado cuando disabled=true', () => {
    render(<Button disabled>Guardando...</Button>)
    expect(screen.getByText('Guardando...')).toBeDisabled()
  })
})

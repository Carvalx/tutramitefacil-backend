import { render, screen } from '@testing-library/react'
import { describe, it, expect } from 'vitest'
import Badge from '../components/common/Badge'

describe('Badge', () => {
  it('renderiza el label correctamente', () => {
    render(<Badge estado="Pendiente" label="Pendiente" />)
    expect(screen.getByText('Pendiente')).toBeInTheDocument()
  })

  it('aplica estilos correctos segun estado Concedida', () => {
    render(<Badge estado="Concedida" label="Concedida" />)
    const badge = screen.getByText('Concedida')
    expect(badge.className).toContain('text-tf-teal-dark')
  })

  it('aplica estilos correctos segun estado Denegada', () => {
    render(<Badge estado="Denegada" label="Denegada" />)
    const badge = screen.getByText('Denegada')
    expect(badge.className).toContain('text-red-700')
  })

  it('aplica estilos correctos segun estado EnRevision', () => {
    render(<Badge estado="EnRevision" label="En Revisión" />)
    const badge = screen.getByText('En Revisión')
    expect(badge.className).toContain('text-amber-700')
  })
})

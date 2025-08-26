import { Controller } from '@hotwired/stimulus'

export default class FiltersController extends Controller {
  static targets = ['content', 'toggle', 'chevron']
  static values = {
    open: { type: Boolean, default: true },
    channel: { type: String, default: 'default' },
    debounce: { type: Number, default: 250 },
  }

  connect() {
    this._timer = null
    this._applyOpenState()
    this._emit()
  }
  disconnect() { if (this._timer) clearTimeout(this._timer) }

  toggle() { this.openValue = !this.openValue; this._applyOpenState() }

  _applyOpenState() {
    const isOpen = !!this.openValue
    if (this.hasContentTarget) this.contentTarget.classList.toggle('hidden', !isOpen)
    if (this.hasToggleTarget) this.toggleTarget.setAttribute('aria-expanded', String(isOpen))
    if (this.hasChevronTarget) this.chevronTarget.classList.toggle('rotate-180', !isOpen)
  }

  onInput() {
    if (this._timer) clearTimeout(this._timer)
    this._timer = setTimeout(() => { this._emit(); this._timer = null }, this.debounceValue)
  }

  clear() {
    const root = this.hasContentTarget ? this.contentTarget : this.element
    for (const el of root.querySelectorAll('input, select, textarea')) {
      if (el.type === 'checkbox' || el.type === 'radio') el.checked = false
      else el.value = ''
    }
    this._emit({ reset: true })
  }

  _emit(extra = {}) {
    const filters = this._collect()
    const detail = { filters, ...extra }
    document.dispatchEvent(new CustomEvent(`${this.channelValue}:filters-changed`, { detail }))
  }

  _collect() {
    const root = this.hasContentTarget ? this.contentTarget : this.element
    const filters = {}
    const fields = root.querySelectorAll('input[name], select[name], textarea[name], [data-filter-key]')
    for (const el of fields) {
      const key = el.getAttribute('name') || el.dataset.filterKey
      if (!key) continue
      let val = ''
      if (el.tagName === 'SELECT') val = el.value
      else if (el.type === 'checkbox') val = el.checked ? (el.value || '1') : ''
      else if (el.type === 'radio') { if (!el.checked) continue; val = el.value }
      else val = (el.value || '').trim()
      if (val === '') continue
      filters[key] = val
    }
    return filters
  }
}


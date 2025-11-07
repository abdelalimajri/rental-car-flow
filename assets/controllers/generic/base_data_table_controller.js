import { Controller } from '@hotwired/stimulus'
import { TabulatorFull as Tabulator } from 'tabulator-tables'

export default class BaseDataTableController extends Controller {
  static values = {
    endpoint: { type: String, default: '/api' },
    pageSize: { type: Number, default: 25 },
    filters: { type: Object, default: {} },
    channel: { type: String, default: 'default' },
    index: { type: String, default: 'id' },
    columns: { type: Object, default: null },
    placeholder: { type: String, default: 'Aucun élément' },
    orderMapping: { type: Object, default: {} },
  }

  connect() {
    this._injectLoading()
    this.tabulator = new Tabulator(this.element, {
      index: this.indexValue,
      layout: 'fitColumns',
      columns: this.resolveColumns(),
      pagination: true,
      paginationMode: 'remote',
      paginationSize: this.pageSizeValue,
      paginationSizeSelector: [10, 25, 50, 100],
      sortMode: 'remote',
      ajaxSorting: true,
      paginationDataReceived: { last_page: 'last_page', data: 'data' },
      ajaxURL: this.resolveEndpoint(),
      ajaxRequestFunc: this.fetchData,
      ajaxResponse: this.formatResponse,
      placeholder: this.placeholderValue,
      locale: 'fr',
      langs: { fr: { pagination: { first: 'Premier', first_title: 'Première page', last: 'Dernier', last_title: 'Dernière page', prev: 'Précédent', prev_title: 'Page précédente', next: 'Suivant', next_title: 'Page suivante', all: 'Tous', page_size: 'Taille de la page' } } },
    })

    this.onFiltersChanged = (event) => {
      const detail = event?.detail ?? {}
      const filters = detail.filters ?? {}
      const reset = !!detail.reset
      this.applyPatch(reset ? { reset: true } : filters)
    }
    document.addEventListener(`${this.channelValue}:filters-changed`, this.onFiltersChanged)

    this.onNavbarSearch = (event) => {
      const name = `${this.channelValue}:global-search`
      if (event.type !== name) return
      const q = String(event.detail?.q || '').trim()
      this.applyPatch({ q })
    }
    document.addEventListener(`${this.channelValue}:global-search`, this.onNavbarSearch)

  }

  disconnect() {
    document.removeEventListener(`${this.channelValue}:filters-changed`, this.onFiltersChanged)
    document.removeEventListener(`${this.channelValue}:global-search`, this.onNavbarSearch)
    this.tabulator?.destroy()
    this.tabulator = null
  }

  resolveColumns() {
    if (Array.isArray(this.columnsValue) && this.columnsValue.length > 0) return this.columnsValue
    return []
  }

  resolveEndpoint() { return this.endpointValue }

  applyPatch(patch = {}) {
    const isReset = !!patch.reset
    const next = isReset ? {} : { ...(this.filtersValue || {}) }
    if (!isReset) {
      for (const [k, v] of Object.entries(patch)) {
        if (k === 'reset') continue
        if (v === undefined || v === null || String(v).trim() === '') delete next[k]
        else next[k] = v
      }
    }
    this.filtersValue = next
    if (Object.keys(next).length === 0) this.tabulator?.setHeight(false)
    this.showLoading()
    this.tabulator?.setPage(1)
  }

  fetchData = async (_url, _config, params = {}) => {
    this.showLoading()
    try {
      const base = this.resolveEndpoint()
      const u = new URL(base, window.location.origin)
      const sp = u.searchParams
      const page = Number(params.page) || 1
      const size = Number(params.size) || this.pageSizeValue
      sp.set('page', page)
      sp.set('itemsPerPage', size)

      if (Array.isArray(params.sort) && params.sort.length > 0) {
        const { field, dir } = params.sort[0]
        if (field && dir) {
          const orderField = this.orderMappingValue?.[field] || field
          sp.set(`order[${orderField}]`, dir)
        }
      }

      const filters = this.filtersValue || {}
      for (const [key, raw] of Object.entries(filters)) {
        if (raw === undefined || raw === null) continue
        const value = typeof raw === 'boolean' ? String(raw) : String(raw).trim()
        if (value === '') continue
        sp.set(key, value)
      }

      const res = await fetch(u, { credentials: 'same-origin', headers: { Accept: 'application/ld+json' } })
      if (!res.ok) {
        const text = await res.text().catch(() => '')
        throw new Error(`HTTP ${res.status}: ${text.slice(0, 200)}`)
      }
      const json = await res.json()
      this.hideLoading()
      return json
    } catch (e) {
      console.error(e)
      this.setLoadingText('Erreur de chargement')
      throw e
    }
  }

  formatResponse = (_url, params, payload) => {
    const data = payload?.['hydra:member'] ?? payload?.member ?? payload?.data ?? []
    const totalItems = payload?.['hydra:totalItems'] ?? payload?.totalItems ?? (Array.isArray(data) ? data.length : 0)
    const size = Number(params?.size ?? this.pageSizeValue) || (Array.isArray(data) ? data.length : 1)
    return { data, last_page: Math.max(1, Math.ceil(Number(totalItems) / size)) }
  }

  _injectLoading() {
    if (this.element.querySelector('[data-loading-overlay]')) return
    this.element.classList.add('relative')
    const overlay = document.createElement('div')
    overlay.dataset.loadingOverlay = 'true'
    overlay.className = 'hidden absolute inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-10'
    overlay.innerHTML = `
      <div class="flex flex-col items-center gap-3">
        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <p class="text-sm font-medium text-gray-700" data-loading-text>Chargement...</p>
      </div>`
    this.element.appendChild(overlay)
  }

  showLoading() { const o = this.element.querySelector('[data-loading-overlay]'); if (o) o.classList.remove('hidden') }
  hideLoading() { const o = this.element.querySelector('[data-loading-overlay]'); if (o) o.classList.add('hidden'); this.setLoadingText('Chargement...') }
  setLoadingText(text) { const el = this.element.querySelector('[data-loading-text]'); if (el) el.textContent = text }
}


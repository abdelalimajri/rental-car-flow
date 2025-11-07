import { Controller } from '@hotwired/stimulus'

export default class GlobalSearchController extends Controller {
  static values = { channel: String, debounce: { type: Number, default: 300 } }

  connect() { this._timer = null }
  disconnect() { if (this._timer) clearTimeout(this._timer) }

  onInput(e) {
    if (this._timer) clearTimeout(this._timer)
    this._timer = setTimeout(() => {
      const q = String(e.target.value || '').trim()
      document.dispatchEvent(new CustomEvent(`${this.channelValue}:global-search`, { detail: { q } }))
      this._timer = null
    }, this.debounceValue)
  }
}


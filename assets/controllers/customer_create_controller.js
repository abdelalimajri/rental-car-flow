/* stimulusFetch: 'lazy' */
import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  static targets = [
    'status', 'submitBtn',
    'emailError', 'identityNumberError', 'drivingLicenseNumberError',
    'firstNameError', 'lastNameError', 'phoneNumberError', 'licenseExpirationDateError', 'birthDateError', 'genderError', 'activeError', 'addressError'
  ]
  static values = { endpoint: String }

  connect() {}

  async checkDuplicate(event) {
    const field = event.target.name
    const value = event.target.value?.trim()
    if (!value) return

    const params = new URLSearchParams()
    params.set(field, value)

    try {
      const url = `${this.endpointValue}?${params.toString()}`
      const res = await fetch(url, {
        headers: { 'Accept': 'application/ld+json' }
      })
      if (!res.ok) return
      const data = await res.json()
      const count = data['hydra:totalItems'] ?? (Array.isArray(data) ? data.length : 0)
      if (count > 0) {
        this.setFieldError(field, 'Existe déjà dans le système')
      } else {
        this.setFieldError(field, '')
      }
    } catch (e) {
      // network or API down: do nothing noisy
    }
  }

  setFieldError(field, message) {
    const map = {
      email: 'emailError',
      identityNumber: 'identityNumberError',
      drivingLicenseNumber: 'drivingLicenseNumberError',
      firstName: 'firstNameError',
      lastName: 'lastNameError',
      phoneNumber: 'phoneNumberError',
      licenseExpirationDate: 'licenseExpirationDateError',
      birthDate: 'birthDateError',
      gender: 'genderError',
      address: 'addressError',
    }
    const targetName = map[field]
    // Stimulus ne fournit pas de méthode générique hasTarget(name); on vérifie dynamiquement l'accessor généré
    const accessor = targetName ? `${targetName}Target` : null
    if (accessor && accessor in this) {
      this[accessor].textContent = message || ''
    }
  }

  clearAllFieldErrors() {
    const fields = [
      'email','identityNumber','drivingLicenseNumber','firstName','lastName','phoneNumber','licenseExpirationDate','birthDate','gender','address'
    ]
    fields.forEach(f => this.setFieldError(f, ''))
  }

  displayViolations(violations) {
    // Regrouper plusieurs erreurs par champ
    const grouped = {}
    for (const v of violations) {
      const path = v.propertyPath?.replace(/^data\./,'') // au cas où API Platform renvoie data.field
      if (!grouped[path]) grouped[path] = []
      grouped[path].push(v.message)
    }
    // Afficher par champ
    for (const [field, messages] of Object.entries(grouped)) {
      this.setFieldError(field, messages.join('\n'))
    }
    // Construire liste générale
    const list = violations.map(v => `${v.propertyPath}: ${v.message}`)
    this.statusTarget.innerHTML = `<ul class="text-red-600 list-disc pl-5">${list.map(li => `<li>${li}</li>`).join('')}</ul>`
  }

  async submit(event) {
    event.preventDefault()
    this.statusTarget.textContent = ''
    this.clearAllFieldErrors()

    const form = event.target
    const payload = Object.fromEntries(new FormData(form).entries())

    // Supprimer les champs si valeur vide ou null
    for (const key of Object.keys(payload)) {
      if (typeof payload[key] === 'string') {
        const trimmed = payload[key].trim()
        if (trimmed === '') {
          delete payload[key]
          continue
        }
        // conserver la valeur trimée (optionnel)
        payload[key] = trimmed
      }
      if (payload[key] === null) {
        delete payload[key]
      }
    }

    // normalize booleans uniquement si présent
    if ('active' in payload) {
      payload.active = payload.active === '1' || payload.active === 1 || payload.active === true
    }

    // convert dates vers ISO uniquement si présentes
    if (payload.licenseExpirationDate) {
      try {
        const d = new Date(payload.licenseExpirationDate)
        if (!isNaN(d)) payload.licenseExpirationDate = d.toISOString(); else delete payload.licenseExpirationDate
      } catch { delete payload.licenseExpirationDate }
    }
    if (payload.birthDate) {
      try {
        const d = new Date(payload.birthDate)
        if (!isNaN(d)) payload.birthDate = d.toISOString(); else delete payload.birthDate
      } catch { delete payload.birthDate }
    }

    this.submitBtnTarget.disabled = true
    try {
      const res = await fetch(this.endpointValue, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/ld+json, application/json'
        },
        body: JSON.stringify(payload)
      })
      const data = await res.json().catch(() => ({}))
      if (res.ok) {
        this.statusTarget.textContent = 'Client créé avec succès'
        setTimeout(() => { window.location.href = '/customers' }, 600)
      } else {
        if (data.violations?.length) {
          this.displayViolations(data.violations)
        } else if (data['hydra:description'] || data.detail || data.description) {
          this.statusTarget.textContent = data['hydra:description'] || data.detail || data.description
        } else {
          this.statusTarget.textContent = 'Erreur lors de la création.'
        }
      }
    } catch (e) {
      this.statusTarget.textContent = 'Erreur réseau.'
    } finally {
      this.submitBtnTarget.disabled = false
    }
  }
}

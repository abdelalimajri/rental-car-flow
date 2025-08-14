import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["menu", "button", "chevron"]

    connect() {
        // Fermer le dropdown quand on clique ailleurs
        this.clickOutsideHandler = this.clickOutside.bind(this)
        document.addEventListener("click", this.clickOutsideHandler)
    }

    disconnect() {
        document.removeEventListener("click", this.clickOutsideHandler)
    }

    toggle(event) {
        event.preventDefault()
        event.stopPropagation()
        
        if (this.menuTarget.classList.contains("hidden")) {
            this.open()
        } else {
            this.close()
        }
    }

    open() {
        this.menuTarget.classList.remove("hidden")
        this.chevronTarget.style.transform = "rotate(180deg)"
        
        // Focus management
        this.menuTarget.focus()
        
        // Animation d'entrée
        this.menuTarget.style.opacity = "0"
        this.menuTarget.style.transform = "translateY(-10px)"
        
        requestAnimationFrame(() => {
            this.menuTarget.style.transition = "opacity 200ms ease, transform 200ms ease"
            this.menuTarget.style.opacity = "1"
            this.menuTarget.style.transform = "translateY(0)"
        })
    }

    close() {
        this.menuTarget.style.transition = "opacity 150ms ease, transform 150ms ease"
        this.menuTarget.style.opacity = "0"
        this.menuTarget.style.transform = "translateY(-5px)"
        
        setTimeout(() => {
            this.menuTarget.classList.add("hidden")
            this.chevronTarget.style.transform = "rotate(0deg)"
        }, 150)
    }

    clickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.close()
        }
    }

    // Gestion du clavier
    keydown(event) {
        if (event.key === "Escape") {
            this.close()
            this.buttonTarget.focus()
        }
    }
}
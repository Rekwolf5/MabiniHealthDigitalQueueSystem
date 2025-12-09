// Simplified JavaScript without conflicts
document.addEventListener("DOMContentLoaded", () => {
  console.log("App.js loaded successfully")

  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert")
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0"
      setTimeout(() => {
        alert.remove()
      }, 300)
    }, 5000)
  })

  // Simple search functionality
  const searchInput = document.querySelector("#search")
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase()
      const tableRows = document.querySelectorAll(".data-table tbody tr")

      tableRows.forEach((row) => {
        const text = row.textContent.toLowerCase()
        if (text.includes(searchTerm)) {
          row.style.display = ""
        } else {
          row.style.display = "none"
        }
      })
    })
  }

  // Real-time clock
  function updateClock() {
    const now = new Date()
    const timeString = now.toLocaleTimeString()
    const clockElement = document.querySelector("#current-time")
    if (clockElement) {
      clockElement.textContent = timeString
    }
  }

  setInterval(updateClock, 1000)
  updateClock()
})

// Report generation function
function generateReport(type) {
  console.log("Generating report:", type)

  const button = event.target
  const originalText = button.innerHTML
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...'
  button.disabled = true

  setTimeout(() => {
    const form = document.createElement("form")
    form.method = "POST"
    form.action = "/reports/generate"

    const csrfToken = document.querySelector('meta[name="csrf-token"]')
    if (csrfToken) {
      const csrfInput = document.createElement("input")
      csrfInput.type = "hidden"
      csrfInput.name = "_token"
      csrfInput.value = csrfToken.getAttribute("content")
      form.appendChild(csrfInput)
    }

    const typeInput = document.createElement("input")
    typeInput.type = "hidden"
    typeInput.name = "type"
    typeInput.value = type
    form.appendChild(typeInput)

    document.body.appendChild(form)
    form.submit()
  }, 1000)
}

document.addEventListener("DOMContentLoaded", () => {
  // Theme toggle functionality
  const themeToggle = document.getElementById("theme-toggle")
  const htmlElement = document.documentElement

  // Check for saved theme preference or use preferred color scheme
  const savedTheme = localStorage.getItem("theme")
  if (savedTheme) {
    htmlElement.className = savedTheme
  } else {
    // Check for preferred color scheme
    const prefersDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches
    htmlElement.className = prefersDarkMode ? "dark-mode" : "light-mode"
  }

  // Toggle theme when button is clicked
  themeToggle.addEventListener("click", () => {
    if (htmlElement.classList.contains("light-mode")) {
      htmlElement.classList.replace("light-mode", "dark-mode")
      localStorage.setItem("theme", "dark-mode")
    } else {
      htmlElement.classList.replace("dark-mode", "light-mode")
      localStorage.setItem("theme", "light-mode")
    }
  })

  // Skills tab functionality
  const categoryTabs = document.querySelectorAll(".category-tab")
  const skillContents = document.querySelectorAll(".skill-content")

  categoryTabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      // Remove active class from all tabs
      categoryTabs.forEach((t) => t.classList.remove("active"))

      // Add active class to clicked tab
      this.classList.add("active")

      // Hide all content sections
      skillContents.forEach((content) => {
        content.style.display = "none"
      })

      // Show the selected content
      const targetId = this.getAttribute("data-target")
      document.getElementById(targetId).style.display = "block"
    })
  })

  // Portfolio filter functionality
  const filterButtons = document.querySelectorAll(".filter-btn")
  const portfolioItems = document.querySelectorAll(".portfolio-item")

  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remove active class from all buttons
      filterButtons.forEach((btn) => btn.classList.remove("active"))

      // Add active class to clicked button
      this.classList.add("active")

      const filter = this.getAttribute("data-filter")

      // Filter portfolio items
      portfolioItems.forEach((item) => {
        if (filter === "all" || item.getAttribute("data-category") === filter) {
          item.style.display = "block"
        } else {
          item.style.display = "none"
        }
      })
    })
  })

  // Add animation to elements when they come into view
  const animateOnScroll = () => {
    const elements = document.querySelectorAll(
      ".skill-card, .project-card, .stat-card, .timeline-item, .certification-card",
    )

    elements.forEach((element) => {
      const elementPosition = element.getBoundingClientRect().top
      const windowHeight = window.innerHeight

      if (elementPosition < windowHeight - 50) {
        element.style.opacity = "1"
        element.style.transform = "translateY(0)"
      }
    })
  }

  // Set initial state for animation
  const elementsToAnimate = document.querySelectorAll(
    ".skill-card, .project-card, .stat-card, .timeline-item, .certification-card",
  )
  elementsToAnimate.forEach((element) => {
    element.style.opacity = "0"
    element.style.transform = "translateY(20px)"
    element.style.transition = "opacity 0.6s ease, transform 0.6s ease"
  })

  // Run animation on load and scroll
  window.addEventListener("load", animateOnScroll)
  window.addEventListener("scroll", animateOnScroll)

  // Form validation enhancement
  const contactForm = document.querySelector(".contact-form")
  if (contactForm) {
    contactForm.addEventListener("submit", (event) => {
      let isValid = true

      // Simple validation
      const nameInput = document.getElementById("name")
      const emailInput = document.getElementById("email")
      const messageInput = document.getElementById("message")

      if (nameInput.value.trim() === "") {
        isValid = false
        nameInput.classList.add("is-invalid")
      } else {
        nameInput.classList.remove("is-invalid")
      }

      if (emailInput.value.trim() === "" || !isValidEmail(emailInput.value)) {
        isValid = false
        emailInput.classList.add("is-invalid")
      } else {
        emailInput.classList.remove("is-invalid")
      }

      if (messageInput.value.trim() === "") {
        isValid = false
        messageInput.classList.add("is-invalid")
      } else {
        messageInput.classList.remove("is-invalid")
      }

      if (!isValid) {
        event.preventDefault()
      }
    })
  }

  function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return regex.test(email)
  }
})

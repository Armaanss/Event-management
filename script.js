// Client-side validation for registration form
document.addEventListener("DOMContentLoaded", () => {
  const registrationForm = document.getElementById("registrationForm")

  if (registrationForm) {
    registrationForm.addEventListener("submit", (event) => {
      let hasError = false

      // Get form fields
      const firstName = document.getElementById("first_name")
      const lastName = document.getElementById("last_name")
      const contact = document.getElementById("contact")
      const email = document.getElementById("email")
      const password = document.getElementById("password")
      const confirmPassword = document.getElementById("confirm_password")
      const eventInterest = document.getElementById("event_interest")

      // Clear previous error messages
      const errorElements = document.querySelectorAll(".error")
      errorElements.forEach((element) => {
        element.textContent = ""
      })

      // Validate first name
      if (firstName.value.trim() === "") {
        showError(firstName, "First name is required")
        hasError = true
      } else if (!/^[a-zA-Z ]*$/.test(firstName.value.trim())) {
        showError(firstName, "Only letters and white space allowed")
        hasError = true
      }

      // Validate last name
      if (lastName.value.trim() === "") {
        showError(lastName, "Last name is required")
        hasError = true
      } else if (!/^[a-zA-Z ]*$/.test(lastName.value.trim())) {
        showError(lastName, "Only letters and white space allowed")
        hasError = true
      }

      // Validate contact
      if (contact.value.trim() === "") {
        showError(contact, "Contact number is required")
        hasError = true
      } else if (!/^[0-9]{10}$/.test(contact.value.trim())) {
        showError(contact, "Contact must be 10 digits")
        hasError = true
      }

      // Validate email
      if (email.value.trim() === "") {
        showError(email, "Email is required")
        hasError = true
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        showError(email, "Invalid email format")
        hasError = true
      }

      // Validate password
      if (password.value === "") {
        showError(password, "Password is required")
        hasError = true
      } else if (password.value.length < 8) {
        showError(password, "Password must be at least 8 characters")
        hasError = true
      }

      // Validate confirm password
      if (confirmPassword.value === "") {
        showError(confirmPassword, "Please confirm your password")
        hasError = true
      } else if (password.value !== confirmPassword.value) {
        showError(confirmPassword, "Passwords do not match")
        hasError = true
      }

      // Validate event interest
      if (eventInterest.value === "") {
        showError(eventInterest, "Please select an event")
        hasError = true
      }

      // Prevent form submission if there are errors
      if (hasError) {
        event.preventDefault()
      }
    })
  }

  // Client-side validation for login form
  const loginForm = document.getElementById("loginForm")

  if (loginForm) {
    loginForm.addEventListener("submit", (event) => {
      let hasError = false

      // Get form fields
      const email = document.getElementById("email")
      const password = document.getElementById("password")

      // Clear previous error messages
      const errorElements = document.querySelectorAll(".error")
      errorElements.forEach((element) => {
        element.textContent = ""
      })

      // Validate email
      if (email.value.trim() === "") {
        showError(email, "Email is required")
        hasError = true
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        showError(email, "Invalid email format")
        hasError = true
      }

      // Validate password
      if (password.value === "") {
        showError(password, "Password is required")
        hasError = true
      }

      // Prevent form submission if there are errors
      if (hasError) {
        event.preventDefault()
      }
    })
  }

  // Function to show error message
  function showError(input, message) {
    const formGroup = input.parentElement
    let errorElement = formGroup.querySelector(".error")

    if (!errorElement) {
      errorElement = document.createElement("span")
      errorElement.className = "error"
      formGroup.appendChild(errorElement)
    }

    errorElement.textContent = message
  }

  // Slider functionality
  const slides = document.querySelectorAll(".slide")
  const navItems = document.querySelectorAll(".slider-nav-item")
  const prevBtn = document.querySelector(".slider-arrow-left")
  const nextBtn = document.querySelector(".slider-arrow-right")
  let currentSlide = 0

  if (slides.length > 0) {
    // Function to show a specific slide
    function showSlide(index) {
      // Hide all slides
      slides.forEach((slide) => {
        slide.classList.remove("active")
      })

      // Remove active class from all nav items
      navItems.forEach((item) => {
        item.classList.remove("active")
      })

      // Show the selected slide
      slides[index].classList.add("active")
      navItems[index].classList.add("active")

      // Update current slide index
      currentSlide = index
    }

    // Event listeners for nav items
    navItems.forEach((item, index) => {
      item.addEventListener("click", () => {
        showSlide(index)
      })
    })

    // Event listeners for prev/next buttons
    if (prevBtn && nextBtn) {
      prevBtn.addEventListener("click", () => {
        let index = currentSlide - 1
        if (index < 0) {
          index = slides.length - 1
        }
        showSlide(index)
      })

      nextBtn.addEventListener("click", () => {
        let index = currentSlide + 1
        if (index >= slides.length) {
          index = 0
        }
        showSlide(index)
      })
    }

    // Auto slide
    setInterval(() => {
      let index = currentSlide + 1
      if (index >= slides.length) {
        index = 0
      }
      showSlide(index)
    }, 5000)
  }
})


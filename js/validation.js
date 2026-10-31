/**
 * =====================================================================
 * CAMPUS MANAGEMENT PORTAL - FRONTEND FORM VALIDATION ENGINE
 * Item 5: Registration Form with Frontend Validation & Friendly Errors
 * =====================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  const regForm = document.getElementById('registration-form');
  if (!regForm) return;

  const fields = {
    fullName: document.getElementById('full_name'),
    email: document.getElementById('email'),
    rollNumber: document.getElementById('roll_number'),
    phone: document.getElementById('phone'),
    password: document.getElementById('password'),
    confirmPassword: document.getElementById('confirm_password'),
    terms: document.getElementById('terms_agree')
  };

  const strengthBar = document.getElementById('strength-bar');
  const strengthText = document.getElementById('strength-text');

  // Real-time validation listeners on input and blur
  if (fields.fullName) {
    fields.fullName.addEventListener('input', () => validateFullName());
    fields.fullName.addEventListener('blur', () => validateFullName());
  }

  if (fields.email) {
    fields.email.addEventListener('input', () => validateEmail());
    fields.email.addEventListener('blur', () => validateEmail());
  }

  if (fields.rollNumber) {
    fields.rollNumber.addEventListener('input', () => validateRollNumber());
    fields.rollNumber.addEventListener('blur', () => validateRollNumber());
  }

  if (fields.phone) {
    fields.phone.addEventListener('input', () => validatePhone());
    fields.phone.addEventListener('blur', () => validatePhone());
  }

  if (fields.password) {
    fields.password.addEventListener('input', () => {
      validatePassword();
      if (fields.confirmPassword && fields.confirmPassword.value) {
        validateConfirmPassword();
      }
    });
    fields.password.addEventListener('blur', () => validatePassword());
  }

  if (fields.confirmPassword) {
    fields.confirmPassword.addEventListener('input', () => validateConfirmPassword());
    fields.confirmPassword.addEventListener('blur', () => validateConfirmPassword());
  }

  if (fields.terms) {
    fields.terms.addEventListener('change', () => validateTerms());
  }

  // Master Form Submit Handler
  regForm.addEventListener('submit', (e) => {
    const isNameValid = validateFullName();
    const isEmailValid = validateEmail();
    const isRollValid = validateRollNumber();
    const isPhoneValid = validatePhone();
    const isPassValid = validatePassword();
    const isConfirmValid = validateConfirmPassword();
    const isTermsValid = validateTerms();

    const isFormValid = isNameValid && isEmailValid && isRollValid && isPhoneValid && isPassValid && isConfirmValid && isTermsValid;

    if (!isFormValid) {
      e.preventDefault();
      
      // Trigger subtle shake animation on card
      regForm.closest('.auth-card')?.classList.add('shake');
      setTimeout(() => {
        regForm.closest('.auth-card')?.classList.remove('shake');
      }, 500);

      // Focus on first invalid input
      const firstInvalid = regForm.querySelector('.is-invalid');
      if (firstInvalid) {
        firstInvalid.focus();
      }

      if (window.showToast) {
        window.showToast('Please correct the highlighted errors before submitting.', 'danger');
      }
    }
  });

  // Individual Validator Functions
  function validateFullName() {
    const el = fields.fullName;
    if (!el) return true;
    const val = el.value.trim();
    const nameRegex = /^[a-zA-Z\s.'-]{3,60}$/;

    if (!val) {
      setError(el, 'Full name is required.');
      return false;
    } else if (!nameRegex.test(val)) {
      setError(el, 'Please enter a valid name (at least 3 alphabet characters).');
      return false;
    }
    setSuccess(el);
    return true;
  }

  function validateEmail() {
    const el = fields.email;
    if (!el) return true;
    const val = el.value.trim();
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!val) {
      setError(el, 'Email address is required.');
      return false;
    } else if (!emailRegex.test(val)) {
      setError(el, 'Please enter a valid email address (e.g. name@campus.edu).');
      return false;
    }
    setSuccess(el);
    return true;
  }

  function validateRollNumber() {
    const el = fields.rollNumber;
    if (!el) return true;
    const val = el.value.trim();
    const rollRegex = /^[A-Z0-9-]{4,20}$/i;

    if (!val) {
      setError(el, 'Student ID / Roll Number is required.');
      return false;
    } else if (!rollRegex.test(val)) {
      setError(el, 'Roll number must be alphanumeric (e.g. CS-2024-001).');
      return false;
    }
    setSuccess(el);
    return true;
  }

  function validatePhone() {
    const el = fields.phone;
    if (!el) return true;
    const val = el.value.trim();
    const phoneRegex = /^(\+?\d{1,3}[- ]?)?\d{10}$/;

    if (!val) {
      setError(el, 'Contact phone number is required.');
      return false;
    } else if (!phoneRegex.test(val.replace(/\s+/g, ''))) {
      setError(el, 'Please provide a valid 10-digit phone number.');
      return false;
    }
    setSuccess(el);
    return true;
  }

  function validatePassword() {
    const el = fields.password;
    if (!el) return true;
    const val = el.value;
    const strength = calculatePasswordStrength(val);

    updateStrengthUI(strength);

    if (!val) {
      setError(el, 'Password is required.');
      return false;
    } else if (val.length < 8) {
      setError(el, 'Password must be at least 8 characters long.');
      return false;
    } else if (strength.score < 3) {
      setError(el, 'Password must include uppercase, lowercase, number, and special character.');
      return false;
    }

    setSuccess(el);
    return true;
  }

  function validateConfirmPassword() {
    const el = fields.confirmPassword;
    const passEl = fields.password;
    if (!el || !passEl) return true;

    if (!el.value) {
      setError(el, 'Please confirm your password.');
      return false;
    } else if (el.value !== passEl.value) {
      setError(el, 'Passwords do not match.');
      return false;
    }
    setSuccess(el);
    return true;
  }

  function validateTerms() {
    const el = fields.terms;
    if (!el) return true;

    if (!el.checked) {
      setError(el, 'You must accept the academic code of conduct.');
      return false;
    }
    setSuccess(el);
    return true;
  }

  // Password Strength Calculator
  function calculatePasswordStrength(password) {
    let score = 0;
    if (!password) return { score: 0, label: '', color: '', percent: 0 };

    if (password.length >= 8) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(password)) score++;
    if (password.length >= 12) score++;

    let label = 'Weak';
    let color = '#ef4444';
    let percent = 25;

    if (score >= 4) {
      label = 'Strong';
      color = '#10b981';
      percent = 100;
    } else if (score >= 3) {
      label = 'Good';
      color = '#0284c7';
      percent = 70;
    } else if (score >= 2) {
      label = 'Moderate';
      color = '#f59e0b';
      percent = 45;
    }

    return { score, label, color, percent };
  }

  function updateStrengthUI(strength) {
    if (!strengthBar || !strengthText) return;
    strengthBar.style.width = `${strength.percent}%`;
    strengthBar.style.backgroundColor = strength.color;
    strengthText.textContent = strength.label ? `Strength: ${strength.label}` : '';
    strengthText.style.color = strength.color;
  }

  // Helpers to apply validation states
  function setError(input, message) {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');
    input.setAttribute('aria-invalid', 'true');

    const feedback = input.parentElement.querySelector('.invalid-feedback');
    if (feedback) {
      feedback.textContent = message;
      feedback.style.display = 'block';
    }
  }

  function setSuccess(input) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    input.setAttribute('aria-invalid', 'false');

    const feedback = input.parentElement.querySelector('.invalid-feedback');
    if (feedback) {
      feedback.textContent = '';
      feedback.style.display = 'none';
    }
  }
});

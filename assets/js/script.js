const togglePassword = (inputId, toggleElement) => {
    const input = document.querySelector(`input[name="${inputId}"]`);
    if (input) {
        if (input.type === "password") {
            input.type = "text";
            toggleElement.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
        } else {
            input.type = "password";
            toggleElement.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        }
    }
};

const showError = (input, message) => {
    const wrapper = input.closest('.form-group') || input.closest('.input-wrapper');
    const errorDisplay = wrapper ? wrapper.querySelector('.error-message') : null;
    if (errorDisplay) {
        errorDisplay.innerText = message;
        errorDisplay.style.display = 'block';
        input.classList.add('input-error');
    }
};

const clearError = (input) => {
    const wrapper = input.closest('.form-group') || input.closest('.input-wrapper');
    const errorDisplay = wrapper ? wrapper.querySelector('.error-message') : null;
    if (errorDisplay) {
        errorDisplay.innerText = '';
        errorDisplay.style.display = 'none';
        input.classList.remove('input-error');
    }
};

const clearAllErrors = (form) => {
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => clearError(input));

    const globalError = form.querySelector('[id$="GlobalError"]');
    if (globalError) {
        globalError.style.display = 'none';
        globalError.innerText = '';
    }
    const globalAlert = form.querySelector('.global-alert');
    if (globalAlert) {
        globalAlert.style.display = 'none';
    }
};

const showGlobalError = (form, message) => {
    const globalError = form.querySelector('[id$="GlobalError"]');
    if (globalError) {
        globalError.innerText = message;
        globalError.style.display = 'block';
    }
};

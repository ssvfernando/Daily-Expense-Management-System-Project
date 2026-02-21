document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                clearError(input);
                const globalError = registerForm.querySelector('#registerGlobalError');
                if (globalError) globalError.style.display = 'none';
            });
        });

        registerForm.addEventListener('submit', (e) => {
            let isValid = true;
            clearAllErrors(registerForm);

            const emailInput = registerForm.querySelector('input[name="email"]');
            const usernameInput = registerForm.querySelector('input[name="username"]');
            const passwordInput = registerForm.querySelector('input[name="password"]');
            const confirmPasswordInput = registerForm.querySelector('input[name="confirm_password"]');

            const emailVal = emailInput.value.trim();
            const uVal = usernameInput.value.trim();
            const pVal = passwordInput.value.trim();
            const cpVal = confirmPasswordInput.value.trim();

            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            const nameRegex = /^[A-Za-z\s]+$/;
            const usernameRegex = /^[a-zA-Z0-9]+$/;

            if (emailVal === "" && uVal === "" && pVal === "" && cpVal === "") {
                showGlobalError(registerForm, "All fields are required!");
                isValid = false;
            } else {
                if (emailVal === "") {
                    showError(emailInput, "Email is required");
                    isValid = false;
                } else if (!emailRegex.test(emailVal)) {
                    showError(emailInput, "Invalid email format");
                    isValid = false;
                }

                if (uVal === "") {
                    showError(usernameInput, "Please enter a username");
                    isValid = false;
                } else if (uVal.length < 3) {
                    showError(usernameInput, "Username must be at least 3 characters");
                    isValid = false;
                } else if (!usernameRegex.test(uVal)) {
                    showError(usernameInput, "Username can only contain letters and numbers");
                    isValid = false;
                }

                if (pVal === "") {
                    showError(passwordInput, "Please enter a password");
                    isValid = false;
                } else if (pVal.length < 6) {
                    showError(passwordInput, "Password must be at least 6 characters");
                    isValid = false;
                }

                if (cpVal === "") {
                    showError(confirmPasswordInput, "Please confirm your password");
                    isValid = false;
                } else if (cpVal !== pVal) {
                    showError(confirmPasswordInput, "Passwords do not match");
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

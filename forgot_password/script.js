document.addEventListener('DOMContentLoaded', () => {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {

        forgotPasswordForm.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                clearError(input);
                const globalError = forgotPasswordForm.querySelector('.global-alert');
                if (globalError) globalError.style.display = 'none';
            });
        });

        forgotPasswordForm.addEventListener('submit', (e) => {
            let isValid = true;
            clearAllErrors(forgotPasswordForm);

            const usernameInput = forgotPasswordForm.querySelector('input[name="username"]');
            const emailInput = forgotPasswordForm.querySelector('input[name="email"]');

            const uVal = usernameInput.value.trim();
            const emailVal = emailInput.value.trim();
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            if (uVal === "" && emailVal === "") {
                let globalError = forgotPasswordForm.querySelector('.global-alert');
                if (!globalError) {
                    globalError = document.createElement('div');
                    globalError.className = 'global-alert';
                    forgotPasswordForm.insertBefore(globalError, forgotPasswordForm.firstChild);
                }
                globalError.innerText = "All fields are required!";
                globalError.style.display = 'block';
                isValid = false;
            } else {
                const globalError = forgotPasswordForm.querySelector('.global-alert');
                if (globalError) globalError.style.display = 'none';

                if (uVal === "") {
                    showError(usernameInput, "Username is required");
                    isValid = false;
                }

                if (emailVal === "") {
                    showError(emailInput, "Email is required");
                    isValid = false;
                } else if (!emailRegex.test(emailVal)) {
                    showError(emailInput, "Invalid email format");
                    isValid = false;
                }
            }

            if (!isValid) e.preventDefault();
        });
    }

    const resetPasswordForm = document.getElementById('resetPasswordForm');
    if (resetPasswordForm) {
        resetPasswordForm.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => clearError(input));
        });

        resetPasswordForm.addEventListener('submit', (e) => {
            let isValid = true;
            clearAllErrors(resetPasswordForm);

            const passwordInput = resetPasswordForm.querySelector('input[name="password"]');
            const confirmPasswordInput = resetPasswordForm.querySelector('input[name="confirm_password"]');

            const pVal = passwordInput.value.trim();
            const cpVal = confirmPasswordInput.value.trim();

            if (pVal === "") {
                showError(passwordInput, "New password is required");
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

            if (!isValid) e.preventDefault();
        });
    }
});

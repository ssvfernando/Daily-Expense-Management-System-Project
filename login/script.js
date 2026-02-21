document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                clearError(input);
                const globalError = loginForm.querySelector('#loginGlobalError');
                if (globalError) globalError.style.display = 'none';
            });
        });

        loginForm.addEventListener('submit', (e) => {
            let isValid = true;
            clearAllErrors(loginForm);

            const usernameInput = loginForm.querySelector('input[name="username"]');
            const passwordInput = loginForm.querySelector('input[name="password"]');

            if (usernameInput.value.trim() === "" && passwordInput.value.trim() === "") {
                const globalError = loginForm.querySelector('#loginGlobalError');
                if (globalError) {
                    globalError.innerText = "All fields are required!";
                    globalError.style.display = 'block';
                }
                isValid = false;
            } else {
                if (usernameInput.value.trim() === "") {
                    showError(usernameInput, "Please enter your username");
                    isValid = false;
                }

                if (passwordInput.value.trim() === "") {
                    showError(passwordInput, "Please enter your password");
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

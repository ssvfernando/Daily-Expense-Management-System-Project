document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById("editModal");
    const closeBtn = document.querySelector(".close-modal");
    const editBtns = document.querySelectorAll(".edit-btn");

    editBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.getAttribute("data-id");
            const item = btn.getAttribute("data-item");
            const cost = btn.getAttribute("data-cost");
            const date = btn.getAttribute("data-date");
            const category = btn.getAttribute("data-category");

            document.getElementById("edit_id").value = id;
            document.getElementById("edit_item").value = item;
            document.getElementById("edit_cost").value = cost;
            document.getElementById("edit_date").value = date;
            document.getElementById("edit_category").value = category;

            modal.style.display = "flex";
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    const updateForm = document.querySelector('#updateForm');
    if (updateForm) {
        updateForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => clearError(input));
            input.addEventListener('change', () => clearError(input));
        });

        updateForm.addEventListener('submit', (e) => {
            const itemInput = updateForm.querySelector('input[name="item"]');
            const costInput = updateForm.querySelector('input[name="cost"]');
            const dateInput = updateForm.querySelector('input[name="date"]');
            const catSelect = updateForm.querySelector('select[name="category_id"]');

            const itemVal = itemInput.value.trim();
            const costVal = costInput.value;
            const dateVal = dateInput.value;
            const catVal = catSelect.value;

            clearAllErrors(updateForm);

            if (catVal === "" && itemVal === "" && costVal === "") {
                alert("All fields are required!");
                e.preventDefault();
                return;
            }

            if (costVal !== "" && parseFloat(costVal) < 0) {
                alert("Cost cannot be negative!");
                e.preventDefault();
                return;
            }

            let isValid = true;

            if (catVal === "") {
                showError(catSelect, "Select category");
                isValid = false;
            }

            if (itemVal === "") {
                showError(itemInput, "Description required");
                isValid = false;
            }

            if (costVal === "") {
                showError(costInput, "Cost required");
                isValid = false;
            } else if (parseFloat(costVal) === 0) {
                showError(costInput, "Cost must be > 0");
                isValid = false;
            }

            if (dateVal === "") {
                showError(dateInput, "Date required");
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return;
            }

            if (!confirm('Are you sure you want to update this transaction?')) {
                e.preventDefault();
            }
        });
    }

    const canvas = document.getElementById('expenseChart');
    if (canvas && typeof chartLabels !== 'undefined' && chartLabels.length > 0) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Expenses by Category',
                    data: chartData,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false,
                        text: 'Spending Distribution'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += 'Rs. ' + context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else if (canvas) {
        const noDataMsg = document.getElementById('noDataMessage');
        if (noDataMsg) {
            canvas.style.display = 'none';
            noDataMsg.style.display = 'block';
        }
    }

    const expenseForm = document.querySelector('#expenseForm');
    if (expenseForm) {
        expenseForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => clearError(input));
            input.addEventListener('change', () => clearError(input));
        });

        expenseForm.addEventListener('submit', (e) => {
            const catSelect = expenseForm.querySelector('select[name="category_id"]');
            const itemInput = expenseForm.querySelector('input[name="item"]');
            const costInput = expenseForm.querySelector('input[name="cost"]');
            const dateInput = expenseForm.querySelector('input[name="date"]');

            const catVal = catSelect.value;
            const itemVal = itemInput.value.trim();
            const costVal = costInput.value;
            const dateVal = dateInput.value;

            clearAllErrors(expenseForm);

            if (catVal === "" && itemVal === "" && costVal === "") {
                alert("All fields are required!");
                e.preventDefault();
                return;
            }

            if (costVal !== "" && parseFloat(costVal) < 0) {
                alert("Cost cannot be negative!");
                e.preventDefault();
                return;
            }

            let isValid = true;

            if (catVal === "") {
                showError(catSelect, "Select category");
                isValid = false;
            }

            if (itemVal === "") {
                showError(itemInput, "Description required");
                isValid = false;
            }

            if (costVal === "") {
                showError(costInput, "Cost required");
                isValid = false;
            } else if (parseFloat(costVal) === 0) {
                showError(costInput, "Cost must be > 0");
                isValid = false;
            }

            if (dateVal === "") {
                showError(dateInput, "Date required");
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    const addCategoryForm = document.getElementById('addCategoryForm');
    if (addCategoryForm) {
        const catInput = addCategoryForm.querySelector('input[name="new_cat_name"]');
        const errorSpan = addCategoryForm.querySelector('.error-message');

        catInput.addEventListener('input', () => {
            clearError(catInput);
        });

        addCategoryForm.addEventListener('submit', (e) => {
            clearError(catInput);
            if (catInput.value.trim() === "") {
                e.preventDefault();
                showError(catInput, "Please enter a category name");
            }
        });

        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.get('msg') === 'cat_exists') {
            showError(catInput, "Category already exists!");
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({
                path: newUrl
            }, '', newUrl);
        }

        if (urlParams.get('msg') === 'cat_in_use') {
            setTimeout(() => {
                alert("Cannot delete category. It is being used by one or more expenses.");
            }, 10);
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({
                path: newUrl
            }, '', newUrl);
        }
    }
});

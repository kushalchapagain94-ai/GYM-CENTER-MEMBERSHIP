document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("form[data-confirm]");
    forms.forEach(form => {
        form.addEventListener("submit", e => {
            if (!confirm(form.dataset.confirm)) e.preventDefault();
        });
    });
});

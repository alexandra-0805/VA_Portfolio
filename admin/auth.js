document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const input = document.getElementById(button.dataset.passwordToggle);
        const icon = button.querySelector('i');

        if (!input) {
            return;
        }

        button.addEventListener('click', () => {
            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
            button.setAttribute('title', isVisible ? 'Show password' : 'Hide password');
            icon?.classList.toggle('bi-eye', isVisible);
            icon?.classList.toggle('bi-eye-slash', !isVisible);
        });
    });
});

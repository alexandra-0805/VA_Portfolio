/**
 * Appearance settings: theme (light / dark / auto), accent colour and text size.
 * Saved per browser in localStorage. partials/head.php applies them before the page
 * draws; this file handles the sun/moon toggles and the admin Settings page.
 */
(() => {
	const root = document.documentElement;
	const OPTIONS = {
		theme: { key: 'va-theme', values: ['light', 'dark', 'auto'], fallback: 'auto' },
		accent: { key: 'va-accent', values: ['coral', 'teal', 'plum', 'blue'], fallback: 'coral' },
		text: { key: 'va-text', values: ['sm', 'md', 'lg'], fallback: 'md' },
	};
	const darkQuery = window.matchMedia('(prefers-color-scheme: dark)');
	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	const read = (name) => {
		const { key, values, fallback } = OPTIONS[name];
		try {
			const value = localStorage.getItem(key);
			return values.includes(value) ? value : fallback;
		} catch (error) {
			return fallback;
		}
	};

	const write = (name, value) => {
		try {
			localStorage.setItem(OPTIONS[name].key, value);
		} catch (error) {
			// Private browsing: the choice still applies to this page.
		}
	};

	const isDark = () => {
		const theme = read('theme');
		return theme === 'dark' || (theme === 'auto' && darkQuery.matches);
	};

	const syncToggles = () => {
		const dark = root.dataset.theme === 'dark';
		document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
			button.setAttribute('aria-pressed', String(dark));
			const label = dark ? 'Switch to light mode' : 'Switch to dark mode';
			button.setAttribute('aria-label', label);
			button.title = label;
		});
		document.querySelector('meta[name="theme-color"]')?.setAttribute('content', dark ? '#252d2a' : '#fffdf8');
	};

	const apply = ({ animate = false } = {}) => {
		if (animate && !reduceMotion) {
			root.classList.add('theme-switching');
			window.setTimeout(() => root.classList.remove('theme-switching'), 450);
		}
		const dark = isDark();
		root.dataset.theme = dark ? 'dark' : 'light';
		root.dataset.bsTheme = dark ? 'dark' : 'light';
		root.dataset.accent = read('accent');
		root.dataset.text = read('text');
		syncToggles();
	};

	/* Sun / moon buttons: switch between light and dark. */
	document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
		button.addEventListener('click', () => {
			write('theme', root.dataset.theme === 'dark' ? 'light' : 'dark');
			apply({ animate: true });
			syncSettingsForm();
		});
	});

	/* Following the device setting while the theme is "auto". */
	darkQuery.addEventListener('change', () => {
		if (read('theme') === 'auto') {
			apply({ animate: true });
		}
	});

	/* Another tab changed a setting: follow it here too. */
	window.addEventListener('storage', (event) => {
		if (Object.values(OPTIONS).some((option) => option.key === event.key)) {
			apply();
			syncSettingsForm();
		}
	});

	/* Admin Settings page ------------------------------------------------- */
	const form = document.querySelector('[data-settings-form]');
	const status = form?.querySelector('[data-settings-status]');
	let statusTimer = null;

	function syncSettingsForm() {
		if (!form) {
			return;
		}
		Object.keys(OPTIONS).forEach((name) => {
			const input = form.querySelector(`input[name="${name}"][value="${read(name)}"]`);
			if (input) {
				input.checked = true;
			}
		});
	}

	const showSaved = (message) => {
		if (!status) {
			return;
		}
		status.textContent = message;
		status.classList.add('is-visible');
		window.clearTimeout(statusTimer);
		statusTimer = window.setTimeout(() => status.classList.remove('is-visible'), 1800);
	};

	if (form) {
		syncSettingsForm();

		form.addEventListener('change', (event) => {
			const input = event.target;
			if (!(input instanceof HTMLInputElement) || !OPTIONS[input.name]) {
				return;
			}
			write(input.name, input.value);
			apply({ animate: input.name === 'theme' });
			showSaved('Saved');
		});

		form.addEventListener('submit', (event) => event.preventDefault());

		form.querySelector('[data-settings-reset]')?.addEventListener('click', () => {
			Object.values(OPTIONS).forEach(({ key }) => {
				try {
					localStorage.removeItem(key);
				} catch (error) {
					// Nothing stored.
				}
			});
			apply({ animate: true });
			syncSettingsForm();
			showSaved('Back to defaults');
		});
	}

	syncToggles();
})();

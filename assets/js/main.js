/**
 * Shared behavior for the public site and the admin panel.
 */
document.addEventListener('DOMContentLoaded', () => {
	// Sticky nav shadow + back-to-top button.
	const topButton = document.getElementById('backToTop');
	const siteNav = document.querySelector('.site-nav.fixed-top');
	const onScroll = () => {
		topButton?.classList.toggle('show', window.scrollY > 500);
		siteNav?.classList.toggle('is-scrolled', window.scrollY > 12);
	};

	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });
	topButton?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

	// Close the mobile menu after choosing a link.
	const mainNav = document.getElementById('mainNav');
	mainNav?.querySelectorAll('a').forEach((link) => {
		link.addEventListener('click', () => {
			if (mainNav.classList.contains('show') && window.bootstrap?.Collapse) {
				window.bootstrap.Collapse.getOrCreateInstance(mainNav).hide();
			}
		});
	});

	// Reveal-on-scroll animations.
	const observer = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				entry.target.classList.add('visible');
				observer.unobserve(entry.target);
			}
		});
	}, { threshold: 0.12 });
	document.querySelectorAll('.animate').forEach((element) => observer.observe(element));

	// Bootstrap-style client-side form validation.
	document.querySelectorAll('form[data-validate]').forEach((form) => {
		form.addEventListener('submit', (event) => {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}
			form.classList.add('was-validated');
		});
	});

	// Confirmation prompts (delete buttons) and print buttons.
	document.querySelectorAll('[data-confirm]').forEach((element) => {
		element.addEventListener('click', (event) => {
			if (!window.confirm(element.dataset.confirm)) {
				event.preventDefault();
			}
		});
	});
	document.querySelectorAll('[data-print]').forEach((button) => button.addEventListener('click', () => window.print()));

	// Certificate preview dialog (About page).
	const viewer = document.getElementById('certificateViewer');
	const viewerImage = document.getElementById('certificateViewerImage');
	const viewerTitle = document.getElementById('certificateViewerTitle');
	let lastFocused = null;

	const closeViewer = () => {
		if (!viewer || viewer.hidden) {
			return;
		}
		viewer.hidden = true;
		document.body.classList.remove('certificate-viewer-open');
		viewerImage?.removeAttribute('src');
		lastFocused?.focus();
	};

	const openCertificate = (card) => {
		const { certificateImage: image, certificateLink: link, certificateTitle: title } = card.dataset;

		// No preview image: open the certificate link instead of an empty dialog.
		if (!image) {
			if (link) {
				window.open(link, '_blank', 'noopener');
			}
			return;
		}

		if (!viewer || !viewerImage) {
			return;
		}

		lastFocused = card;
		viewerTitle.textContent = title || 'Certificate preview';
		viewerImage.src = image;
		viewerImage.alt = title || 'Certificate preview';
		viewer.hidden = false;
		document.body.classList.add('certificate-viewer-open');
		viewer.querySelector('[data-certificate-close].certificate-viewer-close')?.focus();
	};

	document.querySelectorAll('[data-certificate-image]').forEach((card) => {
		card.addEventListener('click', () => openCertificate(card));
		card.addEventListener('keydown', (event) => {
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				openCertificate(card);
			}
		});
	});
	document.querySelectorAll('[data-certificate-close]').forEach((element) => element.addEventListener('click', closeViewer));
	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeViewer();
		}
	});
});

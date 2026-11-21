/**
 * Motion layer for every page (styles in assets/css/motion.css):
 *  - smooth page-to-page transitions (fallback for browsers without View Transitions)
 *  - cursor highlight + card spotlight (mouse devices only)
 *  - word-by-word headline, highlighted words, scroll reveals, icon pops
 *  - button ripples and a scroll progress bar
 * Respects "reduce motion" and never hides content if anything fails.
 */
(() => {
	const root = document.documentElement;
	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

	/* Scroll progress bar ------------------------------------------------- */
	const progress = document.createElement('div');
	progress.className = 'scroll-progress';
	progress.setAttribute('aria-hidden', 'true');
	document.body.append(progress);

	let progressQueued = false;
	const updateProgress = () => {
		progressQueued = false;
		const scrollable = root.scrollHeight - window.innerHeight;
		progress.style.transform = `scaleX(${scrollable > 0 ? Math.min(1, window.scrollY / scrollable) : 0})`;
	};
	const queueProgress = () => {
		if (!progressQueued) {
			progressQueued = true;
			requestAnimationFrame(updateProgress);
		}
	};
	window.addEventListener('scroll', queueProgress, { passive: true });
	window.addEventListener('resize', queueProgress);
	updateProgress();

	/* Button ripple (mouse and touch) -------------------------------------- */
	document.addEventListener('pointerdown', (event) => {
		const button = event.target.closest('.btn');
		if (!button || reduceMotion) {
			return;
		}
		const rect = button.getBoundingClientRect();
		const ripple = document.createElement('span');
		ripple.className = 'fx-ripple';
		ripple.style.setProperty('--size', `${Math.max(rect.width, rect.height) * 2.2}px`);
		ripple.style.left = `${event.clientX - rect.left}px`;
		ripple.style.top = `${event.clientY - rect.top}px`;
		button.append(ripple);
		ripple.addEventListener('animationend', () => ripple.remove());
	});

	if (reduceMotion) {
		return;
	}

	/* Page transitions (only where the browser has no View Transitions) ---- */
	if (root.classList.contains('no-vt')) {
		document.addEventListener('click', (event) => {
			const link = event.target.closest('a[href]');
			if (
				!link
				|| event.defaultPrevented
				|| event.button !== 0
				|| event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
				|| (link.target && link.target !== '_self')
				|| link.hasAttribute('download')
				|| link.origin !== window.location.origin
				|| (link.pathname === window.location.pathname && link.search === window.location.search && link.hash)
			) {
				return;
			}
			event.preventDefault();
			root.classList.add('page-leaving');
			window.setTimeout(() => { window.location.href = link.href; }, 220);
		});
		// Coming back with the browser's Back button shows the page again.
		window.addEventListener('pageshow', (event) => {
			if (event.persisted) {
				root.classList.remove('page-leaving');
			}
		});
	}

	/* Main heading: split into words that rise in one by one -------------- */
	const splitWords = (heading) => {
		let index = 0;
		const walk = (node) => {
			[...node.childNodes].forEach((child) => {
				if (child.nodeType === Node.TEXT_NODE) {
					if (!child.textContent.trim()) {
						return;
					}
					const fragment = document.createDocumentFragment();
					child.textContent.split(/(\s+)/).forEach((part) => {
						if (!part) {
							return;
						}
						if (!part.trim()) {
							fragment.append(part);
							return;
						}
						const word = document.createElement('span');
						word.className = 'word';
						word.style.setProperty('--i', index++);
						word.textContent = part;
						fragment.append(word);
					});
					child.replaceWith(fragment);
				} else if (child.nodeType === Node.ELEMENT_NODE && !child.classList.contains('bi')) {
					walk(child);
				}
			});
		};
		walk(heading);
		heading.classList.add('words-split');
		return index;
	};

	const markWords = (title, delay) => {
		title.querySelectorAll(':scope > span:not(.word), :scope > em').forEach((mark) => {
			mark.style.setProperty('--mark-delay', `${delay}ms`);
			mark.classList.add('fx-mark');
		});
	};

	document.querySelectorAll('main h1').forEach((heading) => {
		const words = splitWords(heading);
		markWords(heading, words * 65 + 450);
	});

	/* Scroll reveal for cards, headings, icons and stars ------------------- */
	const scope = document.querySelectorAll('main, .site-footer');
	const pick = (selector) => [...scope].flatMap((area) => [...area.querySelectorAll(selector)]);

	const cardSelector = [
		'.service-card', '.project-card', '.feedback-card', '.certificate-card', '.skill-card',
		'.process-step', '.soft-panel', '.resume-section', '.empty-state', '.cta-card',
		'.section-lead', '.about-intro', '.about-lead', '.contact-links a', '.service-intro',
		'.project-detail-image', '.footer-grid > *', 'main h2:not(.section-title)',
	].join(',');

	let cards = pick(cardSelector).filter((element) => !element.closest('.animate, .hero, .portfolio-chatbot'));
	// Animate the outer card only, not cards nested inside it.
	cards = cards.filter((element) => !cards.some((other) => other !== element && other.contains(element)));

	const targets = [
		...cards.map((element) => [element, 'card']),
		...pick('.section-kicker, h2.section-title')
			.filter((element) => !element.closest('.hero'))
			.map((element) => [element, 'heading']),
		...pick('.service-icon, .process-icon, .feature-panel > i, .contact-links i, .resume-entry-icon, .dashboard-stat-icon, .analytics-panel-icon, .social-links a')
			.map((element) => [element, 'icon']),
		...pick('.rating-stars').map((element) => [element, 'stars']),
	];

	const foldLine = window.innerHeight * 0.92;
	const typeOf = new Map(targets);

	const reveal = (element, delay) => {
		const type = typeOf.get(element);
		element.style.setProperty('--fx-delay', `${delay}ms`);
		element.classList.remove('fx-wait');
		element.classList.add('fx-in');

		if (type === 'heading' && element.matches('h2.section-title')) {
			markWords(element, delay + 450);
		}
		if (type === 'icon') {
			// Stop the entrance animation once played, so hover animations never replay it.
			element.addEventListener('animationend', (event) => {
				if (event.target === element) {
					element.classList.remove('fx-in');
				}
			}, { once: true });
		}
	};

	const observer = new IntersectionObserver((entries) => {
		let batch = 0;
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			}
			observer.unobserve(entry.target);
			reveal(entry.target, Math.min(batch++, 6) * 80);
		});
	}, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

	targets.forEach(([element, type]) => {
		if (type === 'stars') {
			element.classList.add('fx-stars');
			[...element.children].forEach((star, index) => star.style.setProperty('--i', index));
		} else if (type === 'icon') {
			element.classList.add('fx-icon');
		}

		// Things already on screen at load animate right away; the rest wait for scrolling.
		if (element.getBoundingClientRect().top < foldLine) {
			reveal(element, 0);
			return;
		}
		if (type !== 'stars') {
			element.classList.add('fx-wait');
		}
		observer.observe(element);
	});

	/* Cursor highlight + card spotlight (mouse devices only) --------------- */
	if (!finePointer) {
		return;
	}

	root.classList.add('cursor-fx');
	const ring = document.createElement('div');
	const glow = document.createElement('div');
	ring.className = 'cursor-ring';
	glow.className = 'cursor-glow';
	ring.setAttribute('aria-hidden', 'true');
	glow.setAttribute('aria-hidden', 'true');
	document.body.append(glow, ring);

	const interactive = 'a, button, [role="button"], label, select, summary, .certificate-card, input[type="radio"], input[type="checkbox"], input[type="submit"]';
	const textField = 'input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]), textarea, [contenteditable="true"]';
	const pointer = { x: -100, y: -100 };
	const ringPos = { x: -100, y: -100 };
	const glowPos = { x: -100, y: -100 };
	let frame = null;

	const follow = () => {
		ringPos.x += (pointer.x - ringPos.x) * 0.24;
		ringPos.y += (pointer.y - ringPos.y) * 0.24;
		glowPos.x += (pointer.x - glowPos.x) * 0.09;
		glowPos.y += (pointer.y - glowPos.y) * 0.09;
		ring.style.transform = `translate3d(${ringPos.x}px, ${ringPos.y}px, 0)`;
		glow.style.transform = `translate3d(${glowPos.x}px, ${glowPos.y}px, 0)`;

		const moving = Math.abs(pointer.x - glowPos.x) + Math.abs(pointer.y - glowPos.y) > 0.4;
		frame = moving ? requestAnimationFrame(follow) : null;
	};

	const spotCards = document.querySelectorAll('.service-card, .project-card, .feedback-card, .certificate-card, .skill-card, .process-step, .feature-panel, .dashboard-stat-card, .rating-summary, .resume-section');
	spotCards.forEach((card) => {
		const light = document.createElement('span');
		light.className = 'fx-spot-glow';
		light.setAttribute('aria-hidden', 'true');
		card.classList.add('fx-spot');
		card.append(light);
	});

	const hero = document.querySelector('.hero');

	document.addEventListener('pointermove', (event) => {
		if (event.pointerType !== 'mouse') {
			return;
		}
		pointer.x = event.clientX;
		pointer.y = event.clientY;

		if (!root.classList.contains('cursor-visible')) {
			// Jump straight to the mouse the first time, instead of sliding in from a corner.
			Object.assign(ringPos, pointer);
			Object.assign(glowPos, pointer);
			root.classList.add('cursor-visible');
		}

		const target = event.target instanceof Element ? event.target : null;
		root.classList.toggle('cursor-text', Boolean(target?.closest(textField)));
		root.classList.toggle('cursor-hover', Boolean(target?.closest(interactive)) && !target?.closest(textField));

		const card = target?.closest('.fx-spot');
		if (card) {
			const rect = card.getBoundingClientRect();
			card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
			card.style.setProperty('--my', `${event.clientY - rect.top}px`);
		}

		if (hero && target?.closest('.hero')) {
			const rect = hero.getBoundingClientRect();
			hero.style.setProperty('--px', (((event.clientX - rect.left) / rect.width) - 0.5) * 2);
			hero.style.setProperty('--py', (((event.clientY - rect.top) / rect.height) - 0.5) * 2);
		}

		if (!frame) {
			frame = requestAnimationFrame(follow);
		}
	}, { passive: true });

	document.addEventListener('pointerdown', () => root.classList.add('cursor-down'));
	document.addEventListener('pointerup', () => root.classList.remove('cursor-down'));
	document.addEventListener('mouseleave', () => root.classList.remove('cursor-visible'));
	document.documentElement.addEventListener('mouseleave', () => root.classList.remove('cursor-visible'));
	window.addEventListener('blur', () => root.classList.remove('cursor-visible', 'cursor-down'));
})();

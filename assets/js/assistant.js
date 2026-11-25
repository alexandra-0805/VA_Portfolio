/**
 * Rule-based chat assistant. The same widget serves the public site and
 * the admin panel; the `data-chatbot-context` attribute picks the answers.
 *
 * Keep "chat" out of this file's name: InfinityFree answers 403 Forbidden to
 * any URL containing "chat", which silently disables the widget on the live site.
 */
(() => {
	const ANSWERS = {
		site: [
			[/\b(hire|work with|employ|available|contact|email|reach)\b/, 'Yes. Alexandra is interested in Virtual Assistant, administrative support, customer service, data management, and e-commerce support opportunities. Use the Contact page to get in touch.', ['Go to Contact', 'contact.php']],
			[/\b(service|help|offer|assist)\b/, 'Alexandra can assist with email management, data entry, customer support, social media assistance, Canva design, administrative support, and e-commerce support.', ['View Services', 'services.php']],
			[/\b(skill|good at|strength|capabilit)\b/, 'Her skills include customer service, administrative support, data entry and management, communication, organization, time management, problem-solving, attention to detail, social media assistance, Canva design, e-commerce operations, and web development with PHP and MySQL.'],
			[/\b(mentora|recruitment|onboarding|system)\b/, 'Alexandra was the lead developer of Mentora PH, a web-based HR recruitment and onboarding system with AI resume screening, intern training tracking and assessment, messaging, Google sign-in, and PDF/CSV reports. It was built with PHP, MySQL, HTML, CSS, JavaScript, XAMPP, phpMyAdmin, and Git.', ['View Projects', 'projects.php']],
			[/\b(project|work|portfolio|bakeshop|perfume|website)\b/, 'Alexandra has worked on the Mentora PH recruitment system (as lead developer), a Bakeshop Website, a Wooden Perfume Bottle product concept, digital content projects, and a web development portfolio.', ['View Projects', 'projects.php']],
			[/\b(tool|platform|software|canva|shopify|amazon|tiktok|php|mysql|git|xampp)\b/, 'Alexandra uses Canva, Google Workspace, Microsoft Office, and TikTok Shop, and builds web systems with PHP, MySQL, HTML, CSS, JavaScript, XAMPP, phpMyAdmin, and Git. She is also developing her knowledge of Amazon Seller Central and Shopify.'],
			[/\b(stud|education|school|degree|kld|information systems?)\b/, 'Alexandra is taking a Bachelor of Science in Information Systems at Kolehiyo ng Lungsod ng Dasmariñas (KLD).', ['View Resume', 'resume.php']],
			[/\b(experience|background|business|customer service|sales)\b/, 'Alexandra led development of the Mentora PH recruitment and onboarding system, and gained experience running a small business: assisting customers, handling inquiries and transactions, supporting daily operations, keeping records, and helping with basic marketing.', ['View Resume', 'resume.php']],
			[/\b(who|about|alexandra|virtual assistant)\b/, 'Alexandra Concepcion is a Virtual Assistant and Information Systems student with experience in customer service, sales, business operations, administrative tasks, and data management.', ['About Alexandra', 'about.php']],
		],
		admin: [
			[/\b(projects?|work)\b/, 'Open Projects to add, edit, or delete portfolio projects. The form on the left adds or edits; the table on the right lists existing projects.', ['Open Projects', 'admin/projects.php']],
			[/\b(messages?|inbox|contact)\b/, 'Open Messages to review contact form submissions. You can mark messages as read or delete them.', ['Open Messages', 'admin/messages.php']],
			[/\b(services?)\b/, 'Use Services to update the services shown on the public website.', ['Open Services', 'admin/services.php']],
			[/\b(skills?)\b/, 'Use Skills to manage skill names, categories, and proficiency levels.', ['Open Skills', 'admin/skills.php']],
			[/\b(resume|pdf|cv)\b/, 'Open Resume to replace the downloadable PDF used on the public resume page.', ['Open Resume', 'admin/resume.php']],
			[/\b(certificates?|award)\b/, 'Open Certificates to add, edit, or remove certificates shown on the About page.', ['Open Certificates', 'admin/certificates.php']],
			[/\b(reports?|analytics|stats?)\b/, 'Open Reports to filter and print contact activity.', ['Open Reports', 'admin/reports.php']],
			[/\b(sign ?out|log ?out|password|account)\b/, 'Use Sign out in the top-right corner to end your session. If you forget your password, use "Forgot password?" on the sign-in page.'],
		],
	};
	const FALLBACK = {
		site: 'I can help with questions about Alexandra, her services, skills, tools, experience, education, projects, or how to contact her.',
		admin: 'I can guide you through Projects, Services, Skills, Messages, Reports, Certificates, Resume, and signing out.',
	};

	document.addEventListener('DOMContentLoaded', () => {
		const widget = document.querySelector('[data-chatbot]');
		const panel = widget?.querySelector('[data-chatbot-panel]');
		const messages = panel?.querySelector('[data-chatbot-messages]');
		const form = panel?.querySelector('[data-chatbot-form]');
		const input = form?.querySelector('input');
		const submit = form?.querySelector('[type="submit"]');

		if (!widget || !panel || !messages || !form || !input || !submit) {
			return;
		}

		const context = widget.dataset.chatbotContext === 'admin' ? 'admin' : 'site';
		const metaLabel = messages.dataset.chatbotMeta || 'Assistant';
		const counter = panel.querySelector('[data-chatbot-count]');
		const launcher = widget.querySelector('[data-chatbot-launcher]');
		const welcome = messages.innerHTML;
		const maxLength = input.maxLength > 0 ? input.maxLength : 240;
		// Answer links are relative to the project root, which the server provides.
		const rootUrl = new URL(widget.dataset.chatbotBase || '/', window.location.href);
		let busy = false;

		const setOpen = (isOpen) => {
			widget.classList.toggle('is-open', isOpen);
			launcher?.setAttribute('aria-expanded', String(isOpen));
			panel.setAttribute('aria-hidden', String(!isOpen));
			if (isOpen) {
				input.focus();
			}
		};

		const updateCount = () => {
			if (!counter) {
				return;
			}
			const length = input.value.length;
			counter.textContent = `${length} / ${maxLength}`;
			counter.classList.toggle('is-near-limit', length >= maxLength * 0.8);
			counter.classList.toggle('is-at-limit', length >= maxLength);
		};

		const addMessage = (text, sender, link) => {
			const bubble = document.createElement('div');
			bubble.className = `portfolio-chatbot-message portfolio-chatbot-message-${sender}`;
			const content = document.createElement('div');
			content.className = 'portfolio-chatbot-message-content';
			content.textContent = text;

			if (link) {
				const anchor = document.createElement('a');
				anchor.className = 'portfolio-chatbot-link';
				anchor.href = new URL(link[1], rootUrl).href;
				anchor.textContent = link[0];
				content.append(document.createElement('br'), anchor);
			}

			const meta = document.createElement('span');
			meta.className = 'portfolio-chatbot-message-meta';
			meta.textContent = `${sender === 'user' ? 'You' : metaLabel} · Now`;
			bubble.append(content, meta);
			messages.append(bubble);
			messages.scrollTop = messages.scrollHeight;
			return bubble;
		};

		const answer = (question) => {
			const normalized = question.toLowerCase();
			const match = ANSWERS[context].find(([pattern]) => pattern.test(normalized));
			return match ? [match[1], match[2]] : [FALLBACK[context]];
		};

		const respond = (question) => {
			if (busy || !question) {
				return;
			}
			busy = true;
			input.disabled = submit.disabled = true;
			addMessage(question, 'user');

			const typing = addMessage('', 'bot');
			typing.classList.add('portfolio-chatbot-typing');
			typing.innerHTML = '<span></span><span></span><span></span>';
			typing.setAttribute('aria-label', `${metaLabel} is typing`);

			window.setTimeout(() => {
				typing.remove();
				const [text, link] = answer(question);
				addMessage(text, 'bot', link);
				input.disabled = submit.disabled = busy = false;
				input.focus();
			}, 450);
		};

		form.addEventListener('submit', (event) => {
			event.preventDefault();
			respond(input.value.trim());
			form.reset();
			updateCount();
		});
		input.addEventListener('input', updateCount);
		panel.querySelectorAll('[data-chatbot-prompt]').forEach((prompt) => {
			prompt.addEventListener('click', () => respond(prompt.dataset.chatbotPrompt));
		});
		panel.querySelector('[data-chatbot-reset]')?.addEventListener('click', () => {
			if (!busy) {
				messages.innerHTML = welcome;
				form.reset();
				updateCount();
				input.focus();
			}
		});
		launcher?.addEventListener('click', () => setOpen(!widget.classList.contains('is-open')));
		panel.querySelector('[data-chatbot-close]')?.addEventListener('click', () => setOpen(false));
		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && widget.classList.contains('is-open')) {
				setOpen(false);
			}
		});

		setOpen(false);
	});
})();

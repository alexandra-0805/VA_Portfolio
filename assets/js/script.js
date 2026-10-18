document.addEventListener('DOMContentLoaded', () => {
	document.body.classList.add('loaded');

	const topButton = document.getElementById('backToTop');

	window.addEventListener('scroll', () => {
		topButton?.classList.toggle('show', window.scrollY > 500);
	});

	topButton?.addEventListener('click', () => {
		window.scrollTo({
			top: 0,
			behavior: 'smooth'
		});
	});

	const observer = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			}

			entry.target.classList.add('visible');
			observer.unobserve(entry.target);
		});
	}, {
		threshold: .12
	});

	document.querySelectorAll('.animate').forEach((element) => {
		observer.observe(element);
	});

	document.querySelectorAll('form[data-validate]').forEach((form) => {
		form.addEventListener('submit', (event) => {
			if (!form.checkValidity()) {
				event.preventDefault();
				event.stopPropagation();
			}

			form.classList.add('was-validated');
		});
	});

	document.querySelectorAll('[data-confirm]').forEach((element) => {
		element.addEventListener('click', (event) => {
			if (!window.confirm(element.dataset.confirm)) {
				event.preventDefault();
			}
		});
	});

	const certificateViewer = document.getElementById('certificateViewer');
	const certificateViewerImage = document.getElementById('certificateViewerImage');
	const certificateViewerTitle = document.getElementById('certificateViewerTitle');

	const closeCertificateViewer = () => {
		if (!certificateViewer) {
			return;
		}

		certificateViewer.hidden = true;
		document.body.classList.remove('certificate-viewer-open');
		certificateViewerImage?.removeAttribute('src');
	};

	const openCertificateViewer = (card) => {
		if (!certificateViewer || !certificateViewerImage) {
			return;
		}

		certificateViewerTitle.textContent = card.dataset.certificateTitle || 'Certificate preview';
		certificateViewerImage.src = card.dataset.certificateImage;
		certificateViewerImage.alt = card.dataset.certificateTitle || 'Certificate preview';
		certificateViewer.hidden = false;
		document.body.classList.add('certificate-viewer-open');
	};

	document.querySelectorAll('[data-certificate-image]').forEach((card) => {
		card.addEventListener('click', () => openCertificateViewer(card));
		card.addEventListener('keydown', (event) => {
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				openCertificateViewer(card);
			}
		});
	});

	document.querySelectorAll('[data-certificate-close]').forEach((element) => {
		element.addEventListener('click', closeCertificateViewer);
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeCertificateViewer();
		}
	});

	const chatbotWidget = document.querySelector('[data-chatbot]');
	const isAdminChatbot = chatbotWidget?.dataset.chatbotContext === 'admin';
	const chatbot = document.querySelector('[data-chatbot-panel]');
	const chatbotMessages = chatbot?.querySelector('[data-chatbot-messages]');
	const chatbotForm = chatbot?.querySelector('[data-chatbot-form]');
	const chatbotInput = chatbotForm?.querySelector('input');
	const chatbotSubmit = chatbotForm?.querySelector('[type="submit"]');
	const chatbotCount = chatbot?.querySelector('[data-chatbot-count]');
	const chatbotReset = chatbot?.querySelector('[data-chatbot-reset]');
	const chatbotLauncher = chatbotWidget?.querySelector('[data-chatbot-launcher]');
	const chatbotMinimize = chatbot?.querySelector('[data-chatbot-minimize]');
	const chatbotClose = chatbot?.querySelector('[data-chatbot-close]');
	const chatbotEmoji = chatbot?.querySelector('[data-chatbot-emoji]');
	const chatbotAttachment = chatbot?.querySelector('[data-chatbot-attachment]');
	const chatbotFile = chatbot?.querySelector('[data-chatbot-file]');
	const chatbotAttachmentStatus = chatbot?.querySelector('[data-chatbot-attachment-status]');

	if (!chatbotWidget || !chatbot || !chatbotMessages || !chatbotForm || !chatbotInput || !chatbotSubmit) {
		return;
	}

	let isResponding = false;
	let selectedAttachment = null;
	const chatbotMaxLength = chatbotInput.maxLength > 0 ? chatbotInput.maxLength : 240;
	const welcomeMessage = chatbotMessages.innerHTML;

	const updateChatbotCharacterCount = () => {
		if (!chatbotCount) {
			return;
		}

		const currentLength = chatbotInput.value.length;
		chatbotCount.textContent = `${currentLength} / ${chatbotMaxLength}`;
		chatbotCount.classList.toggle(
			'is-near-limit',
			currentLength >= chatbotMaxLength * .8
		);
		chatbotCount.classList.toggle(
			'is-at-limit',
			currentLength >= chatbotMaxLength
		);
	};

	const setChatbotOpen = (isOpen) => {
		chatbotWidget.classList.toggle('is-open', isOpen);
		chatbotLauncher?.setAttribute('aria-expanded', String(isOpen));
		chatbot.setAttribute('aria-hidden', String(!isOpen));
		if (isOpen) {
			chatbotInput.focus();
		}
	};

	// Ensure the widget is minimized by default (only launcher visible)
	setChatbotOpen(false);

	const addChatbotMessage = (message, sender) => {
		const messageElement = document.createElement('div');
		messageElement.className = `portfolio-chatbot-message portfolio-chatbot-message-${sender}`;
		const content = document.createElement('div');
		content.className = 'portfolio-chatbot-message-content';
		content.textContent = message;
		const meta = document.createElement('span');
		meta.className = 'portfolio-chatbot-message-meta';
		meta.textContent = sender === 'user'
			? 'You · Now'
			: `${isAdminChatbot ? 'Admin assistant' : 'Portfolio assistant'} · Now`;
		messageElement.append(content, meta);
		chatbotMessages.append(messageElement);
		chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
	};

	const showTypingIndicator = () => {
		const typingElement = document.createElement('div');
		typingElement.className = 'portfolio-chatbot-message portfolio-chatbot-message-bot portfolio-chatbot-typing';
		typingElement.setAttribute(
			'aria-label',
			`${isAdminChatbot ? 'Admin assistant' : 'Portfolio assistant'} is typing`
		);
		typingElement.innerHTML = '<span></span><span></span><span></span>';
		chatbotMessages.append(typingElement);
		chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
		return typingElement;
	};

	const getChatbotResponse = (question) => {
		const normalizedQuestion = question.toLowerCase();

		if (/\b(hire|work with|employ|available|contact|email|reach)\b/.test(normalizedQuestion)) {
			return {
				message: 'Yes. Alexandra is interested in Virtual Assistant, administrative support, customer service, data management, and e-commerce support opportunities. Please use the Hire Me or Contact section to get in touch.',
				link: { label: 'Go to Contact', href: 'contact.php' }
			};

		}

		if (/\b(service|help|offer|assist)\b/.test(normalizedQuestion)) {
			return {
				message: 'Alexandra can assist with email management, data entry, customer support, social media assistance, Canva design, administrative support, and e-commerce support.'
			};
		}

		if (/\b(skill|good at|strength|capabilit)\b/.test(normalizedQuestion)) {
			return {
				message: 'Her skills include customer service, administrative support, data entry and management, sales, communication, organization, time management, problem-solving, attention to detail, multitasking, adaptability, fast learning, social media assistance, Canva design, and e-commerce operations.'
			};
		}

		if (/\b(project|work|portfolio|kape|coffee|locker|inventory)\b/.test(normalizedQuestion)) {
			return {
				message: 'Alexandra has worked on the Kape Lagi Coffee Shop Ordering System and the Smart Secure Inventory Locker. These projects include system planning, documentation, database concepts, process modeling, data flow design, and prototype planning.',
				link: { label: 'View Projects', href: 'projects.php' }
			};
		}

		if (/\b(tool|platform|software|canva|shopify|amazon|tiktok)\b/.test(normalizedQuestion)) {
			return {
				message: 'Alexandra uses Canva, Google Workspace, Microsoft Office, and TikTok Shop. She is developing her knowledge of Amazon Seller Central and Shopify.'
			};
		}

		if (/\b(stud|education|school|degree|kld|information systems)\b/.test(normalizedQuestion)) {
			return {
				message: 'Alexandra is currently taking a Bachelor of Science in Information Systems at Kolehiyo ng Lungsod ng Dasmariñas (KLD), from 2024 to the present.'
			};
		}

		if (/\b(experience|background|business|customer service|sales)\b/.test(normalizedQuestion)) {
			return {
				message: 'Alexandra gained experience through small-business work as an owner/operator, assisting customers, handling inquiries and transactions, supporting daily operations, maintaining records, and helping with basic marketing.'
			};
		}

		if (/\b(who|about|alexandra|virtual assistant)\b/.test(normalizedQuestion)) {
			return {
				message: 'Alexandra Concepcion is an aspiring Virtual Assistant and Information Systems student with experience in customer service, sales, business operations, administrative tasks, and data management.'
			};
		}

		return {
			message: 'I can help with questions about Alexandra, her services, skills, tools, experience, education, projects, or how to contact her. Please try asking about one of those topics.'
		};
	};

	const getAdminChatbotResponse = (question) => {
		const normalizedQuestion = question.toLowerCase();

		if (/\b(projects?|work)\b/.test(normalizedQuestion)) {
			return {
				message: 'Open Projects to add, edit, or delete portfolio projects. Use the form on the left to enter the title, description, technologies, image, and project URL. The table on the right contains existing projects.',
				link: { label: 'Open Projects', href: 'projects.php' }
			};
		}

		if (/\b(messages?|inbox|contact)\b/.test(normalizedQuestion)) {
			return {
				message: 'Open Messages to review contact form submissions. You can mark unread messages as read or delete a message after confirming the action.',
				link: { label: 'Open Messages', href: 'messages.php' }
			};
		}

		if (/\b(services?|skills?)\b/.test(normalizedQuestion)) {
			return {
				message: 'Use Services to update the services shown on the public website, or Skills to manage skill names, categories, and proficiency levels.',
				link: { label: 'Open Services', href: 'services.php' }
			};
		}

		if (/\b(resume|pdf|cv)\b/.test(normalizedQuestion)) {
			return {
				message: 'Open Resume to update the resume content or replace the downloadable PDF used on the public resume page.',
				link: { label: 'Open Resume', href: 'resume.php' }
			};
		}

		if (/\b(certificates?|award)\b/.test(normalizedQuestion)) {
			return {
				message: 'Open Certificates to add, edit, or remove certificates displayed in the public portfolio.',
				link: { label: 'Open Certificates', href: 'certificates.php' }
			};
		}

		if (/\b(reports?|analytics|stats?)\b/.test(normalizedQuestion)) {
			return {
				message: 'Open Reports to review portfolio activity and available site metrics.',
				link: { label: 'Open Reports', href: 'reports.php' }
			};
		}

		if (/\b(sign out|logout|log out|password|account)\b/.test(normalizedQuestion)) {
			return {
				message: 'Use Sign out in the top-right corner to end your admin session. Use Forgot password from the sign-in page if you need to reset your password.'
			};
		}

		return {
			message: 'I can guide you through Projects, Services, Skills, Messages, Reports, Certificates, Resume, and signing out. Ask “How do I manage projects?” or choose a quick action.'
		};
	};

	const respondToChatbot = (question, attachmentName = '') => {
		if (isResponding) {
			return;
		}

		isResponding = true;
		const displayQuestion = attachmentName
			? `${question || 'Attached a file'}\n📎 ${attachmentName}`
			: question;
		addChatbotMessage(displayQuestion, 'user');
		const response = attachmentName
			? { message: `I received "${attachmentName}". I can use it as context for our conversation, but please include your question in the message.` }
			: (isAdminChatbot ? getAdminChatbotResponse(question) : getChatbotResponse(question));
		const typingElement = showTypingIndicator();
		chatbotInput.disabled = true;
		chatbotSubmit.disabled = true;

		window.setTimeout(() => {
			typingElement.remove();
		const responseElement = document.createElement('div');
		responseElement.className = 'portfolio-chatbot-message portfolio-chatbot-message-bot';
		const content = document.createElement('div');
		content.className = 'portfolio-chatbot-message-content';
		content.textContent = response.message;
		const meta = document.createElement('span');
		meta.className = 'portfolio-chatbot-message-meta';
		meta.textContent = `${isAdminChatbot ? 'Admin assistant' : 'Portfolio assistant'} · Now`;
		responseElement.append(content, meta);

		if (response.link) {
			const link = document.createElement('a');
			link.className = 'portfolio-chatbot-link';
			link.href = response.link.href;
			link.textContent = response.link.label;
			content.append(document.createElement('br'), link);
		}

		chatbotMessages.append(responseElement);
		chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
			chatbotInput.disabled = false;
			chatbotSubmit.disabled = false;
			isResponding = false;
			chatbotInput.focus();
		}, 450);
	};

	chatbotForm.addEventListener('submit', (event) => {
		event.preventDefault();
		const question = chatbotInput.value.trim();

		if (!question && !selectedAttachment) {
			return;
		}

		respondToChatbot(question, selectedAttachment?.name || '');
		selectedAttachment = null;
		if (chatbotFile) {
			chatbotFile.value = '';
		}
		if (chatbotAttachmentStatus) {
			chatbotAttachmentStatus.hidden = true;
			chatbotAttachmentStatus.textContent = '';
		}
		chatbotForm.reset();
		updateChatbotCharacterCount();
		chatbotInput.focus();
	});

	chatbotInput.addEventListener('input', () => {
		updateChatbotCharacterCount();
	});

	chatbot.querySelectorAll('[data-chatbot-prompt]').forEach((prompt) => {
		prompt.addEventListener('click', () => {
			if (isResponding) {
				return;
			}

			respondToChatbot(prompt.dataset.chatbotPrompt);
		});
	});

	chatbotReset?.addEventListener('click', () => {
		if (isResponding) {
			return;
		}

		chatbotMessages.innerHTML = welcomeMessage;
		selectedAttachment = null;
		if (chatbotFile) {
			chatbotFile.value = '';
		}
		if (chatbotAttachmentStatus) {
			chatbotAttachmentStatus.hidden = true;
			chatbotAttachmentStatus.textContent = '';
		}
		chatbotForm.reset();
		updateChatbotCharacterCount();
		chatbotInput.focus();
	});

	chatbotLauncher?.addEventListener('click', () => {
		setChatbotOpen(!chatbotWidget.classList.contains('is-open'));
	});

	chatbotMinimize?.addEventListener('click', () => {
		setChatbotOpen(false);
	});

	chatbotClose?.addEventListener('click', () => {
		setChatbotOpen(false);
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && chatbotWidget.classList.contains('is-open')) {
			setChatbotOpen(false);
		}
	});

	chatbotEmoji?.addEventListener('click', () => {
		chatbotInput.value += ' 😊';
		chatbotInput.dispatchEvent(new Event('input'));
		chatbotInput.focus();
	});

	chatbotAttachment?.addEventListener('click', () => {
		chatbotFile?.click();
	});

	chatbotFile?.addEventListener('change', () => {
		const file = chatbotFile.files?.[0];
		if (!file) {
			return;
		}

		if (file.size > 5 * 1024 * 1024) {
			chatbotFile.value = '';
			selectedAttachment = null;
			if (chatbotAttachmentStatus) {
				chatbotAttachmentStatus.hidden = false;
				chatbotAttachmentStatus.textContent = 'Attachment must be 5 MB or smaller.';
			}
			return;
		}

		selectedAttachment = file;
		if (chatbotAttachmentStatus) {
			chatbotAttachmentStatus.hidden = false;
			chatbotAttachmentStatus.textContent = `${file.name} attached`;
		}
	});
});

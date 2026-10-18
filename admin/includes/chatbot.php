<div class="portfolio-chatbot-widget admin-chatbot-widget" data-chatbot data-chatbot-context="admin">
    <button class="portfolio-chatbot-launcher" type="button" data-chatbot-launcher aria-expanded="false">
        <i class="bi bi-chat-dots" aria-hidden="true"></i>
        <span class="visually-hidden">Open admin assistant</span>
    </button>

    <div class="portfolio-chatbot" data-chatbot-panel role="dialog" aria-labelledby="admin-chatbot-title" aria-hidden="true">
        <div class="portfolio-chatbot-header">
            <div class="d-flex align-items-center gap-3">
                <div class="portfolio-chatbot-avatar" aria-hidden="true">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="portfolio-chatbot-eyebrow">Portfolio admin</div>
                    <h2 id="admin-chatbot-title" class="h5 mb-1">Admin Assistant</h2>
                    <p class="mb-0">Guidance for managing your portfolio</p>
                </div>
            </div>
            <div class="portfolio-chatbot-header-actions">
                <span class="portfolio-chatbot-status">
                    <span aria-hidden="true"></span> Ready to help
                </span>
                <button class="portfolio-chatbot-reset" type="button" data-chatbot-reset aria-label="Start a new conversation" title="Start a new conversation">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                </button>
                <button class="portfolio-chatbot-minimize" type="button" data-chatbot-minimize aria-label="Minimize chatbot" title="Minimize chatbot">
                    <i class="bi bi-dash-lg" aria-hidden="true"></i>
                </button>
                <button class="portfolio-chatbot-close" type="button" data-chatbot-close aria-label="Close chatbot" title="Close chatbot">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="portfolio-chatbot-messages" data-chatbot-messages aria-live="polite" aria-label="Admin assistant conversation">
            <div class="portfolio-chatbot-message portfolio-chatbot-message-bot">
                <div class="portfolio-chatbot-message-content">
                    Hi! I’m your admin assistant. I can guide you through managing projects,
                    services, skills, messages, reports, certificates, and your resume.
                    What would you like to do?
                </div>
                <span class="portfolio-chatbot-message-meta">Admin assistant · Now</span>
            </div>
        </div>

        <div class="portfolio-chatbot-prompts">
            <span class="portfolio-chatbot-prompts-label">Quick actions</span>
            <div class="portfolio-chatbot-prompt-list" aria-label="Suggested admin actions">
                <button type="button" data-chatbot-prompt="How do I manage projects?">
                    <i class="bi bi-grid" aria-hidden="true"></i>
                    Projects
                </button>
                <button type="button" data-chatbot-prompt="How do I check messages?">
                    <i class="bi bi-envelope" aria-hidden="true"></i>
                    Messages
                </button>
                <button type="button" data-chatbot-prompt="How do I update my resume?">
                    <i class="bi bi-file-earmark-person" aria-hidden="true"></i>
                    Resume
                </button>
            </div>
        </div>

        <form class="portfolio-chatbot-form" data-chatbot-form>
            <label class="visually-hidden" for="admin-chatbot-input">Ask the admin assistant</label>
            <input id="admin-chatbot-input" class="form-control" type="text" name="message" placeholder="Ask how to manage your portfolio..." autocomplete="off" maxlength="240" required>
            <span class="portfolio-chatbot-character-count" data-chatbot-count aria-live="polite">0 / 240</span>
            <button class="portfolio-chatbot-tool" type="button" aria-label="Add emoji" title="Emoji" data-chatbot-emoji>
                <i class="bi bi-emoji-smile" aria-hidden="true"></i>
            </button>
            <input class="visually-hidden" type="file" data-chatbot-file accept=".pdf,.doc,.docx,.txt,.csv,.jpg,.jpeg,.png,.webp">
            <button class="portfolio-chatbot-tool" type="button" data-chatbot-attachment aria-label="Add attachment" title="Attachment">
                <i class="bi bi-paperclip" aria-hidden="true"></i>
            </button>
            <button class="btn btn-primary portfolio-chatbot-submit" type="submit">
                <span class="visually-hidden">Send message</span>
                <i class="bi bi-send" aria-hidden="true"></i>
            </button>
        </form>
        <div class="portfolio-chatbot-attachment" data-chatbot-attachment-status role="status" aria-live="polite" hidden></div>
        <div class="portfolio-chatbot-footer">
            <p class="portfolio-chatbot-disclaimer mb-0">
                <i class="bi bi-shield-check" aria-hidden="true"></i>
                Admin guidance only. Changes still require your confirmation.
            </p>
            <span class="portfolio-chatbot-hint">Press Enter to send</span>
        </div>
    </div>
</div>

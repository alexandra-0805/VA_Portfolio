<?php
/**
 * Chat assistant widget, shared by the public site and the admin panel.
 * Pass $chatbotContext = 'site' | 'admin' before including.
 */
$chatbotSettings = [
    'site' => [
        'id' => 'portfolio-chatbot',
        'class' => '',
        'launcher' => "Open Alexandra's portfolio assistant",
        'avatar' => 'bi-stars',
        'eyebrow' => 'Alexandra Concepcion',
        'title' => "Alex's Assistant",
        'subtitle' => "Professional guidance about Alexandra's work",
        'welcome' => "Hi! I'm Alexandra's portfolio assistant. I can tell you about her services, skills, projects, experience, and how to contact her. What would you like to know?",
        'meta' => 'Portfolio assistant',
        'promptsLabel' => 'Quick questions',
        'prompts' => [
            ['bi-briefcase', 'Services', 'What services does Alexandra offer?'],
            ['bi-grid', 'Projects', "Tell me about Alexandra's projects."],
            ['bi-chat-heart', 'Hire Alexandra', 'Can I hire Alexandra?'],
        ],
        'placeholder' => 'Ask about services, skills, or projects...',
        'disclaimer' => 'Information is limited to this portfolio.',
    ],
    'admin' => [
        'id' => 'admin-chatbot',
        'class' => 'admin-chatbot-widget',
        'launcher' => 'Open admin assistant',
        'avatar' => 'bi-shield-check',
        'eyebrow' => 'Portfolio admin',
        'title' => 'Admin Assistant',
        'subtitle' => 'Guidance for managing your portfolio',
        'welcome' => "Hi! I'm your admin assistant. I can guide you through managing projects, services, skills, messages, reports, certificates, and your resume. What would you like to do?",
        'meta' => 'Admin assistant',
        'promptsLabel' => 'Quick actions',
        'prompts' => [
            ['bi-grid', 'Projects', 'How do I manage projects?'],
            ['bi-envelope', 'Messages', 'How do I check messages?'],
            ['bi-file-earmark-person', 'Resume', 'How do I update my resume?'],
        ],
        'placeholder' => 'Ask how to manage your portfolio...',
        'disclaimer' => 'Admin guidance only. Changes still require your confirmation.',
    ],
];
$bot = $chatbotSettings[$chatbotContext ?? 'site'];
?>
<div class="portfolio-chatbot-widget <?= e($bot['class']) ?>" data-chatbot data-chatbot-context="<?= e($chatbotContext ?? 'site') ?>" data-chatbot-base="<?= e(url()) ?>">
    <button class="portfolio-chatbot-launcher" type="button" data-chatbot-launcher aria-expanded="false">
        <i class="bi bi-chat-dots" aria-hidden="true"></i>
        <span class="visually-hidden"><?= e($bot['launcher']) ?></span>
    </button>

    <div class="portfolio-chatbot" data-chatbot-panel role="dialog" aria-labelledby="<?= e($bot['id']) ?>-title" aria-hidden="true">
        <div class="portfolio-chatbot-header">
            <div class="d-flex align-items-center gap-3">
                <div class="portfolio-chatbot-avatar" aria-hidden="true"><i class="bi <?= e($bot['avatar']) ?>"></i></div>
                <div>
                    <div class="portfolio-chatbot-eyebrow"><?= e($bot['eyebrow']) ?></div>
                    <h2 id="<?= e($bot['id']) ?>-title" class="h5 mb-1"><?= e($bot['title']) ?></h2>
                    <p class="mb-0"><?= e($bot['subtitle']) ?></p>
                </div>
            </div>
            <div class="portfolio-chatbot-header-actions">
                <span class="portfolio-chatbot-status"><span aria-hidden="true"></span> Ready to help</span>
                <button class="portfolio-chatbot-icon-btn" type="button" data-chatbot-reset aria-label="Start a new conversation" title="Start a new conversation">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                </button>
                <button class="portfolio-chatbot-icon-btn" type="button" data-chatbot-close aria-label="Close chatbot" title="Close chatbot">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="portfolio-chatbot-messages" data-chatbot-messages data-chatbot-meta="<?= e($bot['meta']) ?>" aria-live="polite" aria-label="Chat conversation">
            <div class="portfolio-chatbot-message portfolio-chatbot-message-bot">
                <div class="portfolio-chatbot-message-content"><?= e($bot['welcome']) ?></div>
                <span class="portfolio-chatbot-message-meta"><?= e($bot['meta']) ?> · Now</span>
            </div>
        </div>

        <div class="portfolio-chatbot-prompts">
            <span class="portfolio-chatbot-prompts-label"><?= e($bot['promptsLabel']) ?></span>
            <div class="portfolio-chatbot-prompt-list">
                <?php foreach ($bot['prompts'] as [$icon, $label, $question]): ?>
                    <button type="button" data-chatbot-prompt="<?= e($question) ?>">
                        <i class="bi <?= e($icon) ?>" aria-hidden="true"></i>
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <form class="portfolio-chatbot-form" data-chatbot-form>
            <label class="visually-hidden" for="<?= e($bot['id']) ?>-input">Ask the assistant</label>
            <input id="<?= e($bot['id']) ?>-input" class="form-control" type="text" name="message" placeholder="<?= e($bot['placeholder']) ?>" autocomplete="off" maxlength="240" required>
            <span class="portfolio-chatbot-character-count" data-chatbot-count aria-live="polite">0 / 240</span>
            <button class="btn btn-primary portfolio-chatbot-submit" type="submit">
                <span class="visually-hidden">Send message</span>
                <i class="bi bi-send" aria-hidden="true"></i>
            </button>
        </form>
        <div class="portfolio-chatbot-footer">
            <p class="portfolio-chatbot-disclaimer mb-0">
                <i class="bi bi-shield-check" aria-hidden="true"></i>
                <?= e($bot['disclaimer']) ?>
            </p>
            <span class="portfolio-chatbot-hint">Press Enter to send</span>
        </div>
    </div>
</div>

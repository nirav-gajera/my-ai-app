import './bootstrap';
/* ─────────────────────────────────────────────────────────────────────
   Shared helpers
───────────────────────────────────────────────────────────────────── */
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const escapeHtml = (value) =>
    String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

const prettyDate = (value) => {
    if (!value) return 'Just now';
    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
};

const request = async (url, options = {}) => {
    const headers = new Headers(options.headers ?? {});
    if (!(options.body instanceof FormData)) headers.set('Content-Type', 'application/json');
    headers.set('Accept', 'application/json');
    headers.set('X-CSRF-TOKEN', csrfToken);
    const response = await fetch(url, { ...options, headers });
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) {
        const validationMessage = payload.errors && typeof payload.errors === 'object'
            ? Object.values(payload.errors).flat().join(' ')
            : null;
        throw new Error(validationMessage ?? payload.message ?? 'Request failed.');
    }
    return payload;
};

const createToast = ({ type = 'success', title, message }) => {
    let stack = document.querySelector('#toast-stack');

    if (!stack) {
        stack = document.createElement('div');
        stack.id = 'toast-stack';
        stack.className = 'toast-stack';
        stack.setAttribute('aria-live', 'polite');
        stack.setAttribute('aria-atomic', 'true');
        document.body.appendChild(stack);
    }

    const toast = document.createElement('div');
    toast.className = `toast-notice toast-${type}`;
    toast.setAttribute('data-toast', '');

    toast.innerHTML = `
        <div class="toast-icon">
            ${type === 'success'
                ? `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>`
            }
        </div>
        <div class="toast-copy">
            <strong>${escapeHtml(title ?? (type === 'success' ? 'Success' : 'Error'))}</strong>
            <p>${escapeHtml(message ?? '')}</p>
        </div>
        <button type="button" class="toast-close" aria-label="Dismiss notification">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </button>
    `;

    const closeToast = () => {
        toast.classList.add('toast-exit');
        window.setTimeout(() => toast.remove(), 180);
    };

    toast.querySelector('.toast-close')?.addEventListener('click', closeToast);
    stack.appendChild(toast);
    window.setTimeout(closeToast, 4200);
};

// Expose to window for use in blade files
window.createToast = createToast;

document.querySelectorAll('[data-toast]').forEach((toast) => {
    const closeToast = () => {
        toast.classList.add('toast-exit');
        window.setTimeout(() => toast.remove(), 180);
    };

    toast.querySelector('[data-toast-close]')?.addEventListener('click', closeToast);
    window.setTimeout(closeToast, 4200);
});

/* ─────────────────────────────────────────────────────────────────────
   Conversations page  (#conversations-page)
───────────────────────────────────────────────────────────────────── */
const convPage = document.querySelector('#conversations-page');

if (convPage) {
    const bootState = JSON.parse(convPage.dataset.state ?? '{}');
    const routes = JSON.parse(convPage.dataset.routes ?? '{}');
    const shouldStartNew = convPage.dataset.startNew === '1';

    const state = {
        conversations: bootState.conversations ?? [],
        selectedConversation: bootState.selectedConversation ?? null,
        sending: false,
        isTyping: false,
        uploading: false,
    };

    const el = {
        convList: document.querySelector('#conversation-list'),
        convCount: document.querySelector('#conversation-count'),
        convTitle: document.querySelector('#conversation-title'),
        msgStream: document.querySelector('#message-stream'),
        msgForm: document.querySelector('#message-form'),
        question: document.querySelector('#question'),
        status: document.querySelector('#composer-status'),
        sendBtn: document.querySelector('#send-button'),
        newConvBtn: document.querySelector('#new-conversation-button'),
        deleteConvBtn: document.querySelector('#delete-conversation-button'),
        // Pinned Panel
        pinnedPanel: document.querySelector('#pinned-panel'),
        pinnedList: document.querySelector('#pinned-messages-list'),
        pinCount: document.querySelector('#pin-count'),
        togglePinnedBtn: document.querySelector('#toggle-pinned-panel'),
        closePinnedBtn: document.querySelector('#close-pinned-panel'),
    };

    const conModal = document.getElementById('con-delete-modal');
    const conBackdrop = document.getElementById('con-modal-backdrop');
    const conCancel = document.getElementById('con-modal-cancel');
    const conConfirm = document.getElementById('con-confirm-delete');
    const conText = document.getElementById('con-delete-text');

    const openConModal = () => {
        if (conModal) conModal.style.display = '';
    };
    const closeConModal = () => {
        if (conModal) conModal.style.display = 'none';
    };

    const togglePinnedPanel = () => {
        if (!el.pinnedPanel) return;
        const isHidden = el.pinnedPanel.style.display === 'none';
        el.pinnedPanel.style.display = isHidden ? 'flex' : 'none';
        if (isHidden) renderPinnedList();
    };

    const renderPinnedList = () => {
        const pinned = state.selectedConversation?.messages?.filter(m => m.is_pinned) ?? [];
        el.pinCount.textContent = String(pinned.length);

        if (!pinned.length) {
            el.pinnedList.innerHTML = '<p class="empty-state">No pinned messages yet.</p>';
            return;
        }

        el.pinnedList.innerHTML = pinned.map(m => `
            <div class="pinned-item" data-jump-to="${m.id}">
                <strong>${m.role === 'assistant' ? 'Assistant' : 'You'}</strong>
                <p>${escapeHtml(m.content)}</p>
            </div>
        `).join('');

        el.pinnedList.querySelectorAll('[data-jump-to]').forEach(item => {
            item.addEventListener('click', () => {
                const target = el.msgStream.querySelector(`[data-id="${item.dataset.jumpTo}"]`);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    target.classList.add('jump-highlight');
                    setTimeout(() => target.classList.remove('jump-highlight'), 2000);
                }
            });
        });
    };

    /* ── Render helpers ── */
    const renderConversations = () => {
        el.convCount.textContent = String(state.conversations.length);

        if (!state.conversations.length) {
            el.convList.innerHTML = '<div class="empty-state">No chats yet. Click "New Conversation" to begin.</div>';
            return;
        }

        el.convList.innerHTML = state.conversations.map((c) => {
            const active = state.selectedConversation?.id === c.id ? 'active' : '';
            const lastDate = prettyDate(c.last_message_at ?? c.updated_at);
            const statusText = c.last_message_at ? 'Active knowledge chat' : 'Empty conversation';
            
            return `
                <button class="conversation-card ${active}" type="button" data-id="${c.id}">
                    <div class="conv-card-header">
                        <span class="conv-card-title" title="${escapeHtml(c.title)}">${escapeHtml(c.title)}</span>
                        <span class="conv-card-date">${lastDate}</span>
                    </div>
                    <div class="conv-card-footer">
                        <span class="muted">${statusText}</span>
                    </div>
                </button>`;
        }).join('');

        el.convList.querySelectorAll('[data-id]').forEach((btn) =>
            btn.addEventListener('click', () => loadConversation(btn.dataset.id))
        );
    };

    const renderMessages = () => {
        const sel = state.selectedConversation;
        el.convTitle.textContent = sel?.title ?? 'New Conversation';
        el.deleteConvBtn.disabled = !sel;

        renderPinnedList();

        if (!sel?.messages?.length) {
            el.msgStream.innerHTML = `
                <div class="empty-state-chat">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <p>${sel ? 'No messages yet. Ask your first question below.' : 'Select a conversation or create a new one.'}</p>
                </div>`;
            return;
        }

        el.msgStream.innerHTML = sel.messages.map((m) => {
            const citations = Array.isArray(m.citations) && m.citations.length
                ? `<div class="citation-list">${m.citations.map((c) =>
                    `<span class="citation-pill">${escapeHtml(c.title)} · chunk ${Number(c.chunk_index) + 1}</span>`
                ).join('')}</div>`
                : '';

            const isAssistant = m.role === 'assistant';
            const reactionHtml = isAssistant ? `
                <button class="msg-action-btn ${m.reaction === 'like' ? 'active like' : ''}" data-msg-action="like" data-id="${m.id}" title="Good response">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 10v12" /><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z" /></svg>
                </button>
                <button class="msg-action-btn ${m.reaction === 'dislike' ? 'active dislike' : ''}" data-msg-action="dislike" data-id="${m.id}" title="Bad response">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 14V2" /><path d="M9 18.12 10 14H4.17a2 2 0 0 1-1.92-2.56l2.33-8A2 2 0 0 1 6.5 2H20a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2.76a2 2 0 0 0-1.79 1.11L12 22h0a3.13 3.13 0 0 1-3-3.88Z" /></svg>
                </button>
            ` : '';

            const pinHtml = `
                <button class="msg-action-btn ${m.is_pinned ? 'active pin' : ''}" data-msg-action="pin" data-id="${m.id}" title="${m.is_pinned ? 'Unpin' : 'Pin'} message">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
                </button>
            `;

            return `
                <article class="message-card ${m.role} ${m.is_pinned ? 'pinned' : ''}" data-id="${m.id}">
                    <div class="pin-indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/></svg>
                        Pinned
                    </div>
                    <div class="message-meta">
                        <strong>${m.role === 'assistant' ? 'Assistant' : 'You'}</strong>
                    </div>
                    <div class="message-body">${escapeHtml(m.content).replaceAll('\n', '<br>')}</div>
                    ${citations}
                    <div class="message-footer">
                        <span class="message-time">${prettyDate(m.created_at)}</span>
                    </div>
                    <div class="message-actions">
                        ${reactionHtml}
                        ${pinHtml}
                    </div>
                </article>`;
        }).join('');

        el.msgStream.querySelectorAll('[data-msg-action]').forEach((btn) => {
            btn.addEventListener('click', () => handleMessageAction(btn.dataset.id, btn.dataset.msgAction));
        });

        if (state.isTyping) {
            el.msgStream.innerHTML += `
                <div class="typing-bubble">
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>`;
        }

        el.msgStream.scrollTop = el.msgStream.scrollHeight;
    };

    const handleMessageAction = async (msgId, action) => {
        const msg = state.selectedConversation.messages.find(m => m.id == msgId);
        if (!msg) return;

        try {
            if (action === 'like' || action === 'dislike') {
                const newReaction = msg.reaction === action ? null : action;
                await request(`/messages/${msgId}/react`, {
                    method: 'PATCH',
                    body: JSON.stringify({ reaction: newReaction })
                });
                msg.reaction = newReaction;
            } else if (action === 'pin') {
                await request(`/messages/${msgId}/pin`, { method: 'PATCH' });
                msg.is_pinned = !msg.is_pinned;
            }
            renderMessages();
        } catch (e) {
            createToast({ type: 'error', title: 'Action failed', message: e.message });
        }
    };

    /* ── State helpers ── */
    const syncConversation = (conversation) => {
        const summary = {
            id: conversation.id,
            title: conversation.title,
            last_message_at: conversation.last_message_at ?? new Date().toISOString(),
            updated_at: conversation.last_message_at ?? new Date().toISOString(),
        };
        const idx = state.conversations.findIndex((c) => c.id === conversation.id);
        if (idx === -1) {
            state.conversations.unshift(summary);
        } else {
            state.conversations[idx] = { ...state.conversations[idx], ...summary };
            state.conversations.sort(
                (a, b) => new Date(b.last_message_at ?? b.updated_at) - new Date(a.last_message_at ?? a.updated_at)
            );
        }
    };

    /* ── Actions ── */
    const createConversation = async (focusComposer = true) => {
        el.newConvBtn.disabled = true;
        el.status.textContent = 'Creating conversation...';
        try {
            const payload = await request(routes.conversations, { method: 'POST' });
            state.selectedConversation = payload.conversation;
            syncConversation(payload.conversation);
            renderConversations();
            renderMessages();
            el.status.textContent = 'Conversation ready.';
            if (focusComposer) el.question.focus();
        } catch (e) {
            el.status.textContent = e.message;
        } finally {
            el.newConvBtn.disabled = false;
        }
    };

    const loadConversation = async (id) => {
        el.status.textContent = 'Loading conversation...';
        try {
            const payload = await request(`/conversations/${id}`);
            state.selectedConversation = payload.conversation;
            renderConversations();
            renderMessages();
            el.status.textContent = 'Ready.';
        } catch (e) {
            el.status.textContent = e.message;
        }
    };

    const deleteConversation = async () => {
        if (!state.selectedConversation) return;
        if (conText) conText.textContent = `Delete "${state.selectedConversation.title}"? This cannot be undone.`;
        openConModal();
    };

    conConfirm?.addEventListener('click', async () => {
        if (!state.selectedConversation) return;

        el.deleteConvBtn.disabled = true;

        try {
            await request(`/conversations/${state.selectedConversation.id}`, { method: 'DELETE' });
            state.conversations = state.conversations.filter((c) => c.id !== state.selectedConversation.id);
            state.selectedConversation = null;

            renderConversations();
            renderMessages();

            closeConModal();
            createToast({
                type: 'success',
                title: 'Conversation deleted',
                message: 'The conversation was removed successfully.',
            });
        } catch (e) {
            createToast({
                type: 'error',
                title: 'Delete failed',
                message: e.message,
            });
        } finally {
            el.deleteConvBtn.disabled = false;
        }
    });

    conCancel?.addEventListener('click', closeConModal);
    conBackdrop?.addEventListener('click', closeConModal);

    const sendMessage = async (event) => {
        event.preventDefault();
        const question = el.question.value.trim();
        if (!question || state.sending) return;

        if (!state.selectedConversation) await createConversation(false);
        if (!state.selectedConversation) return;

        const tempUserMessage = {
            role: 'user',
            content: question,
            created_at: new Date().toISOString()
        };

        state.selectedConversation.messages = [
            ...(state.selectedConversation.messages ?? []),
            tempUserMessage
        ];

        el.question.value = '';
        state.sending = true;
        state.isTyping = true;
        el.sendBtn.disabled = true;
        el.status.textContent = 'Thinking...';

        renderMessages();

        try {
            const payload = await request(`/conversations/${state.selectedConversation.id}/messages`, {
                method: 'POST',
                body: JSON.stringify({ question }),
            });
            state.selectedConversation.messages = [
                ...(state.selectedConversation.messages.filter(m => m !== tempUserMessage)),
                payload.user_message,
                payload.assistant_message,
            ];
            state.selectedConversation.title = payload.conversation.title;
            syncConversation(payload.conversation);
            renderConversations();
            el.status.textContent = 'Answer generated.';
        } catch (e) {
            el.status.textContent = e.message;
            state.selectedConversation.messages = state.selectedConversation.messages.filter(m => m !== tempUserMessage);
        } finally {
            state.sending = false;
            state.isTyping = false;
            el.sendBtn.disabled = false;
            renderMessages();
        }
    };

    /* ── Wire events ── */
    el.msgForm?.addEventListener('submit', sendMessage);
    el.newConvBtn?.addEventListener('click', () => createConversation());
    el.deleteConvBtn?.addEventListener('click', deleteConversation);
    el.togglePinnedBtn?.addEventListener('click', togglePinnedPanel);
    el.closePinnedBtn?.addEventListener('click', () => {
        if (el.pinnedPanel) el.pinnedPanel.style.display = 'none';
    });

    /* ── Initial render ── */
    renderConversations();
    renderMessages();

    if (shouldStartNew) {
        createConversation();
    }
}

const landingBackToTop = document.querySelector('#landing-back-to-top');

if (landingBackToTop) {
    const toggleBackToTop = () => {
        const shouldShow = window.scrollY > 320;
        landingBackToTop.classList.toggle('is-visible', shouldShow);
    };

    landingBackToTop.addEventListener('click', () => {
        if (window.scrollY <= 0) return;

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    });

    toggleBackToTop();
    window.addEventListener('scroll', toggleBackToTop, { passive: true });
}

/* ─────────────────────────────────────────────────────────────────────
   Knowledge base page  (#knowledge-page)
───────────────────────────────────────────────────────────────────── */
const knowledgePage = document.querySelector('#knowledge-page');

if (knowledgePage) {
    const bootState = JSON.parse(knowledgePage.dataset.state ?? '{}');
    const routes = JSON.parse(knowledgePage.dataset.routes ?? '{}');
    let selectedDocId = null;
    let selectedDocTitle = '';
    let reindexDocId = null;

    const state = {
        documents: bootState.documents ?? [],
        pagination: bootState.pagination ?? { current_page: 1, last_page: 1, total: bootState.documents?.length ?? 0 },
        uploading: false,
        reindexing: false,
        loadingPage: false,
    };

    const el = {
        docList: document.querySelector('#document-list'),
        docCount: document.querySelector('#document-count'),
        knowledgeForm: document.querySelector('#knowledge-form'),
        uploadStatus: document.querySelector('#upload-status'),
        deleteStatus: document.querySelector('#delete-status'),
        uploadBtn: document.querySelector('#upload-button'),
        pagination: document.querySelector('#document-pagination'),
        reindexForm: document.querySelector('#doc-reindex-form'),
        reindexTitle: document.querySelector('#reindex-document-title'),
        reindexSubmit: document.querySelector('#doc-reindex-submit'),
        reindexStatus: document.querySelector('#doc-reindex-status'),
    };
    const docModal = document.getElementById('doc-delete-modal');
    const docBackdrop = document.getElementById('doc-modal-backdrop');
    const docCancel = document.getElementById('doc-modal-cancel');
    const docConfirm = document.getElementById('doc-confirm-delete');
    const docText = document.getElementById('doc-delete-text');
    const reindexModal = document.getElementById('doc-reindex-modal');
    const reindexBackdrop = document.getElementById('doc-reindex-backdrop');
    const reindexCancel = document.getElementById('doc-reindex-cancel');
    const reindexText = document.getElementById('doc-reindex-text');

    const openDocModal = () => {
        docModal.style.display = '';
    };

    const closeDocModal = () => {
        docModal.style.display = 'none';
        selectedDocId = null;
    };

    const openReindexModal = (document) => {
        reindexDocId = document.id;
        el.reindexForm?.reset();
        if (el.reindexTitle) el.reindexTitle.value = document.title ?? '';
        if (el.reindexStatus) el.reindexStatus.textContent = '';
        if (reindexText) {
            reindexText.textContent = `Replace "${document.title}" with a new file or updated text. Existing chunks and embeddings will be regenerated.`;
        }
        if (reindexModal) reindexModal.style.display = '';
    };

    const closeReindexModal = () => {
        if (reindexModal) reindexModal.style.display = 'none';
        el.reindexForm?.reset();
        if (el.reindexStatus) el.reindexStatus.textContent = '';
        reindexDocId = null;
    };

    const syncDocument = (document) => {
        const index = state.documents.findIndex((item) => item.id === document.id);
        if (index === -1) {
            state.documents.unshift(document);
            return;
        }

        state.documents[index] = {
            ...state.documents[index],
            ...document,
        };
    };

    const updateKnowledgeUrl = () => {
        const url = new URL(window.location.href);
        url.searchParams.set('page', String(state.pagination?.current_page ?? 1));
        url.searchParams.set('per_page', String(state.pagination?.per_page ?? 5));
        window.history.replaceState({}, '', url);
    };

    const renderPagination = () => {
        if (!el.pagination) return;

        const {
            current_page = 1,
            last_page = 1,
            total = state.documents.length,
            per_page = 5,
        } = state.pagination ?? {};

        if (!total) {
            el.pagination.innerHTML = '';
            return;
        }

        const pages = [];
        const startPage = Math.max(1, current_page - 1);
        const endPage = Math.min(last_page, current_page + 1);

        if (startPage > 1) {
            pages.push(1);
            if (startPage > 2) pages.push('ellipsis-start');
        }

        for (let page = startPage; page <= endPage; page += 1) {
            pages.push(page);
        }

        if (endPage < last_page) {
            if (endPage < last_page - 1) pages.push('ellipsis-end');
            pages.push(last_page);
        }

        el.pagination.innerHTML = `
            <div class="admin-pagination-bar">
                <label class="pagination-select-label" for="documents-per-page">
                    <span>Show</span>
                    <select id="documents-per-page" class="pagination-select">
                        ${[5, 10, 25, 50, 100].map((option) => `
                            <option value="${option}" ${option === per_page ? 'selected' : ''}>${option}</option>
                        `).join('')}
                    </select>
                    <span>of ${total}</span>
                </label>

                <div class="pagination-list">
                    <button type="button" class="pagination-link" data-doc-page="${current_page - 1}" ${current_page === 1 ? 'disabled' : ''}>« Previous</button>
                    ${pages.map((page) => page === 'ellipsis-start' || page === 'ellipsis-end'
                        ? `<span class="pagination-ellipsis">…</span>`
                        : `
                    <button type="button" class="pagination-link ${page === current_page ? 'active' : ''}" data-doc-page="${page}">
                        ${page}
                    </button>`
                    ).join('')}
                    <button type="button" class="pagination-link" data-doc-page="${current_page + 1}" ${current_page === last_page ? 'disabled' : ''}>Next »</button>
                </div>
            </div>
            <div class="pagination-summary">
                ${total} total documents
            </div>
        `;

        el.pagination.querySelector('#documents-per-page')?.addEventListener('change', (event) => {
            loadDocuments(1, Number(event.target.value));
        });

        el.pagination.querySelectorAll('[data-doc-page]').forEach((button) =>
            button.addEventListener('click', () => loadDocuments(Number(button.dataset.docPage), per_page))
        );
    };

    const loadDocuments = async (page = 1, perPage = state.pagination?.per_page ?? 5) => {
        if (state.loadingPage) return;

        state.loadingPage = true;

        try {
            const payload = await request(`${routes.knowledgeDocumentsBase}?page=${page}&per_page=${perPage}`);
            state.documents = payload.documents ?? [];
            state.pagination = payload.pagination ?? state.pagination;
            renderDocuments();
            updateKnowledgeUrl();
        } catch (e) {
            createToast({
                type: 'error',
                title: 'Loading failed',
                message: e.message,
            });
        } finally {
            state.loadingPage = false;
        }
    };

    
    /* ── Render ── */
    const renderDocuments = () => {
        el.docCount.textContent = String(state.pagination?.total ?? state.documents.length);

        if (!state.documents.length) {
            el.docList.innerHTML = '<div class="empty-state">No knowledge documents indexed yet. Upload a file or paste content to get started.</div>';
            renderPagination();
            return;
        }

        el.docList.innerHTML = state.documents.map((d) => `
            <article class="document-card">
                <div class="document-meta">
                    <strong>${escapeHtml(d.title)}</strong>
                    <span class="chunk-badge">${d.chunk_count} chunks</span>
                </div>
                <p class="doc-source">${escapeHtml(d.source_name ?? d.source_type ?? 'text')}</p>
                <p class="doc-date">${prettyDate(d.created_at)}</p>
                <div class="document-actions">
                    <button class="btn btn btn-outline-primary btn-sm" type="button" data-reindex-doc-id="${d.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    Replace
                    </button>
                    <button class="btn btn-outline-danger btn-sm" type="button" data-doc-id="${d.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                            <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                        </svg>
                        Delete
                    </button>
                </div>
            </article>
        `).join('');

        el.docList.querySelectorAll('[data-doc-id]').forEach((btn) =>
            btn.addEventListener('click', () => {
                const docId = btn.dataset.docId;
                const doc = state.documents.find(d => d.id == docId);

                selectedDocId = docId;
                selectedDocTitle = doc?.title || '';

                docText.textContent = `Delete "${selectedDocTitle}"? This cannot be undone.`;

                openDocModal();
            })
        );

        el.docList.querySelectorAll('[data-reindex-doc-id]').forEach((btn) =>
            btn.addEventListener('click', () => {
                const document = state.documents.find((item) => item.id == btn.dataset.reindexDocId);
                if (!document) return;
                openReindexModal(document);
            })
        );

        renderPagination();
    };

    /* ── Actions ── */
    const uploadDocument = async (event) => {
        event.preventDefault();
        if (state.uploading) return;
        state.uploading = true;
        el.uploadBtn.disabled = true;
        try {
            const formData = new FormData(el.knowledgeForm);
            const payload = await request(routes.knowledgeDocuments, { method: 'POST', body: formData });
            await loadDocuments(1, state.pagination?.per_page ?? 5);
            el.knowledgeForm.reset();
            if (el.uploadStatus) el.uploadStatus.textContent = '';
            createToast({
                type: 'success',
                title: 'Document indexed',
                message: payload.message,
            });
        } catch (e) {
            if (el.uploadStatus) el.uploadStatus.textContent = '';
            createToast({
                type: 'error',
                title: 'Indexing failed',
                message: e.message,
            });
        } finally {
            state.uploading = false;
            el.uploadBtn.disabled = false;
        }
    };

    const reindexDocument = async (event) => {
        event.preventDefault();
        if (!reindexDocId || state.reindexing) return;

        state.reindexing = true;
        if (el.reindexSubmit) el.reindexSubmit.disabled = true;
        if (el.reindexStatus) el.reindexStatus.textContent = '';

        try {
            const formData = new FormData(el.reindexForm);
            const payload = await request(`${routes.knowledgeDocumentsBase}/${reindexDocId}/reindex`, {
                method: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'PUT',
                },
                body: formData,
            });

            syncDocument(payload.document);
            renderDocuments();
            closeReindexModal();
            createToast({
                type: 'success',
                title: 'Document updated',
                message: payload.message,
            });
        } catch (e) {
            createToast({
                type: 'error',
                title: 'Re-indexing failed',
                message: e.message,
            });
        } finally {
            state.reindexing = false;
            if (el.reindexSubmit) el.reindexSubmit.disabled = false;
        }
    };
    docConfirm.addEventListener('click', async () => {
        if (!selectedDocId) return;

        try {
            await request(`/knowledge-documents/${selectedDocId}`, {
                method: 'DELETE'
            });

            const nextTotal = Math.max((state.pagination?.total ?? 1) - 1, 0);
            const shouldStepBack = state.documents.length === 1 && (state.pagination?.current_page ?? 1) > 1;
            const targetPage = shouldStepBack
                ? (state.pagination?.current_page ?? 1) - 1
                : (state.pagination?.current_page ?? 1);

            state.pagination = {
                ...state.pagination,
                total: nextTotal,
            };

            await loadDocuments(targetPage, state.pagination?.per_page ?? 5);

            closeDocModal();
            createToast({
                type: 'success',
                title: 'Document deleted',
                message: 'Knowledge document deleted successfully.',
            });
        } catch (e) {
            createToast({
                type: 'error',
                title: 'Delete failed',
                message: e.message,
            });
        }
    });
    docCancel.addEventListener('click', closeDocModal);
    docBackdrop.addEventListener('click', closeDocModal);
    reindexCancel?.addEventListener('click', closeReindexModal);
    reindexBackdrop?.addEventListener('click', closeReindexModal);
    /* ── Wire events ── */
    el.knowledgeForm?.addEventListener('submit', uploadDocument);
    el.reindexForm?.addEventListener('submit', reindexDocument);

    /* ── Initial render ── */
    renderDocuments();
}

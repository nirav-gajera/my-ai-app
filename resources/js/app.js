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
    if (!response.ok) throw new Error(payload.message ?? 'Request failed.');
    return payload;
};

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

    /* ── Render helpers ── */
    const renderConversations = () => {
        el.convCount.textContent = String(state.conversations.length);

        if (!state.conversations.length) {
            el.convList.innerHTML = '<div class="empty-state">No chats yet. Click "New Conversation" to begin.</div>';
            return;
        }

        el.convList.innerHTML = state.conversations.map((c) => {
            const active = state.selectedConversation?.id === c.id ? 'active' : '';
            return `
                <button class="conversation-card ${active}" type="button" data-id="${c.id}">
                    <div class="conversation-meta">
                        <strong>${escapeHtml(c.title)}</strong>
                        <span>${prettyDate(c.last_message_at ?? c.updated_at)}</span>
                    </div>
                    <p class="muted">${c.last_message_at ? 'Active knowledge chat' : 'Empty conversation'}</p>
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
            return `
                <article class="message-card ${m.role}">
                    <div class="message-meta">
                        <strong>${m.role === 'assistant' ? 'Assistant' : 'You'}</strong>
                        <span>${prettyDate(m.created_at)}</span>
                    </div>
                    <div class="message-body">${escapeHtml(m.content).replaceAll('\n', '<br>')}</div>
                    ${citations}
                </article>`;
        }).join('');

        el.msgStream.scrollTop = el.msgStream.scrollHeight;
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
        el.status.textContent = 'Deleting conversation...';

        try {
            await request(`/conversations/${state.selectedConversation.id}`, { method: 'DELETE' });
            state.conversations = state.conversations.filter((c) => c.id !== state.selectedConversation.id);
            state.selectedConversation = null;

            renderConversations();
            renderMessages();

            el.status.textContent = 'Conversation deleted.';
            closeConModal();
        } catch (e) {
            el.status.textContent = e.message;
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

        state.sending = true;
        el.sendBtn.disabled = true;
        el.status.textContent = 'Searching knowledge base and generating answer...';

        try {
            const payload = await request(`/conversations/${state.selectedConversation.id}/messages`, {
                method: 'POST',
                body: JSON.stringify({ question }),
            });
            state.selectedConversation.messages = [
                ...(state.selectedConversation.messages ?? []),
                payload.user_message,
                payload.assistant_message,
            ];
            state.selectedConversation.title = payload.conversation.title;
            syncConversation(payload.conversation);
            renderConversations();
            renderMessages();
            el.question.value = '';
            el.status.textContent = 'Answer generated from your knowledge base.';
        } catch (e) {
            el.status.textContent = e.message;
        } finally {
            state.sending = false;
            el.sendBtn.disabled = false;
        }
    };

    /* ── Wire events ── */
    el.msgForm?.addEventListener('submit', sendMessage);
    el.newConvBtn?.addEventListener('click', () => createConversation());
    el.deleteConvBtn?.addEventListener('click', deleteConversation);

    /* ── Initial render ── */
    renderConversations();
    renderMessages();

    if (shouldStartNew) {
        createConversation();
    }
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

    const state = {
        documents: bootState.documents ?? [],
        uploading: false,
    };

    const el = {
        docList: document.querySelector('#document-list'),
        docCount: document.querySelector('#document-count'),
        knowledgeForm: document.querySelector('#knowledge-form'),
        uploadStatus: document.querySelector('#upload-status'),
        deleteStatus: document.querySelector('#delete-status'),
        uploadBtn: document.querySelector('#upload-button'),
    };
    const docModal = document.getElementById('doc-delete-modal');
    const docBackdrop = document.getElementById('doc-modal-backdrop');
    const docCancel = document.getElementById('doc-modal-cancel');
    const docConfirm = document.getElementById('doc-confirm-delete');
    const docText = document.getElementById('doc-delete-text');

    const openDocModal = () => {
        docModal.style.display = '';
    };

    const closeDocModal = () => {
        docModal.style.display = 'none';
        selectedDocId = null;
    };


    /* ── Render ── */
    const renderDocuments = () => {
        el.docCount.textContent = String(state.documents.length);

        if (!state.documents.length) {
            el.docList.innerHTML = '<div class="empty-state">No knowledge documents indexed yet. Upload a file or paste content to get started.</div>';
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
                    <button class="btn btn-outline-danger btn-sm" type="button" data-doc-id="${d.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
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
    };

    /* ── Actions ── */
    const uploadDocument = async (event) => {
        event.preventDefault();
        if (state.uploading) return;
        state.uploading = true;
        el.uploadBtn.disabled = true;
        el.uploadStatus.textContent = 'Chunking and embedding document...';
        try {
            const formData = new FormData(el.knowledgeForm);
            const payload = await request(routes.knowledgeDocuments, { method: 'POST', body: formData });
            state.documents.unshift(payload.document);
            renderDocuments();
            el.knowledgeForm.reset();
            el.uploadStatus.textContent = payload.message;
            el.uploadStatus.style.color = 'var(--success)';
        } catch (e) {
            el.uploadStatus.textContent = e.message;
            el.uploadStatus.style.color = 'var(--danger)';
        } finally {
            state.uploading = false;
            el.uploadBtn.disabled = false;
        }
    };

    const deleteDocument = async (docId) => {
        if (!confirm('Delete this knowledge document? This cannot be undone.')) return;
        el.deleteStatus.textContent = 'Deleting document...';
        try {
            await request(`/knowledge-documents/${docId}`, { method: 'DELETE' });
            state.documents = state.documents.filter((d) => d.id !== Number(docId));
            renderDocuments();
            el.deleteStatus.textContent = 'Document deleted.';
            el.deleteStatus.style.color = 'var(--success)';
        } catch (e) {
            el.deleteStatus.textContent = e.message;
            el.deleteStatus.style.color = 'var(--danger)';
        }
    };
    docConfirm.addEventListener('click', async () => {
        if (!selectedDocId) return;

        el.deleteStatus.textContent = 'Deleting document...';
        el.deleteStatus.style.color = 'var(--danger)';

        try {
            await request(`/knowledge-documents/${selectedDocId}`, {
                method: 'DELETE'
            });

            state.documents = state.documents.filter(
                d => d.id !== Number(selectedDocId)
            );

            renderDocuments();

            el.deleteStatus.textContent = 'Document deleted.';
            el.deleteStatus.style.color = 'var(--success)';

            closeDocModal();
        } catch (e) {
            el.deleteStatus.textContent = e.message;
            el.deleteStatus.style.color = 'var(--danger)';
        }
    });
    docCancel.addEventListener('click', closeDocModal);
    docBackdrop.addEventListener('click', closeDocModal);
    /* ── Wire events ── */
    el.knowledgeForm?.addEventListener('submit', uploadDocument);

    /* ── Initial render ── */
    renderDocuments();
}

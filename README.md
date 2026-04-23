# My AI App

My AI App is a Laravel-based knowledge assistant that lets each authenticated user build a private searchable knowledge base and ask questions against it.

The system takes user-provided documents, splits them into chunks, generates embeddings for those chunks, retrieves the most relevant chunks for a question, and then sends that retrieved context to an LLM to produce a grounded answer.

The active AI integration in this project uses `laravel/ai` with Gemini as the configured provider for embeddings and text generation inside the RAG flow.

## What the system actually does

This project is a basic RAG system with three main layers:

1. Knowledge ingestion
2. Retrieval
3. Answer generation with conversation history

At a high level:

- A user uploads a text file or pastes raw content.
- A user can later replace an existing document and re-index it without creating a duplicate source.
- The system stores the original document as a `knowledge_document`.
- The content is split into smaller chunks.
- Each chunk gets a high-resolution embedding vector (3072 dimensions).
- The chunks and embeddings are stored in a **PostgreSQL vector database** (pgvector).
- When the user asks a question (via web or **Telegram**), the system embeds the question.
- The system performs a **vector similarity search** using Laravel's native `whereVectorSimilarTo` method, leveraging the database's optimized vector operators for speed and accuracy.
- The top matching chunks are used as context for the LLM.
- The generated answer is stored in the conversation along with citations.

## Main resources in the system

### 1. Knowledge documents

This is the top-level source material uploaded by a user.

Stored in `knowledge_documents`:

- `user_id`
- `title`
- `source_name`
- `source_type`
- `original_content`
- `chunk_count`

This is the source from which searchable chunks are created.

### 2. Document chunks

Each knowledge document is broken into smaller chunks and stored in `documents`.

Stored in `documents`:

- `knowledge_document_id`
- `content`
- `embedding`
- `chunk_index`
- `character_count`
- `source_name`
- `metadata`

These chunk rows are the actual retrieval resource used during search.

### 3. Conversations

Each user can have multiple conversations.

Stored in `conversations`:

- `user_id`
- `title`
- `last_message_at`

Conversations hold the message history for chat.

### 4. Messages

Stored in `messages`:

- `conversation_id`
- `role`
- `content`
- `citations`
- `meta`

Messages store both the user question and the assistant answer. Assistant messages can include citations and usage metadata.

## Where the data comes from

The system currently supports two ingestion inputs:

- pasted text content
- uploaded text-based files
- **Telegram document attachments** (automatically ingested into your knowledge base)

Allowed uploaded file types:

- `txt`
- `md`
- `markdown`
- `csv`
- `json`
- `pdf` (Fully supported via automated parsing)

## Telegram Integration

The system includes a full-featured Telegram bot that acts as a mobile interface for your private knowledge base.

### Features:
- **Automated Account Linking**: Link your site account to Telegram with a single click in your profile using secure deep-linking (`/start <token>`).
- **Mobile Q&A**: Ask questions to your knowledge base directly from Telegram.
- **Conversational Memory**: The bot maintains context across your messages for a natural chat experience.
- **Document Ingestion**: Forward or upload documents to the bot to automatically add them to your knowledge base.
- **Rich UI**: Support for Markdown formatting and real-time typing indicators.

### Setup:
1. Set `TELEGRAM_API_KEY`, `TELEGRAM_BOT_URL` and `TELEGRAM_BOT_USERNAME` in your `.env`.
2. Register the webhook using `php artisan telegram:set-webhook <your-url>`.
3. Click "Link Telegram Account" in your user profile.
- `log`

The file contents are read as text, trimmed, and then passed into the ingestion pipeline.

Existing indexed documents can also be replaced through the knowledge base UI. In that flow, the system keeps the same top-level `knowledge_document` record, deletes its old chunk rows, and regenerates chunks and embeddings from the replacement content.

## How ingestion works

Ingestion is handled mainly by:

- `app/Http/Controllers/KnowledgeDocumentController.php`
- `app/Services/RagService.php`
- `app/Services/TextChunker.php`
- `app/Services/OpenAIService.php`
- `app/Services/TelegramService.php`
- `Smalot\PdfParser\Parser`
- `Laravel\Ai\Embeddings`

Flow:

1. The controller receives either a file or pasted content.
2. It validates input and resolves the final title and source info.
3. `RagService::ingest()` is called.
4. `TextChunker::split()` breaks the content into overlapping chunks.
5. `OpenAIService::embeddings()` calls `Laravel\Ai\Embeddings` to generate embeddings through the SDK.
6. A `knowledge_document` row is created.
7. Each chunk is stored in `documents` with its embedding and metadata.

Re-index flow:

1. The user clicks `Replace` on an existing indexed document.
2. The controller validates the replacement file or pasted content.
3. `RagService::reindex()` is called.
4. The existing document's chunk rows are deleted.
5. The same `knowledge_document` row is updated with the new title, source info, original content, and chunk count.
6. New chunks and embeddings are generated and stored in `documents`.

This keeps the document identity stable while refreshing the searchable content cleanly.

Chunking behavior:

- The chunker normalizes large whitespace blocks.
- It targets chunk sizes around 1200 characters.
- It keeps overlap between chunks to preserve context continuity.

## How retrieval works

Retrieval is handled by:

- `app/Services/RagService.php`
- `app/Services/SimilarityService.php`

Flow:

1. The user sends a question in a conversation.
2. The system creates an embedding for that question through `laravel/ai`.
3. The system performs a native **pgvector** similarity search using the `<=>` (cosine distance) operator via Laravel's `whereVectorSimilarTo`.
4. The database filters and sorts the most relevant chunks based on vector distance.
5. The top matches are returned directly to the application layer.
6. If the best score is too low (below the similarity threshold), the assistant informs the user that the answer is not in the knowledge base.

### Performance Note:
Unlike traditional RAG implementations that load all embeddings into memory, this system uses **database-level vector search**. This allows the knowledge base to scale to thousands of documents without compromising response time.

## How answer generation works

Answer generation is handled by:

- `app/Http/Controllers/ChatController.php`
- `app/Services/RagService.php`
- `app/Services/OpenAIService.php`
- `Laravel\Ai\agent(...)`

Flow:

1. The question is saved as a user message.
2. The system loads up to the most recent 8 conversation messages as history.
3. The top retrieved chunks are formatted into a context block.
4. That context plus the question and recent history are sent to an anonymous `laravel/ai` agent.
5. The assistant response is saved as a message.
6. The assistant message stores citations for the matched chunks used as sources.
7. The conversation title is auto-generated from the first user message.

The prompt logic explicitly tells the model:

- answer only from supplied knowledge base context
- say the answer is unavailable if the context is insufficient

## Laravel AI integration

The project includes `laravel/ai` and the live RAG flow now uses it directly.

### Where `laravel/ai` is used

- `app/Services/OpenAIService.php`
- `config/ai.php`

### What it is doing in this project

`laravel/ai` is currently used for two things:

1. Embeddings generation
2. LLM text generation for grounded answers

### How it is used

For embeddings:

- `OpenAIService::embeddings()` calls `Laravel\Ai\Embeddings::for(...)->generate(...)`
- Provider is forced to `Lab::Gemini`
- The resulting vectors are stored in the `documents.embedding` column

For answer generation:

- `OpenAIService::answerQuestion()` creates an anonymous SDK agent using `Laravel\Ai\agent(...)`
- Recent conversation history is converted into `Laravel\Ai\Messages\Message` objects
- The prompt includes the retrieved knowledge context
- The SDK sends the prompt to Gemini and returns structured usage/meta data

### What `laravel/ai` is not doing yet

- It is not managing the app's own `conversations` or `messages` tables
- It is not using SDK-managed conversation persistence yet

However, it is now deeply integrated into the retrieval layer through the standard Laravel query builder, allowing for seamless vector stores.

Those parts still remain in your own application layer:

- `RagService` handles ingestion orchestration and answer orchestration
- `SimilarityService` handles cosine similarity
- `Conversation`, `Message`, `KnowledgeDocument`, and `Document` remain your app's own storage model

## AI provider currently used

The current implementation uses Gemini for:

- embeddings
- answer generation

Configured through:

- `config/ai.php`
- `config/services.php`
- `app/Services/OpenAIService.php`

Important note:

- The class is still named `OpenAIService`, but it now uses `laravel/ai` with Gemini under the hood.
- So the active provider for this system's RAG flow is Gemini, routed through the Laravel AI SDK.

Relevant environment variables:

```env
GEMINI_API_KEY=your_gemini_api_key
GEMINI_CHAT_MODEL=gemini-2.5-flash-lite
GEMINI_CHAT_VERSION=v1
GEMINI_EMBEDDING_MODEL=gemini-embedding-001
GEMINI_EMBEDDING_VERSION=v1beta
GEMINI_TIMEOUT=60
```

Related SDK config:

- `config/ai.php` sets:
  - `default => gemini`
  - `default_for_embeddings => gemini`
- `config/ai.php` also maps:
  - `providers.gemini.models.text.default`
  - `providers.gemini.models.embeddings.default`

## User scoping and ownership

The system is user-scoped.

- Knowledge documents are filtered by `user_id`.
- Conversations are filtered by `user_id`.
- Retrieval only searches chunks belonging to the current user's documents.
- One user's indexed resources are not used in another user's retrieval flow.

This is enforced mainly through:

- `Conversation::scopeForUser()`
- `KnowledgeDocument::scopeForUser()`
- `abort_unless(... === $request->user()->id, 404)` checks in controllers
- `whereHas('knowledgeDocument', fn (...) => $query->where('user_id', $userId))` in retrieval

## Main backend files

### Controllers

- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/ConversationController.php`
- `app/Http/Controllers/ChatController.php`
- `app/Http/Controllers/KnowledgeDocumentController.php`

### Services

- `app/Services/RagService.php`
- `app/Services/TextChunker.php`
- `app/Services/OpenAIService.php`
- `app/Services/TelegramService.php`

### Models

- `app/Models/KnowledgeDocument.php`
- `app/Models/Document.php`
- `app/Models/Conversation.php`
- `app/Models/Message.php`

## Main routes

### HTML pages

- `/dashboard`
- `/conversations`
- `/knowledge`
- `/profile`
- `/log-viewer`

### JSON endpoints

- `POST /conversations`
- `GET /conversations/{conversation}`
- `DELETE /conversations/{conversation}`
- `POST /conversations/{conversation}/messages`
- `GET /knowledge-documents`
- `POST /knowledge-documents`
- `PUT /knowledge-documents/{knowledgeDocument}/reindex`
- `DELETE /knowledge-documents/{knowledgeDocument}`

## Project images

Screenshots and UI references for the project are available here:

- Screenshot folder: https://www.awesomescreenshot.com/s/folder/F02ravZvo8/28d0cabfa1677c60679fc7a9b057a0f0

This folder can be used for:

- landing page screenshots
- authentication screens
- dashboard views
- conversations interface
- knowledge base pages

## Log viewer

The project includes `opcodesio/log-viewer` for inspecting Laravel logs in the browser.

Use:

- `{APP_URL}/log-viewer`

This is useful for checking runtime errors, exceptions, failed requests, and application log entries without opening the raw log files manually.

## Setup

### Requirements

- PHP 8.2+
- Composer
- Node.js and npm
- Configured database
- Gemini API key

### Install

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### Development

```bash
composer run dev
```

This starts the Laravel server, queue listener, log tailing, and Vite dev server together.

## Current limitations

- Re-indexing replaces a document's existing chunks and embeddings; there is no document version history yet.
- The service name `OpenAIService` does not match its current role as a Laravel AI SDK wrapper for Gemini.
- `laravel/ai` is not yet managing the application's conversation memory tables directly.
- The README reflects the current code implementation, not a generalized future architecture.

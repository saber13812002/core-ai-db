---
paths:
  - app/Models/**
---

# Models

## SoftDeletes and versioning on core models

Core models (especially ProcessedOutput and MasterPrompt) use SoftDeletes. A correction creates a new version row; the original is never updated in place.

## MasterPrompt is git-like

MasterPrompt keeps a version history with a content hash. Every output references the exact prompt version id, never the "current" prompt.

## SourceFile identity metadata

SourceFile carries the identity fields: instructor, topic, exact date, project title, chapter/session, document type, and OCR/ASR processing status. Chunks of long files also carry token-limit and overlap-percent metadata so they can be tuned per model context window.

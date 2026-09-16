---
paths:
  - '**'
---

# General

## Cartesian product output identity

Every ProcessedOutput is the product of SourceFile x automation action x AI model x MasterPrompt version. No output may be stored without all four coordinates. Re-running with any changed factor creates a new record; it never replaces the old one.

## Traceability is mandatory

No record may be stored without a back-link to the source file at least at second level (audio/video) or page level (PDF/OCR). This enables hallucination checking and human confirmation; it is a binding requirement of the design, not optional metadata.

## Immutable data

Never overwrite stored data. Corrections and re-runs create new version rows; removals use soft deletes. The full production history must survive nightly re-batching of the entire corpus.

## Nine required entities

The schema must contain: SourceFile, MasterPrompt, AutomationFlow, AutomationJob, ProcessedOutput, HumanGroundTruth, BenchmarkResult, VectorCollection, FeedbackLog. A feature that drops one of these is out of scope until the design is re-approved.

---
paths:
  - database/**
---

# Database

## EstimatedCost before batch runs

AutomationJob records an EstimatedCost (token and time estimate computed from data volume) before the batch starts, reported to the system administrator before execution.

## QualityRate 0-100 against HumanGroundTruth

BenchmarkResult compares machine output paragraph-by-paragraph with the expert-approved HumanGroundTruth and stores a QualityRate from 0 to 100. This rate is the basis for promoting prompt versions.

## FeedbackLog ties to prompt-model pairs

Application-level feedback (FeedbackLog) is linked to the specific prompt and model pair that produced the output, so later prompt versions can correct observed failures.

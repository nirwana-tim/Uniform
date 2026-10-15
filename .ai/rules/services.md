---
paths:
  - 'app/Services/**'
---

# Services

## SCM Partial Distribution and Net Demand Rules
When validating distribution transactions, check whether the student has unfulfilled entitlement items before throwing a duplicate schedule exception, allowing partial fulfillment and follow-up pickups. When calculating demand, filter to active students and exclude items already distributed in non-cancelled transactions.

# Architecture Decision Records

Each ADR captures one decision + the alternatives we considered.
Sequence is informational; later ADRs may supersede earlier ones
(explicit `Supersedes: ADR-NNNN` header in that case).

| # | Title | Status | Date |
|---|---|---|---|
| 0001 | Repo bootstrap & stack choices | Pending bootstrap | — |
| 0002 | Multi-tenancy approach (stancl vs hand-rolled) | Pending bootstrap | — |
| 0003 | OAuth providers | Pending bootstrap | — |
| 0004 | Deployment target (Forge / Vapor / self-host) | Pending bootstrap | — |
| 0005 | Background worker driver (Redis / database queue) | Pending bootstrap | — |
| 0006 | Where the registry signed-URL secret lives | Pending bootstrap | — |

## Conventions

- ADRs are numbered sequentially (`0001`, `0002`, …) starting at
  the first commit after bootstrap.
- Each ADR lives at `docs/adr/<NNNN>-<slug>.md`.
- Status: Proposed → Accepted → Implemented → Superseded.
- Every PR that lands an ADR must update this index.

## When to write an ADR

Any decision that:

- Removes a future option (we picked stancl, can't easily move).
- Affects multiple subsystems (auth flow + multi-tenancy together).
- Was contentious in PR review (write it down to avoid relitigating).
- Future-you will ask "why did we do it this way?" within 6 months.

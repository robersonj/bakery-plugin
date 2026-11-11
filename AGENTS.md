# Project instructions

## Scope

This repository contains only the Bakery Ordering WordPress plugin. Keep changes inside this plugin directory unless the user explicitly requests otherwise. Read README.md and docs/PROJECT.md before implementation.

## Development

- Inspect existing work before editing and preserve unrelated changes.
- Keep changes focused on the user's requested behavior.
- Do not invent business rules; record unresolved requirements in docs/PROJECT.md.
- Follow WordPress conventions. Prefix plugin functions, options, hooks, and other global identifiers consistently to avoid collisions.
- Validate and sanitize input, escape output for its context, and enforce capabilities and nonces for privileged actions. Nonces do not replace authorization checks.
- Keep secrets, customer data, database exports, and machine-specific paths out of committed source.
- Do not add dependencies or build tooling without a concrete need.

## Verification and handoff

- Run PHP syntax checks on changed PHP files when PHP is available.
- Verify changed WordPress behavior in Local when available; state clearly when runtime verification was not performed.
- Add meaningful tests for substantive logic when a test environment is established.
- Update docs/DEVELOPMENT-LOG.md with significant changes, verification, and the next step.
- Update docs/MAC-STUDIO-HANDOFF.md when setup or handoff instructions change.
- Do not commit or push unless requested by the user.

*Resume with /user-next. Full state: docs/sessions/2026-11-11-php-patch-and-post-to-prod.md.*

## Next steps

1. Merge, push and deploy `fix/playground-offline` (Bernard): takes the broken playground off sinfat.com.
2. Backlog row 9, due 2026-11-25: choose the AI provider that replaces GitHub Models, then switch the
   playground back on (`PLAYGROUND_ENABLED=true`).
3. Then: module 15, Page Explainer (`specs/module-15-page-explainer.md`); Laravel mail (SMTP via
   Resend) with an AI health check that alerts when the provider stops answering.

Owed work: `docs/backlog.md`. Deploy, server and PHP patching: `docs/deploy-guide.md`.

## Git state

- `main` at `66adda9`, pushed. Branch `fix/playground-offline`: the docs moves and the playground switch, not merged.

## Plans in flight

None. Each module's spec in `specs/` carries its own status.

# Agent Instructions

This project customizes a Gazelle fork to produce a web1.0-style static music library.
Follow these guidelines when modifying code in this repository:

1. **Web1.0 static library**: The site must not allow user accounts or user-generated content. Only administrators may update a static music library with links to external streaming platforms.
2. **Remove unrelated features**: Remove logic, functionality and data structures not directly related to representing the music library (e.g., BitTorrent tracking, user accounts, forums) when possible.
3. **Reuse existing structures**: Where possible reuse existing data structures and logic. For example, a release page that once linked to torrent files should become a similar page that links to different music streaming platforms.
4. **Preserve necessary code**: Leave code unchanged when it is necessary for the site to function or can be used directly (e.g., search logic, music release metadata, artwork handling).
5. **Questionable reuse**: If the reusability of a domain, field or entry is uncertain, leave the code unchanged and describe it in the final output with exact paths and line numbers along with how it might be reused.
6. **Code quality and tests**: Write clean, maintainable code covered by tests. When reasonable, follow existing design and architectural patterns. Run `make lint-staged` and `make test` after making changes.
7. **Database diagrams**: Use `docs/plantuml/current.puml` (current schema) and `docs/plantuml/target.puml` (target schema) as references.
The target schema is not finalized and will be extended. Do not modify `docs/plantuml/current.puml` under any circumstances.
Only edit `docs/plantuml/target.puml` when explicitly directed in the prompt.


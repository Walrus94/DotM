# Agent Instructions

This project customizes a Gazelle fork to produce a web1.0-style static music library.
Follow these guidelines when modifying code in this repository:

1. **Core focus**: The site remains a web1.0-style static music library, but forums, comments, private messaging, and donations are supported features.
2. **Remove unrelated features**: Remove logic, functionality and data structures not directly related to the library or these supported social features. BitTorrent tracking as well as legacy social features such as requests and collages remain deprecated.
3. **Reuse existing structures**: Where possible reuse existing data structures and logic. For example, a release page that once linked to torrent files should become a similar page that links to different music streaming platforms.
4. **Preserve necessary code**: Leave code unchanged when it is necessary for the site to function or can be used directly (e.g., search logic, music release metadata, artwork handling).
5. **Questionable reuse**: If the re-usability of a domain, field or entry is uncertain, leave the code unchanged and describe it in the final output with exact paths and line numbers along with how it might be reused.
6. **Code quality and tests**: Write clean, maintainable code covered by tests. When reasonable, follow existing design and architectural patterns. Keep the code coverage percentage unchanged when possible by adding or adjusting tests for newly created or modified code. Run `make lint-staged` and `make test` after making changes.
7. **Refactoring workflow**: Refactor features in the following order: view templates → controllers → domain logic → database schema. In this repository, `templates/` renders views, `sections/` contains controllers, and `app/` holds domain logic.


# Agent Instructions

This project customizes a Gazelle fork to produce a web1.0-style static music library.
Follow these guidelines when modifying code in this repository:

1. **Core focus**: The site remains a web1.0-style static music library, but forums, comments, private messaging, and donations are supported features.
2. **Refactoring workflow**: Refactor features in the following order: view templates → controllers → domain logic → database schema. In this repository, `templates/` renders views, `sections/` contains controllers, and `app/` holds domain logic.
3. **Remove unrelated features**: Remove logic, functionality and data structures not directly related to the library or these supported social features. BitTorrent tracking as well as legacy features such as requests and collages remain deprecated. 
4. **Reuse existing structures**: Where possible reuse existing data structures and logic. For example, a release page that once linked to torrent files should become a similar page that links to different music streaming platforms.
5. **Preserve necessary code**: Leave code unchanged when it is necessary for the site to function or can be used directly (e.g., search logic, music release metadata, artwork handling).
6. **Questionable reuse**: If the re-usability of a domain, field or entry is uncertain, leave the code unchanged and describe it in the final output with exact paths and line numbers along with how it might be reused.
7. **Prepared data**: You could refer to 'docs/refactoring_data/features_matrix.csv' to check on which functions and features category as well as codebase they reference to. But don't rely on this data much because it's not really accurate. And be extremely cautious when changing classes or packages from 'docs/refactoring_data/danger_zone.txt'. You could reference 'docs/refactoring_data/diagrams/default.puml' to get initial database structure and 'docs/refactoring_data/diagrams/current.puml' to get current database structure.
8. **Code quality and tests**: Write clean, maintainable code covered by tests. When reasonable, follow existing design and architectural patterns. Keep the code coverage percentage unchanged when possible by adding or adjusting tests for newly created or modified code.


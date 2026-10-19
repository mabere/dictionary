# MASTER PROMPT — COMPREHENSIVE AUDIT, SECURITY REVIEW, QA, CODE QUALITY & PRODUCTION READINESS
## Laravel Multilingual Dictionary Application — Pre-Production Assessment

### 1. ROLE AND MISSION

Act as a coordinated team of senior software engineering specialists:

- Senior Laravel/PHP Architect
- Application Security Auditor
- OWASP Application Security Specialist
- Database Architect and Performance Engineer
- QA Engineer and Test Automation Specialist
- UI/UX and Accessibility Reviewer
- DevOps and Production Deployment Engineer
- Code Quality and Maintainability Reviewer

You are auditing an existing multilingual dictionary web application built with Laravel that is approximately 75–80% complete.

Your mission is to conduct a comprehensive, evidence-based audit of the existing project, identify weaknesses and defects, assess security and operational risks, recommend improvements, and determine whether the application is ready for production deployment.

Do not assume the application is complete, secure, or correctly implemented simply because some features work.

**Primary objectives:**

1. Discover functional bugs, incomplete features, logical errors, and regressions.
2. Identify security vulnerabilities and insecure configurations.
3. Review Laravel architecture, code quality, maintainability, and adherence to framework conventions.
4. Validate database schema, relationships, constraints, queries, and data integrity.
5. Evaluate dictionary-specific functionality, including multilingual data, translations, search, and language relationships.
6. Assess performance, scalability, usability, accessibility, and responsive design.
7. Review dependencies, configuration, secrets, logging, and error handling.
8. Prepare a safe, reproducible deployment strategy for the actual target hosting environment.
9. Create a prioritized remediation plan with measurable acceptance criteria.
10. Verify implemented fixes through appropriate tests and evidence.

The final result must be a practical engineering audit, not a generic checklist.

---

### 2. NON-NEGOTIABLE AUDIT RULES

Follow these rules throughout the assessment.

**Rule A — Inspect before changing**

First inspect the repository and establish the current state of the application. Do not immediately rewrite files, refactor the architecture, replace packages, or modify the database.

**Rule B — Establish the actual environment**

Identify the installed Laravel version, PHP version requirements, database engine, frontend stack, authentication implementation, dependency manager, build tools, and deployment constraints from the repository and available environment.

Do not assume that the project uses the latest Laravel version or a particular frontend framework.

**Rule C — Evidence-based findings**

Every significant finding must include, where applicable:

- Finding ID.
- Severity and priority.
- File path and relevant line numbers.
- Description of the defect or risk.
- Evidence from the actual implementation.
- Conditions required for the issue to occur.
- Potential impact.
- Recommended remediation.
- Verification procedure.
- Regression tests.
- Confidence level.

Distinguish confirmed vulnerabilities from potential risks, missing evidence, design recommendations, and unverified assumptions.

Never claim that a vulnerability exists merely because it is theoretically possible.

**Rule D — Preserve working functionality**

Do not remove existing features, change business rules, alter public routes, modify database structures, or replace libraries without understanding their purpose and impact.

Maintain backward compatibility unless a justified change is explicitly approved.

**Rule E — Safe execution**

Initially operate in read-only audit mode.

Do not execute destructive database commands, reset databases, drop tables, truncate data, delete user uploads, modify production services, expose secrets, or perform intrusive security testing against live systems.

Do not run database migrations against production during the audit.

Do not commit, push, publish, or deploy changes without explicit authorization.

**Rule F — No fabricated results**

If a test cannot be executed because dependencies, credentials, services, or infrastructure are unavailable, report it as NOT RUN and explain why.

A passing static review is not proof that runtime behavior is correct. A passing test suite is not proof that the application is secure.

**Rule G — Preserve project context**

Review the existing implementation before proposing architectural changes. Prefer the smallest reliable correction that addresses the underlying problem.

---

### 3. PHASE 1 — PROJECT DISCOVERY AND BASELINE

Begin by examining the repository structure and collecting an inventory of the application.

Inspect relevant files and directories, including, when present:

- `composer.json` and `composer.lock`
- `package.json` and the applicable JavaScript lockfile
- `.env.example` and configuration files
- `artisan`
- `routes/web.php`, `routes/api.php`, and other route files
- `app/Models`
- `app/Http/Controllers`
- `app/Http/Middleware`
- `app/Http/Requests`
- `app/Policies` and authorization logic
- `app/Services`, `app/Actions`, or equivalent business logic
- `database/migrations`
- `database/seeders` and factories
- `resources/views`, frontend source files, and components
- `public` and static assets
- `storage` and `bootstrap/cache`
- `config`
- `tests`
- CI/CD workflows, deployment scripts, Docker files, and hosting instructions, if present.

Do not assume that every directory exists.

Determine:

1. Laravel and PHP versions.
2. Required PHP extensions.
3. Database type and compatibility.
4. Authentication and authorization mechanisms.
5. Frontend technology and asset compilation process.
6. Cache, queue, session, email, and filesystem drivers.
7. API architecture, if applicable.
8. Testing framework and existing test coverage.
9. Current environment configuration conventions.
10. Expected production hosting environment.
11. Existing features that are complete, partial, broken, or undocumented.
12. Existing architectural constraints and technical debt.

Inspect Git status and recent project changes when available. Preserve existing user modifications.

**Deliverable:** Project Inventory and Baseline Report.

Include a feature inventory with statuses: COMPLETE, PARTIAL, BROKEN, MISSING, or NOT VERIFIED.

Do not infer completion merely from the presence of a controller, route, or view.

---

### 4. PHASE 2 — FUNCTIONAL AUDIT AND BUSINESS LOGIC

Test each implemented feature against its intended behavior.

For a multilingual dictionary application, inspect the following areas where applicable.

#### A. Language management

- Create, read, update, and delete language records.
- Validate language names, native names, language codes, and locale identifiers.
- Detect duplicate language codes and inconsistent naming.
- Check relationships between languages and dictionary entries.
- Prevent deletion of languages that are still referenced by entries unless a safe, intentional policy exists.
- Verify whether language activation, deactivation, and visibility behave correctly.
- Check sorting, filtering, and pagination.

#### B. Dictionary entries

- Verify word and translation relationships.
- Validate source language and target language associations.
- Detect missing, invalid, duplicated, or contradictory data.
- Check optional fields such as definitions, pronunciation, examples, grammatical categories, synonyms, and notes, if implemented.
- Verify CRUD operations and authorization.
- Test empty values, whitespace, Unicode characters, and long text.
- Review duplicate detection rules and whether they respect the application's intended language and normalization rules.
- Check update operations for accidental overwriting of unrelated fields.
- Verify deletion behavior and referential integrity.

#### C. Search and lookup

- Exact word lookup.
- Partial-word search.
- Prefix and substring search, where supported.
- Source and target language filtering.
- Search behavior for diacritics, accents, apostrophes, hyphens, and non-Latin scripts.
- Case sensitivity and normalization.
- Empty queries and whitespace-only input.
- Very long queries and unusual Unicode input.
- Pagination and sorting.
- No-result behavior.
- Search performance on large datasets.
- Prevention of SQL injection and unsafe raw query construction.

Do not impose one universal Unicode normalization rule on all languages. Evaluate the application's intended linguistic behavior.

#### D. Translation and language direction

- Verify source-to-target translation direction.
- Ensure results are not accidentally reversed.
- Validate language-pair selection and switching.
- Handle missing translations gracefully.
- Verify bidirectional relationships only where the data model supports them.
- Do not assume that a translation from language A to B implies an identical reverse entry from B to A.
- Check whether duplicate entries across different language pairs are handled correctly.

#### E. Other features

Audit authentication, registration, login, logout, profile management, password reset, administration, roles, dashboards, imports, exports, favorites, history, API endpoints, and other modules only when present.

For every feature, record:

- Expected behavior.
- Actual implementation.
- Test performed.
- Result.
- Defect or risk.
- Required correction.

**Deliverable:** Functional Defect Matrix and Feature Completion Report.

---

### 5. PHASE 3 — SECURITY AUDIT

Perform a comprehensive defensive security review based on OWASP application security guidance and Laravel-specific security practices.

Use a risk-based approach. Consider the impact, exploitability, exposure, and existing mitigations of each issue.

#### A. Authentication

Review:

- Password hashing using supported Laravel mechanisms.
- Login validation and error handling.
- Login throttling and brute-force resistance.
- Session regeneration after authentication.
- Logout invalidation and session handling.
- Password reset token security and expiration.
- Account enumeration risks.
- Email verification if required.
- Multi-factor authentication if implemented or required by the threat model.
- Administrative account protection.
- Authentication behavior across web and API routes.

Do not introduce unnecessary authentication complexity without considering the application's actual requirements.

#### B. Authorization and access control

Review:

- Middleware coverage.
- Gates, policies, roles, and permissions.
- Administrative routes.
- User ownership checks.
- Insecure direct object references (IDOR).
- Privilege escalation.
- Unauthorized creation, editing, or deletion.
- Mass assignment.
- Access to hidden, draft, private, or unpublished dictionary entries.
- API authorization and resource exposure.

Verify that hiding a button in the frontend is not treated as an authorization control.

Create a role-versus-permission matrix based on the roles that actually exist.

#### C. Input validation and injection

Check:

- SQL injection.
- Unsafe raw SQL.
- Unsafe query construction.
- Cross-site scripting (XSS).
- Cross-site request forgery (CSRF).
- Mass-assignment vulnerabilities.
- Server-side request forgery (SSRF), where server-side URL fetching exists.
- Command injection, where shell commands or external processes exist.
- Unsafe deserialization, where applicable.
- Open redirects.
- Path traversal.
- Header injection.
- Unsafe regular expressions and denial-of-service risks.

Inspect Laravel validation rules, Form Requests, query bindings, output escaping, and unsafe rendering patterns.

Do not treat client-side validation as a substitute for server-side validation.

#### D. File uploads and imports

If the application supports uploads, dictionary imports, images, documents, or exported files, review:

- File size limits.
- Extension and MIME-type validation.
- Actual content validation where appropriate.
- Randomized filenames.
- Storage outside the public web root when appropriate.
- Prevention of executable uploads.
- File authorization and access control.
- Archive extraction risks.
- CSV and spreadsheet formula injection.
- Maliciously crafted imports.
- Resource exhaustion.
- Cleanup and retention policies.

Do not assume that an upload feature exists. Mark it NOT APPLICABLE if absent.

#### E. Secrets and configuration

Inspect for:

- Committed `.env` files.
- Exposed database credentials.
- Exposed API keys and SMTP credentials.
- Hard-coded secrets.
- Weak or exposed `APP_KEY` values.
- Debug mode enabled in production.
- Verbose production exception pages.
- Insecure session and cookie settings.
- Overly permissive CORS configuration.
- Incorrect trusted proxy settings.
- Insecure filesystem permissions.
- Publicly accessible logs, backups, SQL dumps, and configuration files.
- Secret leakage in frontend bundles, repository history, or error messages.

Do not print secret values in the report. Identify the affected location and secret type, and redact the value.

If a real secret has been exposed, recommend appropriate rotation and revocation.

Never change the production encryption key as a routine precaution; assess the consequences first.

#### F. HTTP security and web-server configuration

Review the existing hosting configuration and any `.htaccess`, Apache, Nginx, or equivalent rules.

Check:

- Whether the document root points to Laravel's `public` directory.
- Whether application source files and configuration files are inaccessible over HTTP.
- Directory listing prevention.
- HTTPS enforcement.
- Secure redirect behavior.
- HTTP security headers, including CSP where feasible, HSTS when HTTPS deployment is confirmed, `X-Content-Type-Options`, appropriate framing protection, and a suitable referrer policy.
- Cookie security flags.
- Access to hidden files.
- Exposure of backup, temporary, log, dump, and editor-generated files.
- Static asset delivery and route compatibility.
- Correct handling of Laravel's front controller and application routing.
- Compatibility with shared hosting restrictions.

Do not blindly apply generic `.htaccess` rules that could break routing, API endpoints, assets, uploads, or legitimate application functionality.

For each proposed server rule, explain its purpose, compatibility assumptions, and test procedure.

Do not expose `vendor`, application source, storage files, or environment configuration through the public web root.

#### G. API security

If APIs exist, review:

- Authentication and authorization.
- Token storage and expiration.
- Rate limiting.
- Request validation.
- Resource serialization and excessive data exposure.
- CORS.
- Pagination and query limits.
- API versioning where applicable.
- Abuse of expensive endpoints.
- Consistent error responses.
- Protection of administrative endpoints.

#### H. Abuse resistance

Evaluate:

- Login brute-force attempts.
- Excessive dictionary searches.
- Enumeration of user or dictionary records.
- Expensive search queries.
- Oversized payloads.
- Import abuse.
- Email or password-reset abuse.
- Automated scraping, if relevant to the business model.
- Resource exhaustion.

Recommend rate limits and quotas appropriate to the actual features and expected traffic.

#### I. Security findings format

Assign each confirmed finding:

- Critical
- High
- Medium
- Low
- Informational

Use a consistent severity methodology based on impact and exploitability. Include a CVSS score only when a defensible assessment is possible.

For every security finding, include:

1. Finding ID, such as SEC-001.
2. Severity and confidence.
3. Affected file, function, route, or configuration.
4. Evidence.
5. Attack preconditions and affected security boundary.
6. Potential impact.
7. Root cause.
8. Recommended fix.
9. Safe verification procedure.
10. Regression tests.
11. Residual risk after remediation.

Use non-destructive tests against a local or explicitly authorized staging environment. Do not exploit live third-party systems or exfiltrate real user data.

**Deliverable:** Security Audit Report and Prioritized Remediation Plan.

---

### 6. PHASE 4 — LARAVEL ARCHITECTURE AND CODE QUALITY

Review whether the code follows appropriate conventions for the installed Laravel version.

Inspect:

- Controller responsibilities.
- Business logic separation.
- Model design and relationships.
- Form Requests and validation.
- Policies, gates, and middleware.
- Service classes and reusable components.
- Route organization and route naming.
- API resources and serialization.
- Dependency injection.
- Exception handling.
- Transactions and data consistency.
- Configuration management.
- Event and listener usage, where appropriate.
- Jobs and queues, if implemented.
- Naming consistency.
- Duplication and overly complex methods.
- Dead code and unreachable branches.
- Unused dependencies.
- Tight coupling.
- Inconsistent error handling.
- Missing return types or unclear contracts where relevant.
- Testability and maintainability.

Check for:

- Business logic duplicated across controllers.
- Controllers that are excessively large.
- N+1 database queries.
- Unnecessary queries and repeated lookups.
- Misuse of facades or global helpers that harms testability.
- Overengineering and unnecessary abstraction.
- Incorrect use of Laravel conventions.
- Unsafe assumptions about request data.
- Hard-coded business rules.
- Inconsistent handling of null values and missing relationships.

Do not recommend design patterns merely for their own sake.

For each improvement, classify it as:

- Required correction.
- Recommended refactor.
- Optional enhancement.
- Future architectural consideration.

Prefer targeted refactoring over wholesale rewriting.

**Deliverable:** Code Quality Report, Technical Debt Register, and Refactoring Roadmap.

---

### 7. PHASE 5 — DATABASE AND DATA INTEGRITY

Inspect migrations, models, relationships, seeders, factories, and representative queries.

Review:

1. Primary keys and foreign keys.
2. Unique constraints.
3. Nullability and default values.
4. Referential integrity.
5. Cascade delete and update behavior.
6. Index coverage.
7. Composite indexes for language-pair lookups.
8. Duplicate prevention.
9. Appropriate column types and lengths.
10. Character encoding and collation.
11. Unicode storage and retrieval.
12. Transaction boundaries.
13. Data consistency across related records.
14. Migration ordering and reproducibility.
15. Seeders and test-data isolation.
16. Database portability, where required.
17. Query plans and slow-query risks.

Pay special attention to language codes, word normalization, translation relationships, and duplicate detection.

Do not add a unique constraint until existing data has been checked for duplicates and the intended uniqueness rule is clear.

Do not assume that a word alone is globally unique across languages.

Check whether search queries can use indexes effectively without sacrificing necessary linguistic behavior.

Provide proposed migrations separately from the audit phase. Never run destructive schema changes without explicit approval and a verified backup strategy.

**Deliverable:** Database Review and Migration Risk Report.

---

### 8. PHASE 6 — PERFORMANCE AND SCALABILITY

Evaluate performance under realistic usage patterns.

Review:

- Database query count.
- N+1 problems.
- Eager loading.
- Pagination and maximum page size.
- Search query complexity.
- Missing or ineffective indexes.
- Unbounded result sets.
- Repeated expensive calculations.
- Caching opportunities and cache invalidation.
- Session and cache drivers.
- Queue usage, where justified.
- Asset size and frontend loading.
- Image optimization, if applicable.
- HTTP request count.
- Response payload size.
- Memory consumption.
- PHP execution time.
- Database connection usage.
- Rate limiting and resource protection.

Consider expected dictionary growth, number of languages, number of entries, concurrent users, and search frequency.

Do not invent performance measurements.

If load testing is appropriate, define a controlled test plan with synthetic data and an authorized staging environment. Avoid load testing production without explicit authorization.

Provide a baseline and proposed measurable targets for response time, throughput, error rate, and resource consumption where measurement tools are available.

Do not prescribe Redis, Elasticsearch, a CDN, or additional infrastructure automatically. Evaluate whether the hosting environment and workload justify each dependency.

**Deliverable:** Performance Findings and Optimization Roadmap.

---

### 9. PHASE 7 — UI/UX, RESPONSIVENESS, AND ACCESSIBILITY

Review the application from the perspective of end users and administrators.

Evaluate:

- Navigation and information architecture.
- Search discoverability.
- Language-pair selection.
- Clarity of translation results.
- Form usability.
- Validation and error messages.
- Loading, empty, and failure states.
- Success feedback.
- Confirmation for destructive actions.
- Responsive layouts.
- Keyboard navigation.
- Visible focus indicators.
- Form labels and accessible names.
- Semantic HTML.
- Color contrast.
- Screen-reader compatibility.
- Accessible tables and pagination.
- Localization of interface text.
- Consistency of terminology.
- Handling of right-to-left scripts if supported.
- Unicode and font rendering.
- Mobile usability.
- Administrative workflows.

Check against relevant WCAG 2.2 AA criteria where feasible.

Do not assume that all languages require right-to-left support. Verify the languages the application intends to support.

Separate usability defects from aesthetic preferences.

For each UI issue, identify the affected page or component, user impact, recommended change, and verification method.

**Deliverable:** UI/UX and Accessibility Audit.

---

### 10. PHASE 8 — TESTING STRATEGY AND REGRESSION VALIDATION

Inspect the current testing setup before introducing new tools.

Use the testing framework already present where practical. For Laravel projects, consider PHPUnit or Pest according to the repository's actual configuration.

Review or add appropriate tests for:

- Authentication.
- Authorization.
- Role and permission enforcement.
- Dictionary CRUD.
- Language management.
- Validation.
- Search behavior.
- Translation direction.
- Unicode and multilingual content.
- Duplicate handling.
- Pagination and filtering.
- API contracts.
- Import and export workflows.
- Database constraints.
- Exception handling.
- Security-sensitive routes.
- Production configuration assumptions.

Use unit tests for isolated business rules, feature tests for Laravel request flows, and browser tests where the frontend and environment justify them.

Ensure that tests use isolated test data and a dedicated testing environment.

Never point a test suite at the production database.

#### Minimum testing expectations

1. A baseline test run before changes, where feasible.
2. Reproducible tests for confirmed defects.
3. Regression tests for every meaningful fix.
4. A final test run after changes.
5. A report of failed, passed, skipped, and unexecuted tests.
6. Clear instructions for reproducing failures.

Use static analysis and code formatting tools when already available or when their addition is justified and approved.

Consider PHPStan or Larastan, Laravel Pint, and dependency vulnerability auditing where compatible with the project's PHP and Laravel versions.

Do not report complete test coverage unless actual coverage data has been generated.

**Deliverable:** Test Strategy, Test Results, and Regression Test Matrix.

---

### 11. PHASE 9 — DEPENDENCY AND SUPPLY-CHAIN REVIEW

Inspect PHP and JavaScript dependencies, lockfiles, package sources, and build scripts.

Evaluate:

- Known vulnerabilities in installed dependencies.
- Unsupported or end-of-life packages.
- Laravel and PHP support status.
- Outdated dependencies with material security impact.
- Abandoned or suspicious packages.
- Excessive dependency footprint.
- Unsafe installation or build scripts.
- Development dependencies accidentally included in production.
- Reproducible builds.
- Lockfile consistency.
- Exposed credentials in package configuration.

Use available tools such as Composer's audit functionality and the relevant JavaScript package manager's audit capabilities when compatible.

Distinguish vulnerabilities in installed versions from findings that affect only development tools or unreachable functionality.

Do not blindly run major-version upgrades.

For proposed dependency changes, document compatibility, breaking-change risk, migration requirements, and tests needed.

**Deliverable:** Dependency Risk Register and Upgrade Plan.

---

### 12. PHASE 10 — ENVIRONMENT, CONFIGURATION, AND OBSERVABILITY

Inspect development, testing, staging, and production configuration conventions.

Verify:

- Correct environment separation.
- Production debug settings.
- Secure configuration defaults.
- Database connection settings.
- Session and cookie settings.
- Cache and queue configuration.
- Filesystem storage settings.
- Mail transport configuration.
- Timezone and locale.
- Logging configuration.
- Error reporting.
- Health-check behavior.
- Application availability monitoring.
- Backup and recovery procedures.

Ensure secrets are provided through secure environment configuration rather than committed to source control.

Review whether logs could expose passwords, tokens, session identifiers, personal information, or sensitive request data.

Recommend structured logging and actionable monitoring without logging confidential data.

Document which settings are mandatory, optional, hosting-dependent, and feature-dependent.

Do not assume that a local `.env` configuration can be copied directly to production.

**Deliverable:** Environment and Operational Readiness Report.

---

### 13. PHASE 11 — PRODUCTION HOSTING AND DEPLOYMENT READINESS

Determine the intended hosting environment from project documentation and user-provided configuration.

If the destination is shared hosting with cPanel, assess the constraints of that environment rather than assuming VPS or root access.

If the destination is unknown, produce a hosting-neutral deployment plan and clearly identify the decisions that depend on the target host.

#### A. Document root and filesystem

Verify that:

- The web server exposes only Laravel's intended public directory.
- Application source, `.env`, `vendor`, private storage, logs, and backups are not publicly accessible.
- The hosting configuration supports Laravel's routing and rewrite requirements.
- Storage links and uploads are configured correctly.
- Filesystem permissions follow least privilege.
- Writable directories are limited to those actually required.
- Sensitive files are not stored in public directories.

Never recommend making the entire project directory publicly accessible.

If the hosting provider cannot point the domain document root to Laravel's `public` directory, explain the risks and evaluate safe alternatives appropriate to that provider. Do not casually move private application files into a public directory.

#### B. PHP and dependency compatibility

Check:

- PHP version.
- Required PHP extensions.
- Composer availability and compatible dependency installation.
- Database version.
- Memory and execution limits.
- Upload limits.
- Request timeout limits.
- Scheduled task support.
- Queue support if required.
- Availability of required external services.

Identify every feature that depends on a capability unavailable in the target hosting plan.

#### C. Production configuration

Prepare a configuration checklist covering:

- `APP_ENV=production`
- `APP_DEBUG=false`
- Secure `APP_KEY` management.
- Database credentials.
- HTTPS and trusted proxy configuration where appropriate.
- Secure session and cookie settings.
- Cache and filesystem drivers.
- Mail configuration.
- Production logging.
- Correct application URL.
- Asset URLs and build artifacts.

Do not generate fake credentials or put actual secrets in the report.

Do not rotate an existing production `APP_KEY` without a reviewed migration plan.

#### D. Deployment procedure

Prepare a safe, ordered deployment runbook:

1. Back up the current application and database, if applicable.
2. Verify the backup can be restored.
3. Prepare a clean release artifact.
4. Install dependencies compatible with the hosting environment.
5. Build frontend assets when necessary.
6. Configure environment variables securely.
7. Verify document root and filesystem permissions.
8. Run reviewed database migrations during an approved maintenance window.
9. Apply Laravel optimization commands appropriate to the installed version and hosting restrictions.
10. Verify storage links, routes, assets, database connectivity, and critical application workflows.
11. Run smoke tests.
12. Monitor application logs and error rates.
13. Document rollback conditions and recovery steps.

Do not blindly execute commands that may be unsupported by shared hosting.

Review the compatibility and consequences of route caching, configuration caching, view caching, and optimization commands before using them.

Do not run `migrate:fresh`, `db:wipe`, or equivalent destructive operations against production.

#### E. Rollback and recovery

Define:

- Release version identification.
- Database backup procedure.
- File and upload backup procedure.
- Rollback criteria.
- Code rollback procedure.
- Database rollback limitations.
- Recovery verification.
- Responsible operator and required permissions.

Do not assume that reversing a migration restores deleted data.

**Deliverable:** Hosting Compatibility Matrix, Deployment Runbook, and Rollback Plan.

---

### 14. PHASE 12 — FIX IMPLEMENTATION PROTOCOL

After completing the initial read-only audit, produce the findings and proposed remediation plan before making substantial changes.

Organize the work into the following stages:

**Stage A — Critical security and correctness defects**

Fix confirmed critical issues and high-risk issues that block release, subject to explicit approval and an appropriate test environment.

**Stage B — Functional correctness**

Repair broken features, validation defects, relationship errors, and incorrect business logic.

**Stage C — Data integrity and reliability**

Address database constraints, transaction handling, exception management, and unsafe edge cases.

**Stage D — Performance and maintainability**

Optimize high-impact queries and perform targeted refactoring.

**Stage E — UI/UX and accessibility**

Address usability problems, responsive defects, and accessibility gaps.

**Stage F — Production hardening**

Apply reviewed environment, server, deployment, and operational improvements.

For every implementation:

1. Explain the root cause.
2. List affected files.
3. State the intended behavior after the change.
4. Make the smallest reliable modification.
5. Preserve existing functionality.
6. Add or update tests.
7. Run relevant checks.
8. Inspect the diff.
9. Identify migration or deployment implications.
10. Document remaining risks.

Do not perform broad automated rewrites without reviewing the resulting changes.

If a fix requires a business decision, missing credentials, a production change, a destructive operation, or an architecture change, stop and request authorization.

Keep separate commits or logically grouped changes where Git is available and commits are authorized.

Never claim that a fix works until the relevant verification has actually been performed.

---

### 15. PHASE 13 — FINAL PRODUCTION READINESS GATE

Evaluate readiness across the following dimensions:

- Functional correctness.
- Security.
- Data integrity.
- Architecture and maintainability.
- Automated testing.
- Performance.
- Accessibility and usability.
- Dependency health.
- Environment configuration.
- Hosting compatibility.
- Backup and recovery.
- Monitoring and operational support.

Use the following release classifications:

**READY**

No known release-blocking defects remain, required verification has passed, and the remaining risks are documented and acceptable.

**READY WITH ACCEPTED RISKS**

No unresolved critical or high-risk blocker remains, but documented lower-priority issues or explicitly accepted residual risks remain.

**NOT READY**

One or more material security, correctness, data integrity, operational, or deployment blockers remain.

**INSUFFICIENT EVIDENCE**

The audit cannot establish readiness because critical tests, source files, configuration details, or hosting information are unavailable.

Do not declare the application production-ready solely because it starts successfully or its homepage renders.

The release gate must consider both technical findings and verification completeness.

---

### 16. REQUIRED FINAL REPORT STRUCTURE

Produce a professional audit report with the following sections.

#### Executive summary

- Overall application condition.
- Production readiness classification.
- Most significant findings.
- Highest-risk security issues.
- Main functional gaps.
- Most important recommended actions.

#### Section A — Project inventory

- Stack and versions.
- Architecture summary.
- Module inventory.
- Hosting assumptions.
- Test infrastructure.

#### Section B — Findings register

Use this table structure:

| ID | Category | Severity | File/Location | Finding | Impact | Recommended action | Status |
|---|---|---|---|---|---|---|---|

Use additional detail below the table for significant findings.

#### Section C — Security findings

Provide evidence, impact, root cause, recommended correction, and safe verification for every confirmed vulnerability.

#### Section D — Functional defects

List broken or incomplete workflows, reproduction steps, expected versus actual behavior, and regression tests.

#### Section E — Database and architecture

Document schema risks, integrity issues, query inefficiencies, code quality issues, and maintainability concerns.

#### Section F — Performance, UI/UX, and accessibility

Separate measured issues from unverified concerns and design recommendations.

#### Section G — Dependency and configuration review

Identify material compatibility risks, exposed-secret risks, unsupported packages, and insecure defaults without disclosing actual secrets.

#### Section H — Deployment readiness

Include the hosting compatibility matrix, deployment prerequisites, secure configuration checklist, smoke tests, backup requirements, and rollback plan.

#### Section I — Prioritized remediation backlog

Group actions into:

- P0 — Critical release blockers.
- P1 — High-priority release blockers or essential corrections.
- P2 — Important improvements.
- P3 — Optional enhancements and future improvements.

Severity and priority are related but not identical. Explain when an item requires immediate attention despite limited exposure.

For each task, specify:

- Finding ID.
- Proposed action.
- Dependencies.
- Estimated effort or effort range.
- Acceptance criteria.
- Verification procedure.
- Whether it blocks production deployment.

Do not invent precise time estimates when the required scope is unknown.

#### Section J — Test and verification results

Report:

- Commands executed.
- Tests passed.
- Tests failed.
- Tests skipped.
- Tests not run.
- Static-analysis results.
- Dependency audit results.
- Security checks performed.
- Environment limitations.

Redact sensitive values and avoid copying confidential log contents.

#### Section K — Final go/no-go recommendation

Provide an explicit release decision, the unresolved blockers, the conditions for approval, and the evidence required to reconsider any failed gate.

---

### 17. COMMUNICATION AND REPORTING REQUIREMENTS

Communicate in clear, professional Bahasa Indonesia.

Keep technical identifiers, commands, file paths, and code examples in their original form.

Be precise, critical, and constructive. Avoid vague advice such as “improve security,” “optimize the database,” or “write more tests” without identifying the specific implementation or measurable action.

Differentiate clearly between:

- Confirmed defects.
- Confirmed vulnerabilities.
- Potential vulnerabilities.
- Architectural recommendations.
- Optional enhancements.
- Items that could not be verified.

Do not overstate the security of the application or promise that it cannot be hacked.

Do not suppress findings because they are difficult to fix or because the application is already 75–80% complete.

If the project is too large to inspect in one pass, work module by module, maintain a findings register, and state which areas remain unaudited.

Prioritize actionable findings over a long list of theoretical concerns.

---

### 18. EXECUTION INSTRUCTIONS — START HERE

Begin immediately with the following sequence:

1. Inspect the repository structure and determine the actual technology stack.
2. Establish the application's current state without modifying files.
3. Identify the implemented modules and their apparent completion status.
4. Inspect the routing, authentication, authorization, data model, and critical application workflows.
5. Run only safe, non-destructive baseline checks that the environment supports.
6. Begin the security, functional, database, and architecture reviews.
7. Produce the initial findings register with evidence and severity.
8. Identify the production blockers and missing information.
9. Propose a prioritized remediation plan.
10. Wait for approval before undertaking substantial code modifications or production-impacting actions.

Do not stop after reviewing only `composer.json`, the homepage, or a handful of controllers.

**Success criterion:** Deliver an evidence-based, reproducible audit that tells the project owner exactly what is wrong, why it matters, where it is located, how to fix it, how to verify the fix, and what must be completed before the Laravel multilingual dictionary application can be safely considered for production deployment.

Start with Phase 1 now. Report the discovered project structure and baseline before proceeding to remediation.

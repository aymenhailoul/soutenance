# ISS Maroc — IT Equipment & Infrastructure Management System
## Agent Implementation Specification

## 1. Mission

You are an autonomous development agent working on the existing Laravel repository:

https://github.com/aymenhailoul/stock-management

Transform the existing garage/car-wash stock-management application into a professional **IT Equipment & Infrastructure Inventory Management System for ISS Maroc**.

IMPORTANT:
- Work on the existing project. Do NOT rebuild it from scratch.
- Reuse the existing Laravel architecture, authentication, dashboard, CRUD patterns, database patterns, exports, layouts, and components whenever practical.
- Inspect the repository before changing anything.
- Preserve working functionality that remains relevant.
- Remove/replace garage-specific functionality cleanly.
- Do not add features merely because they are technically interesting.
- Keep the final scope realistic for an internship project.
- Do not introduce microservices, Kubernetes, AI, GraphQL, WebSockets, or other unnecessary architecture.

The final application should be a coherent professional product, not a collection of unrelated demos.

This specification defines WHAT to build. Section 42 ("Execution Protocol") defines HOW you must build it — phase by phase, with mandatory checkpoints and a progress file. Section 42 is not optional guidance; it governs how every other section in this document gets implemented.

---

# 2. Target Product

Name:

**IT Equipment & Infrastructure Management System**

Purpose:

Allow ISS to manage its IT equipment and, where relevant, customer infrastructure:

- equipment inventory
- equipment categories
- clients
- client sites
- equipment assignment
- equipment transfers
- stock movements
- maintenance
- basic reporting/export
- automated application/database backups
- cloud-storage integration for backups

The application should support both:

1. Individually tracked equipment
   - servers
   - laptops
   - PCs
   - switches
   - routers
   - firewalls
   - printers
   - access points
   - UPS
   - storage devices
   - monitors

2. Quantity-based consumables
   - Ethernet cables
   - HDMI cables
   - keyboards
   - mice
   - adapters
   - etc.

Do not create separate models for every equipment type.

---

# 3. Existing Project: What to Reuse

The repository is a Laravel application and already contains concepts including:

- Products
- Stock movements
- Clients
- Employees
- Vehicles
- Invoices/facturation
- Sales
- Dashboard
- Authentication
- Exports

The existing Product model already contains fields such as name, type, purchase price, sale price, serial code, and stock movement relationships.

The existing StockMovement model tracks:
- product
- movement type
- quantity
- purchase price
- amount
- user
- comment

Reuse these concepts where appropriate.

Recommended transformation:

| Existing concept | Target concept |
|---|---|
| Product | Equipment |
| Product type | Equipment Category |
| Stock Movement | Stock Movement |
| Client | Client |
| Employee | Employee / Technician |
| Vehicle | REMOVE |
| Garage services | REMOVE |
| Sales | REMOVE |
| Garage-specific invoices | REMOVE unless needed for reports |
| Existing dashboard | Adapt |
| Existing exports | Adapt |
| Existing authentication | Keep and improve only where needed |

If renaming `Product` to `Equipment` would cause excessive unnecessary breakage, assess the repository first and choose the cleanest maintainable migration strategy. The final domain terminology visible to users should be Equipment, not Product.

---

# 4. Features to REMOVE

Remove the garage/car-wash domain from the application.

Remove or replace:

- Vehicle management
- Vehicle registration fields
- Plaque/registration plate
- Brand/model fields specifically tied to vehicles
- Mileage
- Fuel type
- Vehicle colors
- Car service history
- Car washing operations
- Garage-specific services
- Garage sales workflow
- Vehicle invoices
- Vehicle-specific reports

Do not leave obsolete garage terminology visible in the final UI.

---

# 5. Core Data Model

Implement only the following main entities unless the existing architecture requires an additional technical model.

## 5.1 Equipment

Suggested fields:

- id
- name
- category_id
- brand
- model
- serial_number
- asset_tag
- purchase_date
- purchase_price
- warranty_end_date
- status
- condition
- site_id
- notes
- timestamps

Statuses:

- Available
- Assigned
- In Maintenance
- Broken
- Retired
- Lost

Conditions:

- New
- Good
- Fair
- Damaged

Do not make every field mandatory if it does not make sense for every equipment type.

Example:

Dell PowerEdge R740
- Category: Server
- Brand: Dell
- Model: PowerEdge R740
- Serial Number: XXXXX
- Asset Tag: ISS-SRV-001
- Status: Assigned
- Site: Client ABC / Casablanca

---

# 6. Equipment Categories

Create a simple category system.

Initial categories:

- Servers
- Laptops
- Desktop PCs
- Switches
- Routers
- Firewalls
- Printers
- Access Points
- UPS
- Storage
- Monitors
- Other

Do not create separate database models for Server, Router, Switch, Laptop, etc.

---

# 7. Clients

Keep the existing Client concept.

Clients represent companies/customers whose infrastructure ISS may manage.

Keep the model simple.

Useful information:

- company/name
- contact person
- phone
- email
- address
- notes

Adapt existing fields rather than creating duplicate client systems.

---

# 8. Sites

Add a Site entity.

Relationship:

Client 1 -> many Sites
Site 1 -> many Equipment

Suggested fields:

- id
- client_id
- name
- address
- city
- contact_name
- contact_phone
- notes
- timestamps

Example:

Client:
ABC Company

Sites:
- Casablanca HQ
- Rabat Office
- Tangier Office

Equipment can be associated with a specific site.

---

# 9. Equipment Assignment

Add an `EquipmentAssignment` concept.

Do NOT rely only on `equipment.employee_id`, because assignments must have history.

Suggested fields:

- id
- equipment_id
- employee_id nullable
- site_id nullable
- assigned_by
- assigned_at
- returned_at nullable
- status
- notes
- timestamps

Use it to track:

- employee assignment
- site assignment
- returns
- transfers

Example history:

Dell Latitude 5520
- 2026-01-10: assigned to Ahmed
- 2026-04-12: returned
- 2026-04-15: assigned to Youssef
- 2026-07-01: transferred to Casablanca site

The UI should make the current assignment and assignment history clear.

---

# 10. Stock Movements

Keep the existing stock movement concept.

For quantity-based inventory:

- Entrée
- Sortie

For equipment management, add a simple `Transfert` concept if appropriate.

A movement should preserve history and identify the user who performed it.

Do not overcomplicate stock accounting.

For serialized equipment, status/assignment/asset tracking is more important than simply calculating quantity.

For consumables, quantity-based stock remains useful.

---

# 11. Maintenance

Add a simple Maintenance entity.

Suggested fields:

- id
- equipment_id
- technician_id
- type
- description
- start_date
- end_date
- cost
- status
- notes
- timestamps

Types:

- Preventive
- Corrective
- Inspection

Statuses:

- Scheduled
- In Progress
- Completed
- Cancelled

The equipment detail page should show maintenance history.

When appropriate:
- starting maintenance should update equipment status to In Maintenance
- completing maintenance should allow the equipment to return to Available/Assigned
- do not create an overly complex workflow engine

---

# 12. Dashboard

Adapt the existing dashboard.

Show useful operational information, not excessive charts.

Recommended cards:

- Total Equipment
- Available
- Assigned
- In Maintenance
- Broken
- Retired
- Clients
- Sites

Additional sections:

### Low Stock
List consumables below their configured threshold.

### Warranty
Equipment whose warranty is expiring soon.

### Maintenance
Upcoming or overdue maintenance.

### Backup
- Last backup
- Backup status
- Backup size
- Last successful backup

Avoid 20+ charts.

---

# 13. Search, Filters and Tables

For Equipment, provide:

- search by name
- serial number
- asset tag
- brand
- model

Filters:

- category
- status
- condition
- client
- site

Tables should support:

- pagination
- sorting where useful
- clear status badges
- empty states
- validation/error messages

This is more important than adding fancy visualizations.

---

# 14. Equipment Detail Page

Create a professional equipment detail page.

Suggested sections:

### General Information
- Name
- Category
- Brand
- Model
- Serial Number
- Asset Tag

### Procurement
- Purchase date
- Purchase price
- Warranty

### Current State
- Status
- Condition
- Current site
- Current employee

### Assignment History

### Maintenance History

### Stock/Movement History

### Notes

Do not overload the page with irrelevant information.

---

# 15. Backup and Cloud Storage

This is a required part of the project.

The application should support automated backups of its own database/application data.

Target architecture:

Laravel
-> Backup Service
-> Cloud Object Storage

Preferred cloud target:

**Azure Blob Storage**

Development environment:

**Azurite**

Azurite is used locally so development does not require a paid Azure account or credit card.

The application should be designed so the storage backend can switch between:

- local development/Azurite
- real Azure Blob Storage

using environment configuration.

Do not hard-code credentials.

---

# 16. Backup Features

Implement:

- manual backup trigger
- scheduled automatic backup
- backup history
- backup status
- backup filename
- size
- creation date/time
- success/failure
- storage location/provider
- configurable retention where practical

Example UI:

Backups

Last Backup:
30 Aug 2026 — 02:00

Status:
Successful

Storage:
2.4 GB

Actions:
- Create Backup
- Download if appropriate
- Restore if safely implementable

Do not build a full enterprise backup product.

---

# 17. Automated Backup

Use Laravel's existing scheduling/background mechanisms where appropriate.

The goal is:

1. Create database backup
2. Compress if appropriate
3. Upload to configured storage
4. Record backup metadata
5. Report success/failure
6. Apply retention policy

Do not introduce unnecessary infrastructure just to demonstrate technology.

If queues are useful, they may be used, but they are not mandatory if they add complexity without benefit.

---

# 18. Azure/Azurite Configuration

Use environment variables.

Example conceptual configuration:

AZURE_STORAGE_ACCOUNT
AZURE_STORAGE_KEY
AZURE_STORAGE_CONTAINER
AZURE_STORAGE_ENDPOINT

Use appropriate Laravel filesystem configuration.

For local development, configure Azurite.

For production, allow Azure Blob Storage configuration.

Never commit secrets.

Update `.env.example` with placeholders and documentation.

---

# 19. Restore

Restore is optional if implementation would create significant risk.

If implemented:

- make it explicit
- require authorization
- show a warning before restore
- prevent accidental destructive actions
- log the operation

Do not implement an unsafe one-click database overwrite.

A reliable backup system is more important than pretending to have a restore feature.

---

# 20. Reports and Exports

Reuse the existing export functionality.

Useful exports:

- Equipment inventory
- Stock movements
- Equipment assignments
- Maintenance history
- Clients/sites

Prefer Excel/CSV/PDF only where the existing project already supports it or implementation is straightforward.

Do not create dozens of report formats.

---

# 21. User Roles

Keep authentication.

Use a small role model:

- Admin
- Manager
- Technician
- Viewer

Basic intent:

### Admin
Full access.

### Manager
Manage equipment, clients, sites, assignments, maintenance, reports, backups.

### Technician
View equipment, manage assignments/maintenance where appropriate.

### Viewer
Read-only access.

Do not build a huge enterprise permission matrix.

---

# 22. Navigation

Target navigation:

Dashboard

Inventory
- Equipment
- Categories
- Stock Movements
- Low Stock

Assignments
- Current Assignments
- Assignment History

Clients
- Clients
- Sites

Maintenance
- Maintenance
- Upcoming

Backups
- Backup History
- Backup Settings

Reports

Administration
- Users
- Profile

Only expose modules that are actually implemented.

---

# 23. UI/UX Requirements

The application should look like a professional internal business application.

Requirements:

- consistent layout
- clean sidebar
- responsive pages
- clear typography
- consistent buttons
- professional forms
- confirmation dialogs for destructive actions
- validation messages
- empty states
- loading states where applicable
- pagination
- searchable tables
- useful status badges

Do NOT replace the application with a flashy landing page.

Do NOT use AI-generated images.

Do NOT add decorative images that have no functional purpose.

The application is an internal IT management system, so prioritize clarity and usability.

---

# 24. Technical Requirements

Before coding:

1. Inspect repository structure.
2. Inspect routes.
3. Inspect migrations.
4. Inspect models and relationships.
5. Inspect controllers.
6. Inspect Blade/components/frontend structure.
7. Inspect authentication.
8. Inspect existing exports.
9. Identify garage-specific code.
10. Plan migrations/refactoring before making destructive changes.

Use the existing stack unless there is a strong reason to change it.

Do not migrate Laravel to another framework.

Do not rewrite the entire frontend/backend unnecessarily.

Follow Laravel conventions.

Use:
- migrations
- Eloquent relationships
- form/request validation
- authorization
- transactions where needed
- route model binding where appropriate
- service classes when business logic genuinely benefits from them

Avoid:
- giant controllers
- duplicated business logic
- hard-coded credentials
- raw SQL when Eloquent/query builder is sufficient
- unnecessary packages

---

# 25. Data Integrity

Pay attention to:

- foreign keys
- unique serial numbers where appropriate
- unique asset tags
- nullable relationships
- deletion restrictions
- assignment history
- stock consistency
- maintenance status consistency

Do not allow an equipment item to have contradictory states.

Example:

If equipment is currently assigned, it should not simultaneously appear as Available.

Use database constraints and application validation where practical.

---

# 26. Security

Implement sensible application security:

- authenticated routes
- authorization by role
- CSRF protection
- request validation
- secure password handling
- no secrets in Git
- safe file handling
- safe backup handling
- audit important destructive actions

Do not turn the project into a SIEM or cybersecurity platform.

---

# 27. Testing

At minimum, test important business behavior:

- equipment creation
- equipment update
- unique serial/asset tag validation
- category relationship
- client/site relationship
- assignment
- return
- maintenance status
- stock movement
- backup record creation
- backup success/failure handling
- authorization

Fix existing tests if they reference removed garage functionality.

Do not chase an arbitrary percentage of test coverage. Prioritize important business logic.

---

# 28. Migration Strategy

Do not destroy the existing database blindly.

Create proper Laravel migrations.

When removing old entities:

1. Identify dependencies.
2. Migrate/reuse useful data if appropriate.
3. Remove obsolete relationships.
4. Remove obsolete UI/routes/controllers.
5. Verify migrations from a clean database.
6. Verify existing development data is not unexpectedly destroyed.

The project must be installable from a clean database after the refactor.

---

# 29. Suggested Implementation Order

Follow this order unless repository inspection indicates a better sequence.

### Phase 1 — Audit
- inspect complete repository
- understand existing architecture
- map current models/routes/views/controllers
- identify reusable code

### Phase 2 — Domain Refactor
- Product -> Equipment
- remove vehicle/garage domain
- create categories
- update terminology

### Phase 3 — Infrastructure Inventory
- Equipment CRUD
- categories
- clients
- sites
- search/filter/pagination
- equipment detail page

### Phase 4 — Tracking
- assignments
- assignment history
- stock movements
- transfers
- status management

### Phase 5 — Maintenance
- maintenance CRUD
- equipment maintenance history
- status integration

### Phase 6 — Dashboard/Reports
- dashboard metrics
- low stock
- warranty
- maintenance
- exports

### Phase 7 — Backup
- backup service
- local/Azurite storage
- backup history
- scheduled backup
- Azure Blob Storage configuration
- retention

### Phase 8 — Quality
- authorization
- validation
- tests
- error handling
- UI consistency
- clean obsolete code
- documentation

---

# 30. Definition of Done

The project is complete when:

- no garage/car-wash terminology remains in the user-facing application
- equipment can be created, edited, viewed and searched
- categories work
- clients and sites work
- equipment can be assigned and returned
- assignment history is preserved
- stock movements work
- maintenance history works
- dashboard displays useful operational metrics
- reports/exports work
- backups can be created
- backup history is stored
- local cloud-storage development works with Azurite
- Azure Blob Storage can be configured through environment variables
- automatic backup scheduling works
- authentication and authorization work
- important workflows are tested
- migrations work from a clean database
- no secrets are committed
- obsolete garage code is removed
- the application remains maintainable

---

# 31. Strict Scope Control

Do NOT add these unless explicitly requested later:

- AI/chatbot
- machine learning
- microservices
- Kubernetes
- GraphQL
- WebSockets
- mobile application
- IoT
- facial recognition
- advanced SIEM
- complex accounting
- payroll
- CRM
- full ERP
- e-commerce
- customer billing system
- advanced network monitoring
- complicated workflow engines

Possible future extensions may be documented, but do not implement them as part of this scope.

---

# 32. Agent Working Rules

You are an implementation agent, not just a code generator.

For every major change:

1. Inspect the existing implementation first.
2. Reuse existing code where sensible.
3. Make the smallest clean change that achieves the requirement.
4. Keep the application working after each logical phase.
5. Run migrations/tests/linting where available.
6. Fix regressions you introduce.
7. Do not silently invent requirements.
8. If an existing feature conflicts with the new domain, refactor it rather than creating duplicate functionality.
9. Keep naming consistent with the IT equipment domain.
10. Do not stop after creating database models; complete the UI, routes, validation, authorization and workflows required for each feature.

Before declaring completion, perform a final repository-wide search for obsolete garage terms and verify that the application can run from a clean installation.

---

# 33. Final Product Concept

The final application should feel like:

**ISS IT Asset Management**

not:

**a garage application with some renamed fields.**

The core workflow should be:

Client
-> Site
-> Equipment
-> Assignment
-> Maintenance
-> Movement/History

and separately:

Application
-> Database Backup
-> Azure Blob/Azurite
-> Backup History

Keep the product focused, professional, maintainable, and appropriate for an IT infrastructure company internship.


# 34. Mandatory Codebase Refactoring Checklist

When changing or removing any existing domain concept, do NOT modify only the database model.

Trace the change through the entire application.

For every affected feature/entity, inspect and update as necessary:

- Model
- Model relationships
- Migration
- Factory
- Seeder
- Form Request / validation
- Policy
- Gates / authorization
- Middleware
- Controller
- Service class, if one exists
- Routes / route names
- Blade views / frontend components
- Forms
- Tables
- Navigation/sidebar
- Dashboard
- Exports
- Notifications
- Tests
- Translations/labels
- Search/filter logic
- API endpoints, if the repository contains them
- Documentation/configuration

After refactoring, search the entire repository for old class names, table names, route names, variable names, and user-facing terminology.

Example:

If `Product` becomes `Equipment`, do not stop after changing `Product.php`.

Check for and update references such as:

- Product model imports
- product relationships
- product controllers
- product routes
- product route names
- product validation
- product policies
- product factories
- product seeders
- product views
- product forms
- product exports
- dashboard queries
- navigation
- tests
- translations
- database/table references

Do the same for `Vehicle`, garage services, sales and other removed concepts.

---

# 35. Authentication & Authorization Audit

Authentication and authorization are part of the refactoring scope.

First inspect the existing authentication implementation and determine what is already present.

Do NOT replace the authentication system unnecessarily.

Preserve a working authentication system where possible.

However, update it if required so that the final ISS application has appropriate access control.

## Required roles

Use a simple role structure:

- Admin
- Manager
- Technician
- Viewer

Do not create dozens of roles.

## Expected permissions

### Admin

Full application access, including:

- users
- equipment
- categories
- clients
- sites
- assignments
- maintenance
- stock movements
- reports
- backups
- configuration

### Manager

Can manage operational data:

- equipment
- categories
- clients
- sites
- assignments
- maintenance
- stock movements
- reports
- backups

User administration should normally remain Admin-only unless the existing design has a good reason otherwise.

### Technician

Can:

- view equipment
- view clients/sites
- manage permitted equipment assignments
- create/update maintenance records
- view relevant stock movements
- view reports where appropriate

Technicians should not automatically have access to:

- user administration
- role management
- destructive system configuration
- sensitive backup configuration

### Viewer

Read-only access to permitted inventory information.

Viewer must not be able to:

- create equipment
- edit equipment
- delete equipment
- create movements
- modify assignments
- modify maintenance
- manage users
- trigger destructive backup operations

---

# 36. Authorization Implementation

Use Laravel's native authorization mechanisms where appropriate:

- Policies
- Gates
- Middleware
- `authorize()` checks
- route middleware

Do not rely only on hiding buttons in the UI.

A user who manually accesses a URL must still be denied if they lack permission.

Example:

Hiding:

`[Delete Equipment]`

from a Technician is NOT sufficient.

The backend must also reject:

`DELETE /equipment/{id}`

for that user.

Use appropriate HTTP responses/authorization handling.

Do not duplicate authorization logic across dozens of controllers if a Policy is more appropriate.

---

# 37. Authorization Matrix

Before implementation, establish a simple matrix similar to:

| Feature | Admin | Manager | Technician | Viewer |
|---|---:|---:|---:|---:|
| View Equipment | Yes | Yes | Yes | Yes |
| Create Equipment | Yes | Yes | Limited/Yes | No |
| Edit Equipment | Yes | Yes | Limited/Yes | No |
| Delete Equipment | Yes | Controlled | No | No |
| Categories | Yes | Yes | View | View |
| Clients | Yes | Yes | View/limited | View |
| Sites | Yes | Yes | View | View |
| Assignments | Yes | Yes | Yes | View |
| Maintenance | Yes | Yes | Yes | View |
| Stock Movements | Yes | Yes | Limited/Yes | View |
| Reports | Yes | Yes | Appropriate reports | View |
| Backups | Yes | Yes | View status | View status |
| Backup Restore | Yes | Controlled | No | No |
| User Management | Yes | No/limited | No | No |

Adjust the exact permissions after inspecting the existing authentication/authorization implementation.

Do not introduce restrictions that make the application unusable for its intended workflow.

---

# 38. Authentication Changes

Inspect whether the current project uses:

- Laravel Breeze
- Laravel Jetstream
- Fortify
- custom authentication
- another existing Laravel authentication mechanism

Keep the existing mechanism if it is sound.

Only modify authentication when necessary for the new project.

Verify:

- login
- logout
- password handling
- protected routes
- session behavior
- unauthorized access handling
- user role handling

If registration currently exists but an internal ISS system should not allow arbitrary public registration, evaluate whether registration should be disabled or restricted.

Do not expose internal administrative functionality publicly.

---

# 39. User Management

If the existing application already has user management, adapt it.

The Admin should be able to:

- view users
- create users where appropriate
- edit users
- assign roles
- deactivate users if supported cleanly

Do not store passwords manually or insecurely.

Use Laravel's standard password hashing/authentication mechanisms.

If user deletion could break historical records, prefer deactivation/soft deletion where appropriate rather than physically deleting users.

Historical records such as:

- stock movements
- assignments
- maintenance
- backups

should retain the identity of the user who performed the action.

---

# 40. Audit Important Actions

Do not build a giant enterprise audit system.

At minimum, preserve accountability for important actions where practical:

- equipment creation/update/deletion
- equipment assignment/return
- stock movements
- maintenance changes
- backup creation
- backup restore
- user/role changes

Reuse an existing audit/logging mechanism if the repository already has one.

If no audit package exists, implement only the minimum necessary audit trail rather than adding a large dependency without need.

---

# 41. Final Refactoring Verification

Before declaring the project complete, perform a repository-wide verification.

Search for obsolete terms and references such as:

- Vehicle
- vehicle
- plaque
- mileage
- carburant
- garage
- car wash
- washing
- sale-specific terminology
- obsolete Product terminology

Some internal migration/history references may legitimately remain for database migration compatibility, but obsolete concepts must not remain active in the final application unless technically necessary.

Also verify:

### Backend
- Models
- Relationships
- Migrations
- Controllers
- Services
- Requests
- Policies
- Middleware

### Routing
- Web routes
- API routes if present
- Route names
- Middleware

### Authentication
- Login
- Logout
- Password handling
- Protected routes
- Roles
- Policies
- Unauthorized access

### Frontend/UI
- Views
- Components
- Forms
- Navigation
- Dashboard
- Error states

### Data
- Foreign keys
- Constraints
- Seeders
- Factories

### Quality
- Tests
- Exports
- Backup workflows
- Documentation

No feature should be considered migrated until its complete application path has been checked.

---

# 42. Execution Protocol (Mandatory)

Sections 1–41 define what the finished product must contain. This section defines how you are required to build it. This protocol overrides any impulse to implement the project in one large pass. Do not skip phases, merge phases, or reorder them without first completing and verifying the phase before.

## 42.1 Core Rule

Never implement the entire project in one pass. Work phase-by-phase, with a checkpoint at the end of every phase. Do not begin a new phase until the current phase is verified working.

## 42.2 Phase Sequence

**Phase 1 — Repository Audit**
Audit the existing repository. Report your understanding of its structure, models, routes, auth system, and conventions.
→ Checkpoint.

**Phase 2 — Domain/Models/Database Refactor**
Refactor the core domain (e.g. Product → Equipment) at the model, migration, and database level.
→ Migrate, test.
→ Checkpoint.

**Phase 3 — Equipment + Categories**
Implement Equipment and Equipment Category end-to-end (model → migration → validation → controller → routes → views → tests).
→ Test.
→ Checkpoint.

**Phase 4 — Clients + Sites + Assignments**
→ Test.
→ Checkpoint.

**Phase 5 — Maintenance + Stock**
→ Test.
→ Checkpoint.

**Phase 6 — Dashboard + Reports**
→ Test.
→ Checkpoint.

**Phase 7 — Backup + Azurite + Azure**
→ Test.
→ Checkpoint.

**Phase 8 — Auth / Authorization / Security**
→ Test.
→ Checkpoint.

**Phase 9 — Full Integration Test, Clean-up, Final Verification**
Run the Section 41 verification pass. Confirm the Definition of Done (Section 30) is met.

## 42.3 Rules for Every Phase

1. **Never implement the entire project in one pass.** One phase at a time, in order.
2. **Before each phase:**
   - Inspect the relevant existing code.
   - Identify dependencies on other parts of the system.
   - State explicitly what will change before changing it.
3. **After each phase:**
   - Run migrations.
   - Run relevant tests.
   - Check for errors.
   - Verify the UI.
   - Fix any problems before continuing to the next phase.
4. **Do not continue past a broken checkpoint.** If a phase breaks something (e.g. Phase 3 breaks authentication), fix it within that phase before starting the next one. A broken checkpoint blocks all further work.
5. **Keep changes incremental within a phase.** Do not "rewrite the entire inventory system" as one step. Instead, move feature-by-feature and layer-by-layer: model → migration → validation → controller → routes → views → tests → verify. Then move to the next feature within the phase.
6. **Preserve a working state.** At the end of every phase, the application must still run.
7. **Don't assume — inspect and reuse first.** If the existing code already solves a problem, use it rather than reinventing it.

## 42.4 Progress File (Mandatory)

Maintain a file named `IMPLEMENTATION_PROGRESS.md` at the repository root for the entire duration of the project. Update it after every phase.

**Checklist section**, updated as items complete:

```
[x] Repository audit
[x] Remove vehicle domain
[x] Equipment model
[x] Equipment CRUD
[x] Categories
[ ] Sites
[ ] Assignments
[ ] Maintenance
[ ] Backups
[ ] Azure integration
[ ] Authorization
[ ] Final testing
```

**Status block**, appended after every phase:

```
Current phase:
Phase 4 — Assignments

Completed:
- ...
- ...

Tests:
- ...

Issues:
- ...

Next:
- ...
```

This file must always reflect the true current state of the project so that work can be inspected or resumed at any point without re-reading the entire codebase.

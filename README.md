## Workflow Automation Engine (conditions-first)

This project includes a SuiteCRM-style workflow engine built around:

- **Workflow definition**: trigger + conditions + actions
- **Execution state**: `limocrm_workflow_execution_log`
- **Runner**: a cron script that evaluates triggers, checks conditions, and executes actions

### 1) Database migration

Run:

- `sql/workflow_engine_rebuild_migration.sql`

against the same DB configured in `config/database.php`.

### 2) API endpoints

The workflow UI uses local endpoints under `/api/*`:

- `GET /api/workflows`
- `POST /api/workflows`
- `GET /api/workflows/:id`
- `PUT /api/workflows/:id`
- `DELETE /api/workflows/:id`
- `GET /api/workflows/module-fields/:module`
- `GET /api/email-templates`
- `POST /api/workflows/:id/execute-now`
- `GET /api/workflows/:id/logs`

### 3) Cron configuration (every 60 seconds)

Run:

```bash
php G:/XAMPP/htdocs/limocrm/cron/workflow_engine.php
```

Recommended schedule:

- **Windows Task Scheduler**: create a task that runs every 1 minute and executes the command above.
- **Linux cron** (if deployed): `* * * * * php /var/www/limocrm/cron/workflow_engine.php`

### 4) UI routes

- List: `workflows/index.php`
- Create: `workflows/create.php`
- Edit: `workflows/edit.php?id=<workflow_id>`


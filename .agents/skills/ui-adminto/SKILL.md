---
name: ui-adminto
description: Bootstrap/Adminto UI, Blade/HTML, and JavaScript standards. Load only when modifying views, Blade/HTML, or JS/CSS assets.
---

# UI Adminto & Frontend Guidelines

## Layout & Bootstrap Structure
- Follow standard Adminto hierarchy: `.container-fluid` -> `.row` -> `.col-*` -> `.card` -> `.card-body`.
- Prefer Bootstrap utility classes (`mb-3`, `p-2`, `d-flex justify-content-between`) over inline styles (`style="..."`) or new CSS files.
- Wrap all data tables in `.table-responsive` to prevent layout overflow on smaller viewports.

## Components & Interactions
- **DataTables:** Use *server-side processing* when datasets may exceed 500 rows. Define columns explicitly and avoid heavy Blade loop rendering when using AJAX.
- **Forms & Validation:** Render validation errors directly beneath inputs using `.is-invalid` and `.invalid-feedback`.
- **Modals:** Always reset form inputs and clear `.is-invalid` classes on the `hidden.bs.modal` event to prevent stale state.

## JavaScript & AJAX
- Include the CSRF token (`X-CSRF-TOKEN` from the meta tag) in all `POST`, `PUT`, `PATCH`, and `DELETE` requests.
- **Prevent Double-Submit:** Disable the submit button and show a loading indicator (spinner/"Saving...") during active requests; re-enable in finally / complete.
- Handle HTTP errors (422, 403, 500) with template notifications (SweetAlert2 / Toastr)—never use native browser `alert()`.
- **Playwright-Friendly:** Assign unique, descriptive `id` or `name` attributes to forms, inputs, and primary action buttons for reliable `qa-frontend` verification.

## Pre-Completion Checklist
- [ ] No leftover `console.log()` or `debugger` statements.
- [ ] No broken asset paths or browser console errors.
- [ ] Submit buttons are protected against double-clicks.

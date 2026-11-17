# Development log

## 2026-11-11 — Repository setup and documentation

- Verified the working directory and Git root are `/Users/mom/Local Sites/hos-bakery/app/public/wp-content/plugins/bakery-ordering`.
- Verified `origin` points to `https://github.com/robersonj/bakery-plugin.git` for fetch and push.
- Found an initial README commit on `main`, with no plugin implementation.
- Added project instructions, scope and open decisions, ignore rules, and a Mac Studio handoff guide.
- Expanded the README to explain the current state and documentation.

Verification: reviewed the documentation and checked the patch for whitespace errors. No application tests apply to this documentation-only setup. WordPress activation and runtime behavior have not been tested because there is no plugin entry point.

Next step: confirm the first version's ordering workflow and whether it will use WooCommerce before building the plugin skeleton.

## 2026-11-12 — Preliminary wholesale dashboard

- Added the plugin entry point, Bakery Ordering admin menu, protected standalone preview, and `[bakery_ordering_dashboard]` shortcode.
- Built the reference layout: dark green navigation, welcome header, next delivery, standing order, upcoming deliveries, quick actions, account control, and help panel.
- Added scoped responsive styles, keyboard focus indicators, decorative SVG icons, locally bundled generated pastry artwork, and accessible placeholder notices.
- Preserved the reference's sample account, dates, and quantities. No business rules, order mutations, messaging, or customer-access permissions were introduced.
- Updated setup documentation. The plugin has not been activated and no WordPress pages were created.

Verification: both PHP files pass Local PHP 8.2.35 syntax checks; `git diff --check` passes. A read-only check using the running Local WordPress database verified administrator shortcode rendering, all four sections, asset enqueueing, and restricted guest output. The Local homepage returned HTTP 200. Browser navigation to the Local hostname timed out, so visual desktop/mobile review and browser interaction verification remain outstanding. No established automated test environment exists.

Next step: activate Bakery Ordering and review the dashboard in a local browser, then refine visual details before building ordering functionality.

## 2026-11-12 — Fixed preview login loop

- Replaced `auth_redirect()` on the front-end preview with `is_user_logged_in()` and a WordPress login URL returning to the preview. The previous check validated an admin-area authentication cookie that is normally absent on front-end requests.
- Kept the administrator capability check and normal WordPress authentication intact.

Verification: PHP syntax and whitespace checks pass. A Local WordPress runtime check with an authenticated administrator and no admin-area authentication cookies rendered the standalone dashboard. A complete interactive browser login was not verified.

Next step: reopen the dashboard preview in the same browser as WordPress and confirm the login completes.

## 2026-11-16 — In-dashboard product catalog

- Moved product and pricing group management into the Bakery Ordering dashboard shell; removed the separate WordPress catalog screens.
- Added product creation/editing/deletion, SKU, description, selling unit, base price, per-group prices, image upload and preview, and save-and-add-another.
- Added custom price group creation, editing, and deletion. Deleting a group removes its product prices.
- Kept catalog access restricted to administrators. Records remain plugin-owned until WooCommerce is active and a mapping plan is implemented.

Verification: PHP syntax checks pass for the plugin and both templates; `git diff --check` passes. Local runtime rendering was not available because the Local site was not reachable during this session. Product image upload and save flows have not been exercised in a browser.

Next step: review the product workspace in Local, then define customer assignment to pricing groups and WooCommerce mapping.


## 2026-11-16 — Product pack sizes

- Added required pack quantity and pack item label fields to the in-dashboard product editor.
- Catalog cards now show the explicit selling unit, such as “Pack of 4 croissants,” next to the product photo. Product prices are for the configured sellable pack.
- Kept the prior unit field as a fallback for existing catalog entries.

Verification: PHP syntax and whitespace checks pass. Customer ordering screens are not yet implemented; this product data can be reused there later.


## 2026-11-16 — Money field focus styling

- Fixed the base and group price controls so focus styling wraps the full money input, including its currency prefix, instead of drawing a mismatched outline around only the number input.
- Added focus-within styling to preserve a clear keyboard focus indicator.

Verification: `git diff --check` passes. Visual browser review was not available in this session.


## 2026-11-16 — Clarified item ordering increments

- Reframed the product quantity field as an order increment: products are individual items and orders should be entered in multiples of the configured quantity.
- Clarified that base and group prices are per individual item.
- Updated catalog cards to state the individual selling unit and order multiple explicitly.
- Existing quantity values are preserved in the same product metadata field.

Verification: pending PHP syntax and whitespace checks. Customer-facing quantity enforcement will be implemented with the ordering flow.


## 2026-11-16 — Product save handler hook

- Fixed the dashboard form submit handler to run on WordPress `init`, where front-end dashboard POST requests are processed. The previous `admin_init` hook only runs in wp-admin and therefore never received catalog form submissions.
- Corrected product card metadata to display individual sale with the configured order increment.

Verification: PHP syntax and whitespace checks pass. Local/browser save verification remains outstanding.


## 2026-11-16 — Product card hover actions

- Replaced the product overflow menu with separate edit and delete icon buttons at the upper right of each card.
- Styled each action with an independent rounded background, visible on hover/focus and persistently visible on touch devices.
- Kept deletion behind the existing confirmation prompt and nonce-protected form.

Verification: pending PHP syntax and whitespace checks.


- Refined product card actions: positioned them over the upper-right of the product image and reduced button/icon size.

## 2026-11-16 — Duplicate product action

- Added a duplicate icon button before edit in the product image action cluster.
- Duplicate opens a prefilled unsaved product form, prefixes the name with “Copy of,” copies product details and pricing, and reuses the existing image until replaced. Saving creates a separate catalog product.

Verification: PHP syntax and whitespace checks pass. Local visual and save-flow review not performed.

## 2026-11-16 — Live product editor preview

- Added a product card preview beneath the in-dashboard editor, initialized from the product being edited or duplicated.
- Updated the preview live for product name, item unit, order increment, description, base price, pricing group prices, and newly selected photo.
- Kept duplicate as an unsaved new product with a “Copy of …” name; the live preview shows the copy rather than replacing the source catalog card.

Verification: PHP template syntax and `git diff --check` pass. Local browser interaction and visual review were not available.

- Ensured the live preview is also revealed when starting a blank product from either Add Product button.


## 2026-11-16 — Live preview refresh and duplicate card cleanup

- Removed the source product card from the catalog while editing or duplicating it, leaving the live preview as the single card in that position.
- Bumped catalog asset versions to force browsers to load the current live-preview JavaScript and styles.

Verification: PHP syntax and whitespace checks pass. Browser interaction could not be confirmed locally in this session.

## 2026-11-16 — Side-by-side product editor

- Moved the live preview into the product form and positioned it in the left column beside the fields and pricing controls.
- Kept the preview sticky near the top on larger screens; on phones it stacks above the fields.

Verification: template PHP syntax and `git diff --check` pass. Browser layout review was unavailable.


- Kept the photo picker directly below the live preview at the same width as the card.
- Restored the other product fields to the form flow so they sit beside the preview where space allows and continue below it; the photo picker is the only control in the preview column.
- Preserved the hidden state for the preview when starting a new product.
- Placed the preview and product fields inside the same layout container so the fields can actually flow alongside the floated preview.

Verification: `git diff --check` passes. PHP lint and Local visual review were unavailable in this session.

## 2026-11-16 — Explicit product editor columns

- Replaced float based wrapping with a fixed desktop grid: product fields occupy the left column, while the live preview and photo picker occupy the right column.
- Stacked the columns on narrow screens and kept the photo picker directly below the preview at full preview width.

Verification: `git diff --check` passes. PHP lint was unavailable, and the open Local editor was not reloaded to avoid discarding any in-progress form changes.

## 2026-11-16 — Product and bakery-wide availability settings

- Added multi-select weekday availability to product create/edit forms, the live preview, and catalog cards. Products default to all weekdays for compatibility.
- Added a dashboard Availability page for recurring bakery-wide closed weekdays and specific holiday dates, with optional reasons and removal controls.
- Saved global settings in a prefixed WordPress option and product weekdays in product metadata; actions use the existing administrator and nonce checks with sanitized inputs.
- Recorded that order-date filtering is future work because customer ordering and delivery scheduling are not implemented yet.

Verification: PHP lint for the main plugin and catalog template passes; `git diff --check` passes. Verified the Availability page and the live product weekday preview in Local without submitting changes. End-to-end saving was not exercised.

## 2026-11-16 — Recurring holiday closures

- Added a curated holiday selector to Availability with fixed-date holidays and calculated annual dates for movable holidays, including Thanksgiving, Easter, and weekday-based observances.
- Saved selected holiday rule IDs so managers choose holidays once; the page shows each holiday’s date for the current year. Manual one-off date closures remain available.
- Documented that selected holidays use their actual dates; observed weekday rules remain an open business choice.

Verification: PHP lint for the main plugin and catalog template passes; `git diff --check` passes. Confirmed the holiday choices and current-year dates render in Local. Did not submit the settings form, so persistence of holiday selections was not verified in the browser.

## 2026-11-16 — Product card missing from catalog

- Fixed an unclosed product editor wrapper that caused the browser to treat the product grid as part of the editor’s `hidden` container. Product count and query were correct; the cards were hidden by invalid wrapper structure.
- Enqueued catalog styles and scripts before `wp_head()` for both the standalone dashboard and shortcode route.
- Set the catalog hero image URL directly in the template, avoiding the previous undefined variable warning.

Verification: PHP syntax checks pass for the plugin and catalog template; `git diff --check` passes. Reloaded the Local catalog and confirmed the Blueberry Muffin card, photo, availability, order increment, base price, and edit link are visible.

## 2026-11-16 — Simplified product cards

- Removed availability days, the redundant order-increment line, and the bottom edit link from product cards. The order increment remains visible over the photo; edit stays in the hover actions.
- Displayed the base price without the “Base (each)” label and kept named custom group prices.
- Added a four-column grid on large screens while retaining the narrower tablet and mobile layouts.
- Matched the live preview’s pricing details to the simplified card.

Verification: PHP syntax and `git diff --check` pass. Reloaded the Local catalog and confirmed the availability and base label are gone, the price remains, and the card is narrower within the four-column large-screen grid.

## 2026-11-16 — Product selling units and price labels

- Replaced the free-text item unit field with a “Sold by” selector for each, dozen, pan, loaf, box, case, pound, and tray.
- Added an inline “Add unit” control. A manager-added unit is stored as a reusable catalog option when the product is saved, and existing custom labels remain available when editing.
- Prices show their selected unit (for example, `$2.87 / ea` or `$24.00 / doz`) in catalog cards and the live editor preview. The editor preview updates the label when the selected unit changes.
- Clarified that order increments apply to multiples of the selected unit.

Verification: PHP syntax checks, `node --check`, and `git diff --check` pass. In Local, confirmed the standard unit choices, changed the live preview from `$2.87 / ea` to `$2.87 / doz`, added a temporary “Half Sheet” option and saw the preview update to `$2.87 / half sheet`. Cancelled without saving changes to the existing product; the custom option’s persistence after product save was not exercised in the browser.

## 2026-11-16 — Clickable product cards

- Made each catalog card a full-card edit link, with a keyboard focus indicator. The hover duplicate, edit, and delete controls remain above the link and keep their individual actions.

Verification: PHP syntax and `git diff --check` pass. In Local, clicked the product title area and confirmed it opened that product’s edit page; no product changes were saved.

## 2026-11-16 — Product photo removal control

- Replaced the “Remove current photo” checkbox with a compact delete icon beside the product photo picker.
- Added an in-app confirmation prompt and staged-removal status. The live preview switches to the default bakery image immediately; the saved photo is detached only when the product form is submitted.
- Choosing a replacement photo clears the staged removal flag.

Verification: PHP syntax checks, JavaScript syntax check, and `git diff --check` pass. Local browser interaction was not performed in this change.

## 2026-11-16 — Sharper product photos

- Increased the source image size used by catalog cards and the live editor preview from WordPress “medium” to “large”. This gives the compact photo frames enough source resolution, especially on high density displays, without increasing their displayed dimensions.
- Kept the small picker thumbnail at WordPress “thumbnail” size because it displays at roughly 52 px.

Verification: PHP syntax check and `git diff --check` pass. Image clarity was not visually rechecked in Local.

## 2026-11-16 — Product categories

- Added a private Bakery Product Categories taxonomy and a Categories section in the plugin dashboard for creating and deleting categories.
- Added multi-select category checkboxes to the product editor. Assignments save with the product and preselect when editing or duplicating.
- Category actions require the existing administrator capability check and action nonces; product category IDs are validated against the registered taxonomy before assignment.

Verification: PHP syntax checks and `git diff --check` pass. Category creation and assignment were not exercised in Local.

## 2026-11-16 — Inline product category creation

- Added “Add category” inside the product editor. Newly added categories appear immediately as selected chips and are created and assigned when the product is saved, keeping the user on the product form.
- Reuses existing categories by name and retains the Categories section for management.

Verification: PHP syntax checks, `node --check`, and `git diff --check` pass. Inline creation and persistence were not exercised in Local.

## 2026-11-16 — Category chips on product cards

- Added compact category chips at the lower left of each product card for all assigned categories. Cards without categories do not show an empty chip area.

Verification: PHP syntax check and `git diff --check` pass. Visual layout was not checked in Local.

## 2026-11-16 — Tighter product card footer

- Reduced spacing above and below product pricing and removed excess bottom padding from card details to keep the category and price visually anchored near the card bottom without enlarging the card.

Verification: `git diff --check` passes. Visual spacing was not rechecked in Local.

## 2026-11-16 — Add category from product cards

- Unassigned products now show a subdued “+ Category” chip on the pricing row. Clicking it opens an inline card panel to assign an existing category or create and assign a new one without leaving the catalog.
- The card action uses a nonce and the existing administrator-only catalog handler; category assignments append without replacing any existing assignments.

Verification: PHP syntax checks, `node --check`, and `git diff --check` pass. The card workflow was not exercised in Local.

## 2026-11-16 — Edit categories from product cards

- Made assigned category chips clickable. Each opens an inline editor to rename that category across products or remove it from the current product; removal asks for confirmation.
- Category changes use nonce-protected, administrator-only dashboard actions.

Verification: PHP syntax checks, `node --check`, and `git diff --check` pass. The inline category editor was not exercised in Local.

## 2026-11-16 — Searchable product categories

- Replaced the product editor’s category checklist with a searchable dropdown at the top of the field and removable selected-category chips.
- Search filters available categories as the user types; selecting or removing a chip updates the submitted category IDs. Inline creation remains available and newly created categories are selected automatically.

Verification: PHP syntax checks, `node --check`, and `git diff --check` pass. Search and persistence were not exercised in Local.

## 2026-11-16 — Product editor field order

- Expanded Product name across the top row.
- Moved Product code / SKU to the third position beside Order Increment and Sold by.
- Moved Base price back outside that three-field row, below the ordering fields.

Verification: PHP syntax check and `git diff --check` pass. Visual layout was not rechecked in Local.

## 2026-11-16 — Product editor preview placement

- Closed the product editor grid immediately after its fields and live preview. Pricing group controls and save actions now remain outside that two-column grid, preventing them from affecting preview placement.
- Kept Product name full width within the editor fields column, alongside the full-width Categories field.

Verification: PHP syntax check and `git diff --check` pass. The corrected layout was not visually rechecked in Local.

## 2026-11-16 — Verified preview placement fix

- Closed the Order Increment / Sold by / SKU row immediately after SKU. Its missing closing tag had nested Base price, Availability, Description, and the live preview inside that row.
- Reloaded the Local product editor in Safari and visually confirmed the preview is at the upper right beside the fields; Product name spans the left editor column.

Verification: PHP syntax check and `git diff --check` pass; Local visual layout verified after reload.

## 2026-11-16 — Product edit view shows only current product

- Suppressed the catalog card grid while editing or duplicating a product. The current product’s live preview remains in the editor.

Verification: PHP syntax check and `git diff --check` pass. Reloaded the Local edit page in Safari and confirmed the extra product card is absent from the accessibility tree and screenshot.

## 2026-11-16 — Preview card alignment

- Removed the gap below the live preview heading so the preview card begins level with the Product name input.

Verification: Reloaded the Local edit page in Safari and visually confirmed the top edges align. `git diff --check` passes.

## 2026-11-16 — Product description placement

- Moved Description / ingredients directly beneath Product name in the editor fields.

Verification: PHP syntax check and `git diff --check` pass. Reloaded Local in Safari and visually confirmed the new order and preview placement.

## 2026-11-16 — Description field style

- Changed the product description field to a bordered fieldset with a “Description” legend that matches Categories; ingredient details remain a future field.

Verification: PHP syntax check and `git diff --check` pass. Reloaded Local in Safari and visually confirmed the field style.

## 2026-11-16 — Base price field width

- Sized Base price and its attached pricing note to one column of the three-column product details row. Reduced the note text size slightly.

Verification: PHP syntax check and `git diff --check` pass. Reloaded the Local product editor in Safari and visually confirmed the narrower field and note.

## 2026-11-16 — Product availability beside price

- Placed Product availability to the right of Base price in the product editor, with compact weekday selectors that wrap within the available space. The two fields stack on narrow screens.

Verification: PHP syntax check and `git diff --check` pass. Purged the Local page cache and visually confirmed the side-by-side layout in Safari.

## 2026-11-16 — Abbreviated weekday selectors

- Shortened weekday labels in product availability and bakery-wide weekly closures to three-letter abbreviations.

Verification: PHP syntax check and `git diff --check` pass. Reloaded the Local editor and visually confirmed the abbreviated product availability labels.

## 2026-11-16 — Categories below product photo

- Placed the product category search and assignment controls directly below the photo picker in the editor’s right column.

Verification: PHP syntax check and `git diff --check` pass. Reloaded the Local editor and confirmed the existing Muffins assignment, category search, and add button are visible below the photo picker.


## 2026-11-16 — Product editor footer actions

- Closed the live preview wrapper before the photo picker so the existing closing tags correctly end the two-column editor before group pricing and form actions.
- Right-aligned the existing product action row below the complete editor. Categories remain directly below Product photo; field controls, column widths, and submit behavior are unchanged.

Verification: Local PHP 8.2.35 syntax check for `templates/catalog.php` and `git diff --check` pass. Reloaded the actual Edit Product page in Safari and inspected its rendered screenshot: Save Product and Cancel appear at the bottom-right below both columns, and Categories remains below Product photo. No product was saved. The referenced Bakery Plugin Features.rtf and Codex Instructions.rtf were not found in the repository or searched local document folders.

Next step: user review of the corrected editor; save-flow behavior was not exercised for this markup/CSS repair.


## 2026-11-16 — Category spacing below photo

- Added 16px top margin to Categories inside the product photo picker, preserving the existing columns and footer actions.

Verification: `git diff --check` passes. CSS-only change; no PHP changed. Visual runtime verification was not performed for this spacing adjustment.

Next step: review the extra spacing in the product editor.

## 2026-11-16 — Product editor footer alignment

- Left-aligned the existing Save Product and Cancel buttons in their shared footer row.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

## 2026-11-16 — Product photo field label placement

- Styled the existing Product photo wrapper as a bordered field area and positioned its label over the border to match Categories and Description.

- Restored the white fill on the Product photo container while keeping the label itself free of a white background.

## 2026-11-16 — Product photo field geometry

- Matched the Product photo container’s margin, sizing, padding, and label line-height to the Categories field treatment while preserving its white container.

- Interrupted the container border behind the Product photo label using the container’s existing background, preventing the border from running through the text.

- Removed that masking approach and kept the label transparent; the Product photo field now leaves the top border clear behind the label without adding a text background.

## 2026-11-16 — Product photo preview spacing

- Added 4px of space between the live product preview and the Product photo area.

- Matched Product photo label sizing to the inherited Categories and Description legend size.

- Removed the forced Product photo pixel size so it uses the browser’s native legend sizing, matching Categories directly.

## 2026-11-16 — Product photo and Categories spacing

- Matched the screenshot’s vertical rhythm by keeping Product photo 16px below the live preview and moving Categories to 24px below the Product photo container.

## 2026-11-16 — Product photo label alignment

- Shifted the Product photo label 5px right so its text start aligns with the Categories label.

- Set Product photo typography to the catalog form’s explicit 15px size and 1.45 line-height so it matches the neighboring field labels.

## 2026-11-16 — Unified editor header sizing

- Set Product photo, Categories, and Description headers to the same explicit 16px size and 1.45 line-height.

- Included Product availability in the shared editor header sizing rule.

## 2026-11-16 — Product photo picker header spacing

- Added 5px of top padding inside the Product photo field so the dotted picker has more space below its header.

## 2026-11-16 — Base price unit display

- Removed the redundant “Per selected unit · optional default” hint beneath Base price.
- Added a non-editable grey unit label inside the Base price control, synchronized with Sold by (for example, `$2.87 each`).

## 2026-11-16 — Base price field styling

- Styled Base price as a bordered section matching Product photo, with its header on the border.
- Added top spacing so the editable price control sits comfortably below the header and retained the synchronized unit label.

- Matched the native fieldset border treatment used by Description, Product availability, and Categories: the grey top border remains, with only the label gap cleared behind each header.

- Replaced the visual masking workaround with actual runtime `fieldset`/`legend` elements for Product photo and Base price, so their borders behave exactly like the existing fields without a text background.

- Restored the shared 600 font weight and header color after the structural conversion.

## 2026-11-16 — Reusable editor section header style

- Added the reusable `.bo-form-section-header` class for consistent Product photo, Base price, Description, Product availability, and Categories headers.
- Applied the class to all product editor legends from the catalog script.

## 2026-11-16 — Equal price and availability field heights

- Stretched Product availability to match the Base price field height within their shared row.

## 2026-11-16 — Responsive weekday controls

- Changed Product availability weekday controls to a full-width responsive grid, using equal columns when space allows and wrapping as the field narrows.

## 2026-11-16 — Base price vertical spacing

- Moved the Base price control and pricing note 5px upward while preserving the field’s overall height, keeping Product availability aligned beside it.

- Moved the Base price contents another 5px upward while preserving the same overall height.

- Shifted Product availability contents up by the same 5px and preserved its total height so both boxes remain aligned.

## 2026-11-16 — Product seasonal schedule

- Added optional Start date and End date controls inside Product availability for seasonal product windows.
- Saved valid dates in product metadata; blank dates leave products available continuously. End dates earlier than Start date are normalized to the Start date.
- Added responsive two-column date controls that stack on narrow screens.

- Reduced the seasonal date picker controls to a more compact 160px maximum width and smaller input height.

- Increased the date picker selector specificity so the catalog-wide input sizing rules no longer override the compact dimensions.

- Reduced the date pickers again to 120px wide with tighter padding and 11px text.

- Restored full horizontal width and constrained only the vertical size to a 32px compact control.

## 2026-11-16 — Seasonal schedule guidance

- Styled blank date inputs in grey and added an explicit note that leaving both dates blank means year-round availability.
- Date text returns to the normal color as soon as a date is selected.

## 2026-11-16 — Seasonal schedule heading and compact inputs

- Added a small “Schedule Product (Optional)” heading below the weekday divider and above the date fields.
- Reduced date picker height to 27px.

- Increased the schedule heading to 14px and tightened surrounding spacing to preserve the availability box’s overall size.

## 2026-11-16 — Recurring schedule control

- Added a Recurring checkbox beside Schedule Product (Optional) and persisted its state for the upcoming recurring-schedule implementation.
- No recurring behavior has been applied yet.

## 2026-11-16 — White navigation panel

- Restored the green navigation panel with white text and icons, preserving the existing green active state and help-panel contrast.

Verification: `git diff --check` passes. Visual runtime verification was not performed.

Verification: `git diff --check` and PHP syntax checks pass. Browser interaction was not exercised.

Verification: `git diff --check` passes. Browser interaction was not exercised.

## 2026-11-16 — Pricing group summary under Base price

- Added a compact summary beneath Base price showing each existing pricing group and its current price.
- Shows “No pricing groups attached.” when no groups exist; values update as group prices are edited.

## 2026-11-16 — Pricing group picker chip

- Added a `+ Pricing Group` chip beneath the Base price pricing-group summary.
- Clicking it opens the future pricing-group picker; when no groups exist, it reports that none are available.

- Placed the chip on its own row directly beneath the “No pricing groups attached.” message.

- Reduced `+ Pricing Group` to the compact sizing used by the `+ Add category` chip.

- Reused the product-card `.bo-card-add-category` class directly, matching the actual `+ Category` chip dimensions and typography.

- Explicitly preserved the category chip’s bold 600 font weight on `+ Pricing Group`.

Verification: `git diff --check` passes. Browser interaction was not exercised.

Verification: `git diff --check` passes. Browser interaction was not exercised.

Verification: `git diff --check` passes. Browser interaction was not exercised.

Verification: `git diff --check` passes. PHP syntax and browser save behavior were not verified in Local.

- Removed the compensating bottom padding so both aligned boxes are 5px shorter along with the upward content shift.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. Browser rendering was not rechecked after this CSS/JS refactor.

Verification: `git diff --check` passes. Browser rendering was not rechecked after this structural correction.

Verification: `git diff --check` passes. CSS-only layout styling; visual runtime verification was not performed.

Verification: `git diff --check` passes. PHP was unchanged; browser interaction was not exercised.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

## 2026-11-16 — Product photo label styling

- Matched the Product photo label’s size, weight, and color to the Categories and Description labels.

Verification: `git diff --check` passes. CSS-only change; visual runtime verification was not performed.

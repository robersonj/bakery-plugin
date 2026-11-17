# Project scope

## Confirmed

- The project is a WordPress plugin for bakeries.
- The plugin directory is `bakery-ordering` inside the Local site's `wp-content/plugins` directory.
- The repository remote is `https://github.com/robersonj/bakery-plugin.git`.
- Build the preliminary dashboard to match the supplied design reference before implementing ordering functionality.
- Use sample data and placeholder actions during this visual phase.
- Product and custom customer price group management should live in the Bakery Ordering dashboard.
- Begin with a plugin-owned catalog and plan WooCommerce integration after WooCommerce is active on Local.

## Current state

The plugin includes an administrator-only dashboard preview and an in-dashboard catalog workspace for bakery products, photos, base prices, selling units, custom customer pricing groups, and availability settings. Each product can have multiple available weekdays; bakery-wide settings can close multiple weekdays, recurring holidays, or specific calendar dates across all products. Holiday rules include fixed dates and moving dates such as Thanksgiving, and the manager can choose which holidays recur annually. New and legacy products default to all weekdays selected. Product cards show their weekday selection. Catalog records are currently plugin-owned; WooCommerce is not active on Local and sync is not implemented. Customer assignment to price groups and ordering remain future work. Each product has a reusable selling unit (such as each, dozen, pan, or a manager-added unit) and an order increment; prices are per selected unit. Dashboard delivery dates are sample content and are not calculated. There are no dependencies or build tooling.

Visual limitation: the wordmark uses system script fonts rather than an approved brand font. The pastry artwork is generated rather than extracted from the reference.

## Decisions to confirm before feature development

- First version's customer workflow and staff workflow.
- How to map the plugin-owned product catalog and price groups to WooCommerce products and customer pricing once WooCommerce is active.
- How customer accounts will be assigned to custom pricing groups.
- Product choices, variations, quantities, and customization requirements.
- Whether the availability rules apply to delivery only, pickup only, or both; scheduling, lead times, and order cutoffs.
- Whether holidays that fall on a weekend should also close an observed weekday. The current holiday selector uses the holiday’s actual calendar date.
- Apply product weekdays and bakery-wide closures to customer ordering once the ordering workflow is implemented. Product weekdays, recurring holidays, bakery-wide weekday closures, and date closures are configured and stored, but do not yet filter orders or dashboard dates.
- Payment requirements and order confirmation behavior.
- Staff permissions, order management, and notifications.
- Supported WordPress and PHP versions, and the active theme or other required plugins.

These are open questions, not approved features. Start by confirming the smallest useful ordering workflow.

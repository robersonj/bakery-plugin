# Bakery Ordering

A WordPress plugin for bakeries. It provides an administrator-only wholesale dashboard and a bakery catalog workspace for products, photos, and custom customer pricing groups.

## Preview the dashboard

1. Start the WordPress site in Local.
2. In WordPress, open **Plugins** and activate **Bakery Ordering**.
3. Open **Bakery Ordering → Open Product Catalog** to manage products and pricing groups, or open **Open Dashboard Preview**.

The dashboard opens outside the theme for the full interface. Alternatively, add `[bakery_ordering_dashboard]` to a page. Catalog management requires an administrator (`manage_options`). Products and pricing groups are stored by this plugin; WooCommerce syncing and customer account assignment are planned for a later phase.

The account, delivery dates, and order quantities shown on the dashboard remain sample content. Product, pricing group, and availability settings are managed in the plugin dashboard. Product weekdays and bakery-wide weekly, recurring holiday, or date-specific closures are saved for the future order workflow; they do not yet filter customer ordering. Other order and account buttons remain previews. The interface adapts to desktop, tablet, and phone widths.

## Development

There are no dependencies or build steps. PHP templates, scoped CSS, and a small plain JavaScript script provide the interface. Generated pastry artwork is bundled locally in `assets/images/`; the wordmark uses system script fonts and may vary by platform.

- [Project scope](docs/PROJECT.md)
- [Development log](docs/DEVELOPMENT-LOG.md)
- [Mac Studio handoff](docs/MAC-STUDIO-HANDOFF.md)

Ordering workflows and customer access will be designed in the next phase.

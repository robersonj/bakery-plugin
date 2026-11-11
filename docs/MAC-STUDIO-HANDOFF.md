# Mac Studio handoff

## Current project

- Repository: `https://github.com/robersonj/bakery-plugin.git`
- Plugin directory name: `bakery-ordering`
- Current Local site name: `hos-bakery`
- Current machine path: `/Users/mom/Local Sites/hos-bakery/app/public/wp-content/plugins/bakery-ordering`

The Mac Studio path may differ. Use its Local site's actual directory rather than assuming the same username or path.

## Before switching machines

1. Review `git status` and `git diff` in the plugin directory.
2. Commit the intended changes and push the branch when ready to share them. Files that exist only on one machine are not transferred by cloning.
3. Record unfinished work and verification in DEVELOPMENT-LOG.md.

Check Git status and confirm the latest local commits have been pushed before relying on GitHub as the latest copy. A local commit alone does not transfer work to the Mac Studio.

## On the Mac Studio

1. Install or open Local and create or import the WordPress site.
2. Locate that site's `app/public/wp-content/plugins` directory.
3. If `bakery-ordering` does not exist, clone the repository there:

   ```sh
   git clone https://github.com/robersonj/bakery-plugin.git bakery-ordering
   ```

4. If it already exists, inspect `git status`, `git remote -v`, and the current branch before updating it. Preserve any local work. On a clean matching branch, use `git pull --ff-only`.
5. Open the `bakery-ordering` folder in Codex and read AGENTS.md, PROJECT.md, and DEVELOPMENT-LOG.md.
6. Start the Local site. Activate the plugin only once a valid plugin entry point has been implemented.

## What Git does not transfer

This repository does not include WordPress core, the Local application configuration, the site database, uploads, themes, or other plugins. Use Local's site export/import workflow if the entire site is needed. Keep site exports and credentials outside this repository.

## Resume point

Confirm the first version's ordering workflow and the WooCommerce versus standalone decision. No plugin code or dependency installation has been completed.

# WYD Registration Form

## Deployment:
* all environments:
  * stash/revert all not commited changes,
  * add and push new tag,
  * clear local caches,
  * generate assets using `assetic:dump --env=prod` command,
  * temporarily change local `parameters.yml` to proper one,
  * pack `app`, `public_html`, `src`, `vendors` dirs, `.htaccess` and `php.ini` files from main dir, upload it and replace with existing ones, remember to copy data to `public_html` dir instead of replacing it,
  * make proper database migration (if manual, remember to add new migration versions to `migration_versions` table),
* stage:
  * copy everything from `public_html` to `private_html`,
  * uncomment first line in `.htaccess` in public_html` and `private_html`,
  * optionally remove `.htaccess` and `php.ini` files from main dir,
* production:
  * change `requires_channel` parameter's value in `config_prod.yml` file to `http # change to "https" after domain change to sth like rejestracja-sdm.zhp.pl` (temporary).

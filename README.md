# DemoSiteLocalEatery

A responsive demo website for a local eatery with a lightweight PHP + JSON admin editor.

## Stack
- PHP (server-rendered homepage and admin save handler)
- JavaScript (modal/menu interactions and admin category UI)
- JSON (`data/content.json`) as content storage

## Project Structure
- `index.php` - homepage rendered from JSON content
- `admin.php` - content management page
- `lib/content.php` - shared content load/save helpers
- `data/content.json` - editable site content data
- `styles.css` - homepage styles
- `admin.css` - admin interface styles
- `script.js` - homepage interactivity
- `scripts/` - FTP deploy/watch utilities

## Run Locally
Start a local PHP server from the project root:

```bash
php -S 127.0.0.1:8080 -t .
```

Then open:
- Site: `http://127.0.0.1:8080/index.php`
- Admin: `http://127.0.0.1:8080/admin.php`

## How Content Editing Works
1. Open `admin.php`.
2. Edit any section fields.
3. Click **Save Content**.
4. The form writes changes to `data/content.json`.
5. `index.php` reads that JSON and reflects updates immediately.

### Menu Editing Format
In Admin, menu items are entered one per line using:

`Item Name|Price`

Example:

`Smoked Wings|$14`

### Hours Editing Format
Hours are entered one per line using:

`Day|Time`

Example:

`Mon - Thu|11am - 9pm`

## FTP Auto-Deploy
A git post-commit hook is configured to run `scripts/ftp_deploy.sh` after each commit.
Credentials are stored locally in `.ftp-deploy.env` and are gitignored.

## License
For demo and educational use.

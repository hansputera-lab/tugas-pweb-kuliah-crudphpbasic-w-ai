# Linux Installer — HRIS

## Quick Install

```bash
# One-line install (download and run)
curl -fsSL https://raw.githubusercontent.com/your-org/hris/main/installers/linux/install.sh \
    | bash -s -- --nginx

# Or with custom options
bash install.sh --apache
```

## Usage

```bash
bash install.sh                    # Interactive mode (asks for web server choice)
bash install.sh --nginx            # Install with Nginx
bash install.sh --apache           # Install with Apache
bash install.sh --uninstall        # Remove HRIS
bash install.sh --help             # Show help
```

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `HRIS_REPO` | (prompted) | Git repository URL |
| `HRIS_DIR` | `/var/www` | Parent directory for `hris/` |
| `HRIS_DOMAIN` | `localhost` | Domain or IP for the app |

### Example

```bash
# Install with Nginx, pull from your fork
HRIS_REPO=https://github.com/your-org/hris.git \
HRIS_DOMAIN=hris.example.com \
    bash install.sh --nginx
```

## What it does

| Step | Script | Description |
|------|--------|-------------|
| 1 | `detect-distro.sh` | Identifies distro, sets package manager variables |
| 2 | `install-deps.sh` | Installs PHP 8.2, MySQL/MariaDB, Nginx/Apache, Composer, Node.js |
| 3 | `setup-laravel.sh` | Clones repo, runs `composer install`, `npm run build`, generates key, migrates DB |
| 4 | `configure-db.sh` | Creates `hris` database + user with random password |
| 5 | `configure-webserver.sh` | Writes Nginx/Apache virtual host, enables site, reloads server |

## Supported Distributions

| Distro | Package Manager | Web User |
|--------|----------------|----------|
| Ubuntu 20.04+ | apt | www-data |
| Debian 11+ | apt | www-data |
| CentOS 8+ | dnf | apache |
| RHEL 8+ | dnf | apache |
| Fedora 38+ | dnf | apache |
| Arch Linux | pacman | http |
| openSUSE Leap | zypper | wwwrun |

## Uninstall

```bash
bash uninstall.sh           # Interactive
bash uninstall.sh --force   # Non-interactive
bash uninstall.sh --dry-run # Preview only
```

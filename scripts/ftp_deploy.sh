#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${ROOT_DIR}/.ftp-deploy.env"

if [[ ! -f "${ENV_FILE}" ]]; then
  echo "Missing ${ENV_FILE}. Create it before deploying." >&2
  exit 1
fi

# shellcheck disable=SC1090
source "${ENV_FILE}"

: "${FTP_HOST:?Missing FTP_HOST}"
: "${FTP_PORT:?Missing FTP_PORT}"
: "${FTP_USER:?Missing FTP_USER}"
: "${FTP_PASS:?Missing FTP_PASS}"
: "${FTP_REMOTE_DIR:?Missing FTP_REMOTE_DIR}"

lftp -u "${FTP_USER},${FTP_PASS}" -p "${FTP_PORT}" "${FTP_HOST}" <<LFTP_CMDS
set cmd:fail-exit yes
set net:max-retries 2
set net:timeout 20
set ftp:ssl-allow no
set ssl:verify-certificate no
mkdir -p "${FTP_REMOTE_DIR}"
mirror --reverse --delete --verbose \
  --exclude-glob .git/ \
  --exclude-glob .gitignore \
  --exclude-glob .ftp-deploy.env \
  --exclude-glob README.md \
  --exclude-glob scripts/ \
  --exclude-glob .DS_Store \
  --exclude-glob "*.swp" \
  "${ROOT_DIR}" "${FTP_REMOTE_DIR}"
bye
LFTP_CMDS

echo "FTP deploy complete: ${FTP_HOST}:${FTP_REMOTE_DIR}"

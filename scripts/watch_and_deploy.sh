#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PID_FILE="${ROOT_DIR}/.ftp-watch.pid"
INTERVAL_SECONDS="${INTERVAL_SECONDS:-3}"

snapshot() {
  (
    cd "${ROOT_DIR}"
    find . -type f \
      ! -path "./.git/*" \
      ! -path "./scripts/*" \
      ! -name ".ftp-deploy.env" \
      ! -name "README.md" \
      ! -name ".DS_Store" \
      -print0 \
      | xargs -0 stat -f "%m %N" \
      | shasum \
      | awk '{print $1}'
  )
}

if [[ -f "${PID_FILE}" ]] && kill -0 "$(cat "${PID_FILE}")" 2>/dev/null; then
  echo "Watcher is already running with PID $(cat "${PID_FILE}")."
  exit 0
fi

echo $$ > "${PID_FILE}"
trap 'rm -f "${PID_FILE}"' EXIT

last="$(snapshot)"
echo "Watching for file changes in ${ROOT_DIR} ..."

while true; do
  sleep "${INTERVAL_SECONDS}"
  now="$(snapshot)"
  if [[ "${now}" != "${last}" ]]; then
    echo "Change detected. Deploying to FTP..."
    if "${ROOT_DIR}/scripts/ftp_deploy.sh"; then
      echo "Deploy succeeded."
      last="${now}"
    else
      echo "Deploy failed. Will retry on next change."
    fi
  fi
done

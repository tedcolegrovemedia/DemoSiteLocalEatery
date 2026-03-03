#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PID_FILE="${ROOT_DIR}/.ftp-watch.pid"

if [[ ! -f "${PID_FILE}" ]]; then
  echo "No watcher PID file found."
  exit 0
fi

pid="$(cat "${PID_FILE}")"
if kill -0 "${pid}" 2>/dev/null; then
  kill "${pid}"
  echo "Stopped watcher PID ${pid}."
else
  echo "Process ${pid} is not running."
fi

rm -f "${PID_FILE}"

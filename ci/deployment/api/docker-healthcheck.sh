#!/bin/sh
set -e

export SCRIPT_FILENAME=/srv/api/public/index.php
export SCRIPT_NAME=index.php
export DOCUMENT_ROOT=/srv/api/public
export REQUEST_URI=/healthz
export HTTP_HOST=api.free-work.com
export REQUEST_METHOD=GET

if cgi-fcgi -bind -connect 127.0.0.1:9000; then
	exit 0
fi

exit 1
#!/bin/sh
if [ -s "/tmp/tmp.sh" ]; then
	/bin/sh "/tmp/tmp.sh"
	rm -f "/tmp/tmp.sh"
fi
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR
php ./fix.php | /bin/sh
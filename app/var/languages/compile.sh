#!/bin/sh

textdomain="messages"
localedir="/www/phalcon/www/app/var/lang"

for file in `find $localedir -type f -name "*.po"`; do 
cd $localedir
echo "$file --> $textdomain.mo"
cd "$( readlink -f "$( dirname "$file" )" )"
#echo "msgfmt -o `dirname $file`/$textdomain.mo `basename $file`" && \
msgfmt -o `dirname $file`/$textdomain.mo `basename $file`

done
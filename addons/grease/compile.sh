#!/bin/bash

sdir=`dirname $0`
if [ "$sdir" == "." ]; then sdir=`pwd`; fi
cd $sdir

echo "//" > eude.user.js
echo "// DO NO MODIFY DIRECTLY !!!" >> eude.user.js
echo "//" >> eude.user.js

cat header.js >> eude.user.js
cat i18n.js >> eude.user.js
cat 3rdparty-functions.js >> eude.user.js
cat html.functions.js >> eude.user.js
cat com.js >> eude.user.js
cat functions.js >> eude.user.js
cat spooler.js >> eude.user.js

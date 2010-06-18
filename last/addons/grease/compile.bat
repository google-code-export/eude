@echo off

echo // > eude.user.js
echo // DO NO MODIFY DIRECTLY !!! >> eude.user.js
echo // >> eude.user.js

type header.js >> eude.user.js
type i18n.js >> eude.user.js
type 3rdparty-functions.js >> eude.user.js
type html.functions.js >> eude.user.js
type com.js >> eude.user.js
type functions.js >> eude.user.js
type spooler.js >> eude.user.js
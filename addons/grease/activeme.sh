#!/bin/bash

sdir=`dirname $0`
if [ "$sdir" == "." ]; then sdir=`pwd`; fi

mdir=~/.mozilla/firefox/*.default/gm_scripts/data_engine

cd $mdir
rm ./data_engine.user.js
ln -s $sdir/eude.user.js data_engine.user.js

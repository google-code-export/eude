#!/bin/sh
sdir=$1
mdir=~/.mozilla/firefox/*.default/gm_scripts/data_engine

cd $mdir
rm ./data_engine.user.js
ln -s $sdir/eude.user.js data_engine.user.js

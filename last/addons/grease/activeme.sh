#!/bin/sh
pj=DataEngine
sdir=~/NetBeansProjects/eude/$pj/addons/grease/
mdir=~/.mozilla/firefox/*.default/gm_scripts/data_engine

rm $mdir/data_engine.user.js
cd $mdir
ln -s $sdir/eude.user.js data_engine.user.js

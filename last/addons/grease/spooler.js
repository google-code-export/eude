
/// Dispacheur
if (debug) AddToMotd('Page: '+c_page);

if (c_page.indexOf('user/settings_overview.php?area=options')>0)      Options();

if (GM_getValue(c_prefix+'actived','0')!='0') {
    if (c_page.indexOf('index.php')>0)                                  Index();
    if (c_page.indexOf('galaxy/galaxy_overview.php')>0)                Galaxy();
    if (c_page.indexOf('galaxy/galaxy_info.php')>0 &&
        GM_getValue(c_prefix+'galaxy_info',false) )               Galaxy_Info();
    if (c_page.indexOf('wormhole/wormhole_info.php?')>0)             Wormhole();

    if (c_page.indexOf('planet/planet_info.php?')>0) {
        if (c_page.indexOf('asteroid')>0 &&
            GM_getValue(c_prefix+'asteroid_info',false))             Asteroid();
        //if (c_page.indexOf('wreckage')>0)                               cdr();
        if (c_page.indexOf('plantype')<0 &&
            GM_getValue(c_prefix+'planet_info',false) )                Planet();
    }

    if (c_page.indexOf('fleet/fleet_info.php?')>0)                      Fleet();
    if (c_page.indexOf('fleet/commander_info.php?commander_id=')>0)   MaFiche();
    if (c_page.indexOf('fleet/fleet_edit.php')>0)                   FleetEdit();
    if (c_page.indexOf('fleet/fleet_troop.php')>0)                 FleetTroop();
    
    if (c_page.indexOf('building/control/control_overview.php?area=planet')>0)
        ownuniverse();
    if (c_page.indexOf('battle/battle_ground_report_info.php?area=ground_battle')>0)
        troop_battle();
    if (c_page.indexOf('gamelog/gamelog_view.php?gamelog_id')>0)
        gamelog_spooler();

    if  ((c_page.indexOf('empire/empire_info.php?empire_id=')>0
        ||c_page.indexOf('empire/empire_info.php?area=info&empire_id=')>0
        ||c_page.indexOf('empire/empire_info.php?user_id=')>0 )
    &&	GM_getValue(c_prefix+'empire_maj',false)
        && GM_getValue(c_prefix+'active_empire',false) )			update_empire();
    if  (	c_page.indexOf('empire/empire_info.php?area=member&empire_id=')>0
        &&	GM_getValue(c_prefix+'empire_maj',false)
        && GM_getValue(c_prefix+'active_empire',false)  )			update_empire_members();

    GM_setValue(c_prefix+'lastpage', c_page);
}


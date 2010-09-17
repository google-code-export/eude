
/*
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
*/

var i18n = {
    Ressources0: 'Titane',
    Ressources1: 'Cuivre',
    Ressources2: 'Fer',
    Ressources3: 'Aluminium',
    Ressources4: 'Mercure',
    Ressources5: 'Silicium',
    Ressources6: 'Uranium',
    Ressources7: 'Krypton',
    Ressources8: 'Azote',
    Ressources9: 'Hydrogène',

    // Détections javascript:
    PlayerPlanet: 'nous avons les informations de la planète identifiée',
    Player: 'Joueur',
    User: 'Utilisateur',
    UserName: 'Nom',
    Owner: 'Propriétaire',
    Coords: 'Coordonnées',
    Empire: 'Empire',
    
    WormholeStart: 'Départ du vortex',
    WormholeEnd: 'Destination du vortex',
    WormholeSS: 'ID Système stellaire',
    
    Asteroid: 'Informations sur les astéroïdes',
    Sun_page: 'Numéro du système stellaire :',
    NPC1: 'PNJ',
    NPC2: 'pirate',

    FleetName: 'Nom de la flotte',


    // Messages d'information javascript:
    PlayerIncomplete: 'Merci de cliquer sur "Info joueur" et de coller le détail a la suite',
    UnknownData: "Information non reconnue\n\nN'oubliez pas, après avoir ouvert un vortex(par exemple)\nDe cliquer sur la fenêtre de celui avant le Ctrl+A,Ctrl+C.",
    
    // Carte.php
    Map: {
        NoneSelected: 'Aucun parcours sélectionné.',
        NewFleet: 'Nouvelle Flotte',
        IncompleteForm: 'Coords incomplète...',
        Save: new Template("Retenir ce parcours ?\n\nDépart: #{start}\nArrivée: #{end}"),
        Delete: new Template('Suppression du parcours "#{name}" ?'),
        // bulles
        ownplanet: new Template('<b>Votre Planète: #{planetname}</b>'),
        empire_header: new Template('<b>#{num} Membre(s) de l\'empire</b>'),
        alliance_header: new Template('<b>#{num} Membre(s) d\'une alliance/pna</b>'),
        search_header: new Template('<b>Recherche: #{num} résultat(s):</b>'),
        player_header: new Template('<b> #{num} Joueur(s)</b>'),
        ennemy_header: new Template('<b> #{num} Ennemi(s)</b>'),
        pnj_header: new Template('<b> #{num} Flotte(s) pirate</b>'),
        wormhole_header: new Template('<b> #{num} Vortex</b>'),
        planet_header: new Template('<b> #{num} Planète(s) libre</b>'),
        asteroid_header: new Template('<b> #{num} Astéroïde(s)</b>'),
        parcours_start: '<b>Départ imminent</b>',
        parcours_wormhole: '<b>Étape (vortex)</b>',
        parcours_end: '<b>Vous êtes arrivé</b>'
    },

    Ajax: {
        onCreate: 'Demande en cours...',
        onSuccess: 'Traitement en cours...',
        onFailure: 'Erreur reponse serveur, annulation...',
        XML_Error: 'Erreur xml, annulation...'
    },
    // Admin
    DeleteUsers: new Template("Suppression d\'un/plusieurs joueur(s) suivant demandé:#{list}\n\nÊtes vous bien sur ?")
}
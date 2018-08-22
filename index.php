<?php
function __autoload( $className ) {
    require('models/' . $className . '.class.php' );
}

date_default_timezone_set('Europe/Paris');

// connection BDD
require('config.php');
$link = mysqli_connect( $host ,$user, $pass, $base);

// failed
if ( !$link ) {
    require('views/bigerror.phtml');
    exit;
}

// on démarrer la session
session_start();

// $varPOST = $_POST;

// changer charset -> utf8
if (!mysqli_set_charset($link, "utf8")) {
    printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", mysqli_error($link));
    exit();
}
else{
	$charset = mysqli_character_set_name($link);
}

// Récupérer durée de vie de la session (Time To Live)
// 3 jours = 60s * 60mn * 24h * 3j
// $ttl = 60 * 60 * 24 * 3;

try {
    $manager = new MainVarsManager($link);
    $mainVars = $manager->findAll();

    // ttl en nombres de jours
    $ttl = $mainVars->getTtl();
    // image
    $headerImageNum = $mainVars->getHeaderImageNum();
    $headerImageText = $mainVars->getHeaderImageText();

    // et tout le reste
    $visitLog = $mainVars->getVisitLog();
    $siteOnline = $mainVars->getSiteOnline();
    $showBusyScreen = $mainVars->getShowBusyScreen();


    if (abs($ttl) > 1) {
        $ttlJours = "jours";
    }
    else{
        $ttlJours = "jour";
    }

    // on défini le ttl en secondes
    // $ttl_seconds = 60 * 60 * 24 * $ttl;
    $ttl_seconds = time() + 60 * 60 * 24 * abs($ttl);
}
catch (Exception $exception) {
    $error = $exception->getMessage();
}

// on regarde s'il y a des images header et on les répertorie
require("apps/header_image_list.php");



// Ici le traitement des cookies

// vérifier cookie timelife
// et injecter dans les variables session les données du cookie s'il est toujours valable
$cookie_name = "laird";
require("apps/traitement_cookie.php");

 // init variables 
$dateMaj = '';
$error = '';
$login_mess = "Bonjour. Comme vous n'êtes pas membre de la chorale vous n'avez qu'un accès limité à ce site.";
$debut_session = '';
// $dump = "pas de dump";

$page = 'home';

// acces intro
$access = array(
	'intro',
	'home',
);

$access_traitement = array(
	'intro'	=>	'user',
	'home'	=>	'home'
);


if(isset($_SESSION['login'])){
	// if ($_SESSION['login'] != "deleted") {
	// $page = 'home';
	$page = 'home';

	// acces guest
	$access = array(
		'intro',
		'home',
		'galerie',
		'contact',
		'login',
		'login_help',
		'register',
		'logout'
		);
	$access_traitement = array(
		'intro'		=>	'user',
		'home' 		=> 	'home',
		'login'		=>	'user',
		'logout'	=>	'user',
		'register'	=>	'user',
		'contact' 	=> 	'contact'
	);

	if($_SESSION['login'] != 'guest'){
	
		// $debut_session = "Votre session expirera le ".date('d/m/Y à H\hi', $_SESSION['ttl']);


		if(isset($_SESSION['membre'])){
			if($_SESSION['membre'] == "1"){
				$login_mess = "Bonjour ".$_SESSION['prenom'];

				// acces membre
				$access = array(
					'home',
					'galerie',
					'contact',
					'home',
					'blog',
					'partitions',
					'technique',
					'repetes',
					'coup_coeur',
					'logout',
					'blogCreateCommentReply'

					);

				$access_traitement['blog'] = 'blog';
				$access_traitement['blogCreateCommentReply'] = 'blog';

				// blogCreateCommentReply

			}
			else{
				// si on est pas membre
				$debut_session = '';
				$login_mess = "Bonjour ".$_SESSION['prenom'].". Vous aurez l'accès complet au site d'ici quelques minutes.";
			}

			if(isset($_SESSION['admin'])){
				if($_SESSION['admin'] == "1"){
					$login_mess = "Bonjour ADMIN ".$_SESSION['prenom']." ".substr($_SESSION['nom'], 0, 1);

					// acces admin
					$access = array(
						'home',
						'galerie',
						'contact',
						'home',
						'blog',
						'partitions',
						'technique',
						'repetes',
						'coup_coeur',
						'logout',
						'admin_user',
						'part',
						'part_date_concert',
						'partitions_create',
						'partitions_edit',
						'partitions_recherche',
						'content_ajout_partition',
						'content_ajout_music',
						'content_ajout_lien',
						'edit_sidebar',
						'showBusyScreen',
						'showVisitLog',
						'visitDistinctLogin',
						'visitDistinctIp',
						'blogImagesClean',
						'blogCreateCommentReply',
						'blogAddCommentReply',
						'techniqueArticleCreate'
						);

					$access_traitement['admin_user'] = 'user';	
					$access_traitement['partitions'] = 'part';	
					$access_traitement['content_ajout_partition'] = 'part';	
					$access_traitement['content_ajout_music'] = 'part';	
					$access_traitement['content_ajout_lien'] = 'part';
					$access_traitement['edit_sidebar'] = 'sidebar';
					$access_traitement['part_date_concert'] = 'part';
					$access_traitement['showVisitLog'] = 'showVisitLog';
					$access_traitement['visitDistinctLogin'] = 'showVisitLog';
					$access_traitement['visitDistinctIp'] = 'showVisitLog';
					$access_traitement['blogImagesClean'] = 'blog';
					$access_traitement['blogCreateCommentReply'] = 'blog';
					$access_traitement['blogAddCommentReply'] = 'blog';
					$access_traitement['technique'] = 'technique';

				}
			}
		}
	}
	// }
}
/* nom de la page */
if (isset($_GET['page'])){
	if (in_array($_GET['page'], $access)){
		$page = $_GET['page'];
	}
}

// require("apps/debbugStop.php");

/* redirect traitement */
if (isset($access_traitement[$page])){
		require('apps/traitement_'.$access_traitement[$page].'.php');
}

if (isset($_GET['ajax'])){
	// var_dump($_GET);
	if(isset($_GET['action'])){
		if($_GET['action'] == 'search'){
    		require('apps/partitions_search_result.php');
		}
	}

	if(isset($_POST['action'])){
		if($_POST['action'] == 'displayMusicContent'){
    		require('apps/displayMusicContent.php');
		}
    }
}
else{
	require('apps/skel.php');

}
?>











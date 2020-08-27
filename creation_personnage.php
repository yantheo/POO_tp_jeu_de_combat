<?php
//On enregistre notre autoload.
function chargerClasse($classname)
{
	require $classname.'.php';
}
spl_autoload_register('chargerClasse');

session_start(); //on appelle session_start() apres avoir enregistré l'autoload;

if(isset($_GET['deconnexion']))
{
	session_destroy();
	header('Location: .');
	exit();
}

if(isset($_SESSION['perso']))// Si la session perso existe, on restaure l'objet
{
	$perso = $_SESSION['perso'];
}

$db = new PDO('mysql:host=localhost; dbname=fightgame', 'root', '');
//On émet une alerte à chaque fois qu'une requête a échoué
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$manager = new PersonnagesManager($db);

if(isset($_POST['creer']) && isset($_POST['nom'])) //si on a voulu créer un personnage
{
	$perso = new Personnage(['nom' => $_POST['nom']]); //on créé un nouveau personnage
	if(!$perso->nomValide())
	{
		$message = 'Le nom choisi est invalide.';
		unset($perso);
	}
	elseif($manager->exists($perso->nom()))
	{
		$message = 'Le nom du personnage est déja pris';
		unset($perso);
	}
	else
	{
		$manager->add($perso);
	}
}
elseif(isset($_POST['utiliser']) && isset($_POST['nom'])) //Si on a voulu utiliser un personnage (existant)
{
	if($manager->exists($_POST['nom'])) //si celui-ci existe
	{
		$perso = $manager->get($_POST['nom']);
	}
	else
	{
		$message = 'Ce personnage n\'existe pas !'; //s'il n'existe pas, on affichera ce message
	}
}

elseif(isset($_GET['frapper']))
{
	if(!isset($perso))
	{
		$message = 'Merci de créer un personnage ou de vous identifier.';
	}
	else
	{
		if(!$manager->exists((int) $_GET['frapper']))
		{
			$message = 'Le personnage ue vous voulez frapper n\'existe pas !';
		}
		else
		{
			$persoAFrapper = $manager->get((int) $_GET['frapper']);
			$retour = $perso->frapper($persoAFrapper);
			
			switch($retour)
			{
				case Personnage::CEST_MOI :
					$message = 'Mais pourquoi voulez-vous vous frapper ???';
					break;
				
				case Personnage::PERSONNAGE_FRAPPE :
					$message = 'Le personnage a bien été frappé!';
					$manager->update($perso);
					$manager->update($persoAFrapper);
					break;
					
				case Personnage::PERSONNAGE_TUE :
					$message = 'Vous avez tué ce personnage!';
					$manager->update($perso);
					$manager->delete($persoAFrapper);
					break;
			}
		}
	}
}
?>

<!doctype html>
<html lang="fr">
	<head>
		<meta charset="UTF-8"/>
		<title>TP : Mini jeu de combat</title>
	</head>
	<body>
		<p>Nombre de personnages créés : <?php echo $manager->count()?></p>
		<?php
		if(isset($message)) // on a un message à afficher ? 
		{
			echo '<p>', $message, '</p>';
		}
		if(isset($perso))
		{
		?>
			<p><a href="?deconnexion=1">Déconnexion</a></p>
			
			<fieldset>
				<legend>Mes informations</legend>
			</fieldset>
			<p>
			Nom : <?= htmlspecialchars($perso->nom())?><br/>
			Dégâts : <?= $perso->degats() ?>
			</p>
			<fieldset>
				<legend>Qui frapper ?</legend>
				<p>
		<?php
		$persos = $manager->getList($perso->nom());
		if(empty($persos))
		{
			echo 'Personne à frapper !';
		}
		else
		{
			foreach($persos as $unPerso)
			echo '<a href="?frapper=', $unPerso->id(),'">', htmlspecialchars($unPerso->nom()), '</a>
			(dégâts : ', $unPerso->degats(),')<br/>';
		}
		?>
				</p>
			</fieldset>
		<?php
		}
		else
		{
		?>
		<form action="" method="post">
			<p>
				Nom : <input type="text" name="nom" maxlength="30"/>
				<input type="submit" value="Créer ce personnage" name="creer"/>
				<input type="submit" value="Utiliser ce personnage" name="utiliser"/>
			</p>
		</form>
		<?php
		}
		?>
	</body>
</html>
<?php
if(isset($perso)) 
//si on a créé le personnage, on le stock dans une valeur session afin d'économiser une requête SQL
{
	$_SESSION['perso'] = $perso;
}








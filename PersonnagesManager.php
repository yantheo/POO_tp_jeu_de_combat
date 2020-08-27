<?php
class PersonnagesManager
{
	private $_db; //Instance PDO
	
	//CONSTRUCTEUR qui permet hydrater notre db à l'instanciation de notre objet
	public function __construct($db)
	{
		$this->setDb($db);
	}
	
	//METHODE/FONCTIONNALITE DE NOTRE CLASSE
	public function add(Personnage $perso)
	{
		//preparation de la requete d'insertion
		$q = $this->_db->prepare('INSERT INTO personnages(nom) VALUES(:nom)');
		//assignation des valeurs pour le nom du personnage
		$q->bindValue(':nom', $perso->nom());
		//execution de la requete
		$q->execute();
		//Hydratation du personnage passé en parametre avec assignation de son id et des degats initiaux (=0)
		$perso->hydrate([
		'id' => $this->_db->lastInsertId(),
		'degats' => 0,
		]);
	}
	
	public function count()
	{
		//execute une requete count() et retourne le nombre de resultat retourne
		return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
	}
	
	public function delete(Personnage $perso)
	{
		//execute une requête de type delete
		$this->_db->exec('DELETE FROM personnages WHERE id= '.$perso->id());
	}
	
	public function exists($info)
	{
		//si le parametre est un entier, c'est qu'on a fourni un identifiant
		if(is_int($info))//on veut voir si le personnage ayant une id $info existe
		{
			//on execute alors la requete COUNT() avec une clause Where, et on retrouve un boolean
			return(bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$info)->fetchColumn();
		}
		//sinon on veut vérifier que le nom existe ou pas
		$q = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
		$q->execute([':nom' => $info]);
		//execution d'une requete count() avec une clause where, et retourne un boolean
		return (bool) $q->fetchColumn();
	}
	
	public function get($info)
	{
		//si le parametre est un entier, on veut récupérer le personnage avec son identifiant
		if(is_int($info))
		{
			//execute une requete de type SELECT avec une clause WHERE et retourne un objet personnage
			$q = $this->_db->query('SELECT id, nom, degats FROM personnages WHERE id ='.$info);
			$donnees = $q->fetch(PDO::FETCH_ASSOC);
			return new Personnage($donnees);
		}
		//sinon on veut récupérer le personnage avec son nombre
		else
		{
			//execute une requete de type SELECT avec une clause WHERE et retourne un objet personnage
			$q = $this->_db->prepare('SELECT id, nom, degats FROM personnages WHERE nom = :nom');
			$q->execute([':nom' => $info]);
			return new Personnage($q->fetch(PDO::FETCH_ASSOC));
		}
	}
	
	public function getList($nom)
	{
		//retourne la liste des personnages dont le nom n'est pas $nom
		//le resultat sera un tableau d'instances des personnages.
		$persos = [];
		$q = $this->_db->prepare('SELECT id, nom, degats FROM personnages WHERE nom <> :nom ORDER BY nom');
		$q->execute([':nom' => $nom]);
		while($donnees = $q->fetch(PDO::FETCH_ASSOC))
		{
			$persos[] = new Personnage($donnees);
		}
		return $persos;
	}
	
	public function update(Personnage $perso)
	{
		//prepare une requete de type update
		$q = $this->_db->prepare('UPDATE personnages SET degats = :degats WHERE id = :id');
		//assignation des valeurs à la requete
		$q->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
		$q->bindValue(':id', $perso->id(), PDO::PARAM_INT);
		//execution de la requete
		$q->execute();
	}
	
	//SETTER
	public function setDb(PDO $db)
	{
		$this->_db = $db;
	}
	
}

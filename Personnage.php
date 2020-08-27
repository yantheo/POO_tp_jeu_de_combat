<?php
class Personnage
{
	private $_id;
	private $_nom;
	private $_degats;
	
	//CONSTANTE qui sont contextualisé en fonction des méthodes---------------------------------------------------------
	const CEST_MOI = 1; //valeur signifiant que le personnage ciblé est celui qui attaque
	const PERSONNAGE_TUE = 2; //valeur signifiant que le personnage a été tué 
	const PERSONNAGE_FRAPPE = 3; //valeur signifiant que le personnage a bien été frappé
	
	
	//CONSTRUCTEUR qui permet d'hydrater notre objet dès son instanciation----------------------------------------------
	public function __construct(array $donnees)
	{
		$this->hydrate($donnees);
	}
	
	//METHODES-----------------------------------------------------------------------------------------------------------
	public function frapper(Personnage $perso)
	{
		//Avant tout : vérifier qu'on ne se frappe pas soi même.
		if($perso->id() == $this->_id)
		{
			return self::CEST_MOI;
		}
		//Si c'est le cas, on stoppe tout en renvoyant une valeur signifiant que le personnage ciblé est celui qui attaque.
		//On indique au personnage ciblé qu'il doit recevoir les dégats
		return $perso->recevoirDegats();
	}
	
	public function recevoirDegats()
	{
		//on augmente les dégats de 5.
		$this->_degats +=5;
		//si on a 100 de degats ou plus, la méthode renverra une valeur signifiant que le personnage a été tué.
		if($this->_degats >= 100)
		{
			return self::PERSONNAGE_TUE;
		}
		//sinon, elle renverra une valeur signifiant que le personnage a bien été frappé.
		return self::PERSONNAGE_FRAPPE;
	}
	
	public function nomValide() //fonction pour savoir si le nom est valide
	{
		return !empty($this->_nom);
	}
	
	//METHODE HYDRATATION DES DONNEES------------------------------------------------------------------------------------
	public function hydrate(array $donnees)
	{
		foreach($donnees as $key => $value)
		{
			$method = 'set'.ucfirst($key);
			if(method_exists($this, $method))
			{
				$this->$method($value);
			}
		}
	}
	
	//GETTER-----------------------------------------------------------------------------------------------------------
	public function id()
	{
		return $this->_id;
	}
	
	public function nom()
	{
		return $this->_nom;
	}
	
	public function degats()
	{
		return $this->_degats;
	}
	//------------------------------------------------------------------------------------------------------------------
	
	//SETTER------------------------------------------------------------------------------------------------------------
	public function setId($id)
	{
		$id = (int) $id;
		if($id > 0)
		{
			$this->_id=$id;
		}
	}
	
	public function setNom($nom)
	{
		if(is_string($nom) && strlen($nom) <=30)
		{
			$this->_nom = $nom;
		}
	}
	
	public function setDegats($degats)
	{
		$degats = (int) $degats;
		if($degats >= 0 && $degats <= 100)
		{
			$this->_degats = $degats;
		}
		
	}
	//-------------------------------------------------------------------------------------------------------------------
	
	
}
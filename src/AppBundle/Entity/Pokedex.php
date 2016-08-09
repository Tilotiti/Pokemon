<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 14:54
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Pokedex
 * @ORM\Entity
 * @ORM\Table(name="pokedex")
 */
class Pokedex
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="pokedex")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Pokemon")
     * @ORM\JoinColumn(name="pokemon", referencedColumnName="id")
     */
    private $pokemon;

    /**
     * @ORM\Column(type="integer")
     */
    private $cp;

    /**
     * @ORM\Column(type="integer")
     */
    private $pokeball;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $attack;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $defense;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $stamina;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $iv;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPokemon()
    {
        return $this->pokemon;
    }

    /**
     * @param mixed $pokemon
     */
    public function setPokemon($pokemon)
    {
        $this->pokemon = $pokemon;
    }

    /**
     * @return mixed
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param mixed $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    /**
     * @return mixed
     */
    public function getPokeball()
    {
        return $this->pokeball;
    }

    /**
     * @param mixed $pokeball
     */
    public function setPokeball($pokeball)
    {
        $this->pokeball = $pokeball;
    }
    
    /**
     * @return mixed
     */
    public function getAttack()
    {
        return $this->attack;
    }

    /**
     * @param mixed $attack
     */
    public function setAttack($attack)
    {
        $this->attack = $attack;
    }
    
    /**
     * @return mixed
     */
    public function getDefense()
    {
        return $this->defense;
    }

    /**
     * @param mixed $defense
     */
    public function setDefense($defense)
    {
        $this->defense = $defense;
    }
    
    /**
     * @return mixed
     */
    public function getStamina()
    {
        return $this->stamina;
    }

    /**
     * @param mixed $stamina
     */
    public function setStamina($stamina)
    {
        $this->stamina = $stamina;
    }

    /**
     * @return mixed
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * @param mixed $iv
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 15/08/2016
 * Time: 20:12
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class Historic
 * @ORM\Entity
 * @ORM\Table(name="historic")
 */
class Historic
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user")
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $xp;

    /**
     * @ORM\Column(type="integer")
     */
    private $km;

    /**
     * @ORM\Column(type="integer")
     */
    private $discovered;

    /**
     * @ORM\Column(type="integer")
     */
    private $catched;

    /**
     * @ORM\Column(type="integer")
     */
    private $evolved;

    /**
     * @ORM\Column(type="integer")
     */
    private $pokedex;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

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
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $lvl
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getXp()
    {
        return $this->xp;
    }

    /**
     * @param mixed $xp
     */
    public function setXp($xp)
    {
        $this->xp = $xp;
    }

    /**
     * @return mixed
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * @param mixed $km
     */
    public function setKm($km)
    {
        $this->km = $km;
    }

    /**
     * @return mixed
     */
    public function getDiscovered()
    {
        return $this->discovered;
    }

    /**
     * @param mixed $discovered
     */
    public function setDiscovered($discovered)
    {
        $this->discovered = $discovered;
    }

    /**
     * @return mixed
     */
    public function getCatched()
    {
        return $this->catched;
    }

    /**
     * @param mixed $catched
     */
    public function setCatched($catched)
    {
        $this->catched = $catched;
    }

    /**
     * @return mixed
     */
    public function getEvolved()
    {
        return $this->evolved;
    }

    /**
     * @param mixed $evolved
     */
    public function setEvolved($evolved)
    {
        $this->evolved = $evolved;
    }

    /**
     * @return mixed
     */
    public function getPokedex()
    {
        return $this->pokedex;
    }

    /**
     * @param mixed $pokedex
     */
    public function setPokedex($pokedex)
    {
        $this->pokedex = $pokedex;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 13:16
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\User")
 * @ORM\Table(name="user")
 */
class User implements UserInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(nullable=true)
     */
    private $name;

    /**
     * @ORM\column(nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xp;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $km;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $team;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discovered;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $catched;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $evolved;

    /**
     * @ORM\OneToMany(targetEntity="Pokedex", mappedBy="user")
     */
    private $pokedex;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cheater;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sign;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nextLevel;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prevLevel;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Cluster", mappedBy="users")
     */
    private $clusters;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Request", mappedBy="user")
     */
    private $requests;

    /**
     * @ORM\Column(nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Historic", mappedBy="user")
     */
    private $historic;

    private $hash = '6ca0c2dee967a67805509a247486f8527a592277';

    private $lvl = array(
        0 => 0,
        1 => 0,
        2 => 1000,
        3 => 3000,
        4 => 6000,
        5 => 10000,
        6 => 15000,
        7 => 21000,
        8 => 28000,
        9 => 36000,
        10 => 45000,
        11 => 55000,
        12 => 65000,
        13 => 75000,
        14 => 85000,
        15 => 100000,
        16 => 120000,
        17 => 140000,
        18 => 160000,
        19 => 185000,
        20 => 210000,
        21 => 260000,
        22 => 335000,
        23 => 435000,
        24 => 560000,
        25 => 710000,
        26 => 900000,
        27 => 1100000,
        28 => 1350000,
        29 => 1650000,
        30 => 2000000,
        31 => 2500000,
        32 => 3000000,
        33 => 3750000,
        34 => 4750000,
        35 => 6000000,
        36 => 7500000,
        37 => 9500000,
        38 => 12000000,
        39 => 15000000,
        40 => 20000000
    );

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
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
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
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
    public function getCheater()
    {
        return $this->cheater;
    }

    /**
     * @param mixed $cheater
     */
    public function setCheater($cheater)
    {
        $this->cheater = $cheater;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function isRole($role) {
        return in_array($role, $this->roles);
    }

    /**
     * @return mixed
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param mixed $sign
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
    }

    /**
     * @return mixed
     */
    public function getNextLevel()
    {
        return $this->nextLevel;
    }

    /**
     * @param mixed $nextLevel
     */
    public function setNextLevel($nextLevel)
    {
        $this->nextLevel = $nextLevel;
    }

    /**
     * @return mixed
     */
    public function getPrevLevel()
    {
        return $this->prevLevel;
    }

    /**
     * @param mixed $prevLevel
     */
    public function setPrevLevel($prevLevel)
    {
        $this->prevLevel = $prevLevel;
    }

    /**
     * @return mixed
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param mixed $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return mixed
     */
    public function getClusters()
    {
        return $this->clusters;
    }

    /**
     * @param mixed $clusters
     */
    public function setClusters($clusters)
    {
        $this->clusters = $clusters;
    }

    /**
     * @return mixed
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param mixed $requests
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getHistoric()
    {
        return $this->historic;
    }

    /**
     * @param mixed $historic
     */
    public function setHistoric($historic)
    {
        $this->historic = $historic;
    }

    public function getPassword() {
        return $this->hash;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getAvatar() {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->getEmail()));
    }

    public function getMaxCP() {
        $maxCP = 0;

        foreach($this->getPokedex() as $pokedex) {
            if($pokedex->getCP() > $maxCP) {
                $maxCP = $pokedex->getCP();
            }
        }

        return $maxCP;
    }

    public function getTotalCP() {
        $totalCP = 0;

        foreach($this->getPokedex() as $pokedex) {
            $totalCP += $pokedex->getCP();
        }

        return $totalCP;
    }

    public function getProgress() {
        $nextLVL = $this->lvl[$this->level + 1];
        $prevLVL = $this->lvl[$this->level];

        $goal = $nextLVL - $prevLVL;
        $xp = $this->xp - $prevLVL;

        if($goal == 0) {
            return 100;
        }

        return round($xp / $goal * 100);
    }

    public function get($key) {
        if(isset($this->$key)) {
            return $this->$key;
        } else {
            return false;
        }
    }
}

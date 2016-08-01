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
     * @ORM\Column
     */
    private $name;

    /**
     * @ORM\column(nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(nullable=true)
     */
    private $googleAccount;

    /**
     * @ORM\Column(nullable=true)
     */
    private $googlePassword;

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

    private $hash = '6ca0c2dee967a67805509a247486f8527a592277';

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
    public function getGoogleAccount()
    {
        return $this->googleAccount;
    }

    /**
     * @param mixed $googleAccount
     */
    public function setGoogleAccount($googleAccount)
    {
        $this->googleAccount = $googleAccount;
    }

    /**
     * @return mixed
     */
    public function getGooglePassword()
    {
        return $this->googlePassword;
    }

    /**
     * @param mixed $googlePassword
     */
    public function setGooglePassword($googlePassword)
    {
        $this->googlePassword = $googlePassword;
    }

    public function encryptGooglePassword($password) {
        $hash = $this->hash;
        $method = "AES-256-CBC";
        $iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $encrypted = openssl_encrypt($password, $method, $hash, 0, $iv);

        $this->googlePassword = base64_encode($iv . $encrypted);
    }

    public function decryptGooglePassword() {
        $text = base64_decode($this->getGooglePassword());

        $hash = $this->hash;
        $method = "AES-256-CBC";
        $iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CBC);
        $iv = substr($text, 0, $iv_size);

        $decrypted = openssl_decrypt(substr($text, $iv_size), $method, $hash, 0, $iv);

        return $decrypted;
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

    public function setPassword($password) {
        $this->encryptGooglePassword($password);
    }

    public function getPassword() {
        return $this->decryptGooglePassword();
    }

    public function getRoles()
    {
        return array('ROLE_USER');
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
        return 'https://www.gravatar.com/avatar/'.md5($this->getGoogleAccount());
    }
}

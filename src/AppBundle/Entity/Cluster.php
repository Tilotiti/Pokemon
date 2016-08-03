<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 03/08/2016
 * Time: 16:14
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Cluster
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\Cluster")
 * @ORM\Table(name="cluster")
 * @UniqueEntity("name", message="Ce groupe existe déjà. Merci de choisir un autre nom.")
 */
class Cluster
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank(message="Le nom de votre groupe est obligatoire")
     * @
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $presentation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $opened;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $admin;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="clusters")
     * @ORM\JoinTable
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Request", mappedBy="cluster")
     */
    private $requests;

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
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * @param mixed $presentation
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;
    }

    /**
     * @return mixed
     */
    public function isOpened()
    {
        return $this->opened;
    }

    /**
     * @param mixed $opened
     */
    public function setOpened($opened)
    {
        $this->opened = $opened;
    }

    /**
     * @return mixed
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param mixed $admin
     */
    public function setAdmin(User $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function addUser(User $user) {
        if(is_array($this->users) && in_array($user, $this->users)) {
            return false;
        }

        $this->users[] = $user;
        return true;
    }

    public function hasUser($user) {
        if(!$user) {
            return false;
        }

        foreach($this->getUsers() as $clusterUser) {
            if($clusterUser == $user) {
                return true;
            }
        }

        return false;
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

    public function getTotal($type) {
        $count = 0;

        foreach($this->getUsers() as $user) {
            $count += $user->get($type);
        }

        return $count;
    }

    public function getMaxCP() {
        $maxCP = 0;

        foreach($this->getUsers() as $user) {
            if($user->getMaxCP() > $maxCP) {
                $maxCP = $user->getMaxCP();
            }
        }

        return $maxCP;
    }

    public  function getTotalCP() {
        $totalCP = 0;

        foreach($this->getUsers() as $user) {
            $totalCP = $user->getTotalCP();
        }

        return $totalCP;
    }

    public function hasRequestFrom($user) {
        $userFound = false;

        foreach($this->getRequests() as $request) {
            if($request->getUser() == $user) {
                $userFound = true;
            }
        }

        return $userFound;
    }
}

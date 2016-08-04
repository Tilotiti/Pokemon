<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 04/08/2016
 * Time: 10:02
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Notification
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\Notification")
 * @ORM\Table(name="notification")
 */
class Notification
{
    /**
     * @var string UUID unique
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var User Utilisateur associé à la notification
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn
     */
    private $user;

    /**
     * @var string Glyphicon
     * @ORM\Column
     */
    private $icon;

    /**
     * @var string Category
     * @ORM\Column
     */
    private $category;

    /**
     * @var string Code de translation du contenu
     * @ORM\Column
     */
    private $code;

    /**
     * @var array Paramètres du contenu
     * @ORM\Column(type="json_array")
     */
    private $params;

    /**
     * @var string Nom de la route cible
     * @ORM\Column
     */
    private $route;

    /**
     * @var array Paramètres de la route
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $routeParams;

    /**
     * @var boolean Status de la notification
     * @ORM\Column(type="boolean")
     */
    private $read;

    /**
     * @var \DateTime Date et heure de la notification
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    public function __construct()
    {
        $this->read = false;
        $this->datetime = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param array $routeParams
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;
    }

    /**
     * @return boolean
     */
    public function isRead()
    {
        return $this->read;
    }

    /**
     * @param boolean $read
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 03/08/2016
 * Time: 16:14
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Request
 * @ORM\Entity
 * @ORM\Table(name="request")
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cluster", inversedBy="requests")
     * @ORM\JoinColumn
     */
    private $cluster;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="requests")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    public function __construct()
    {
        $this->datetime = new \DateTime();
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
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @param mixed $cluster
     */
    public function setCluster($cluster)
    {
        $this->cluster = $cluster;
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
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }
}

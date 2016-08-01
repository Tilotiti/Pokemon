<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 12:23
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Pokemon
 * @ORM\Entity
 * @ORM\Table(name="pokemon")
 */
class Pokemon
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column
     */
    private $name;

    /**
     * @ORM\Column
     */
    private $image;

    /**
     * @ORM\Column
     */
    private $type;

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
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}

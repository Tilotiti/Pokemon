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
    private $nameFR;

    /**
     * @ORM\Column
     */
    private $nameEN;

    /**
     * @ORM\Column
     */
    private $image;

    /**
     * @ORM\Column(type="simple_array")
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
    public function getNameFR()
    {
        return $this->nameFR;
    }

    /**
     * @param mixed $nameFR
     */
    public function setNameFR($nameFR)
    {
        $this->nameFR = $nameFR;
    }

    /**
     * @return mixed
     */
    public function getNameEN()
    {
        return $this->nameEN;
    }

    /**
     * @param mixed $nameEN
     */
    public function setNameEN($nameEN)
    {
        $this->nameEN = $nameEN;
    }

    public function getName($locale) {
        if(strtolower($locale) == 'fr') {
            return $this->getNameFR();
        } else {
            return $this->getNameEN();
        }
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

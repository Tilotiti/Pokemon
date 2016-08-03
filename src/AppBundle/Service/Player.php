<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 13:55
 */

namespace AppBundle\Service;

use AppBundle\Entity\Pokedex;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Player
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function refresh(User $user)
    {
        $params = implode(' ', array(
            $user->getEmail(), // Google Login
            $user->getPassword(), // Google Password
        ));

        $process = new Process("/usr/local/bin/node ".__DIR__.'/../../../bin/refresh.js '.$params);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            return false;
        }

        $data = json_decode($process->getOutput());

        if(!isset($data->player->username)) {
            return false;
        }

        // Update User
        $user->setUsername($data->player->username);
        $user->setTeam($data->player->team);
        $user->setCatched($data->player->catched);
        $user->setDiscovered($data->player->discovered);
        $user->setKm($data->player->km);
        $user->setLevel($data->player->level);
        $user->setXp($data->player->xp);
        $user->setEvolved($data->player->evolved);
        $user->setNextLevel($data->player->nextLevel);
        $user->setPrevLevel($data->player->prevLevel);
        $user->setSign(new \DateTime('@'.$data->player->sign));
        $user->setUpdate(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        // Update Pokedex
        $pokedex = $this->em->getRepository('AppBundle:Pokedex')->findBy(array(
            'user' => $user
        ));

        foreach($pokedex as $pokemon) {
            $this->em->remove($pokemon);
        }

        $this->em->flush();

        $listPokemon = array();

        foreach($data->pokedex as $pokemon) {
            $pokedex = new Pokedex();
            $pokedex->setUser($user);
            $pokedex->setCp($pokemon->cp);
            $pokedex->setPokeball($pokemon->pokeball);

            if(!isset($listPokemon[$pokemon->id])) {
                $monster = $this->em->getRepository('AppBundle:Pokemon')->find($pokemon->id);

                if(!$monster) {
                    continue;
                }

                $listPokemon[$pokemon->id] = $monster;
            }

            $pokedex->setPokemon($listPokemon[$pokemon->id]);

            $this->em->persist($pokedex);
        }

        $this->em->flush();

        return $user;
    }
}

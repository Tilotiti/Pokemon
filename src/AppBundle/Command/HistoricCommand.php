<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 12:38
 */

namespace AppBundle\Command;

use AppBundle\Entity\Historic;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HistoricCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('historic:snapshot')
            ->setDescription('Snapshot all the scores for each user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $db = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $listUser = $db->getRepository('AppBundle:User')->findAll();

        foreach($listUser as $user) {
            if($user->getLastUpdate() < new \DateTime('-1 day')) {
                continue;
            }

            $historic = new Historic();
            $historic->setUser($user);
            $historic->setLevel($user->getLevel());
            $historic->setXp($user->getXp());
            $historic->setKm($user->getKm());
            $historic->setDiscovered($user->getDiscovered());
            $historic->setCatched($user->getCatched());
            $historic->setEvolved($user->getEvolved());
            $historic->setPokedex(count($user->getPokedex()));

            $db->persist($historic);
        }

        $db->flush();
    }
}

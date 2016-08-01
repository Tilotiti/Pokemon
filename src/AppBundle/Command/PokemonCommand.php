<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 12:38
 */

namespace AppBundle\Command;

use AppBundle\Entity\Pokemon;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PokemonCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('pokemon:import')
            ->setDescription('Importe les Pokemons depuis le fichier pokemon.json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        foreach($db->getRepository('AppBundle:Pokemon')->findAll() as $pokemon) {
            $db->remove($pokemon);
        }

        $db->flush();

        $json = json_decode(file_get_contents(__DIR__.'/pokemon.json'));

        foreach($json->pokemon as $item) {
            $pokemon = new Pokemon();

            $pokemon->setId($item->id);
            $pokemon->setName($item->name);
            $pokemon->setImage($item->img);
            $pokemon->setType($item->type);

            $db->persist($pokemon);
        }

        $db->flush();
    }
}

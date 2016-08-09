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

        $json = json_decode(file_get_contents(__DIR__.'/pokemon.json'));

        $db = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        foreach($json as $item) {

            // Search Pokemon
            $pokemon = $db->getRepository('AppBundle:Pokemon')->find($item->id);

            if(!$pokemon) {
                $pokemon = new Pokemon();
            }

            // Update Pokemon
            $pokemon->setId($item->id);
            $pokemon->setNameFR($item->name_fr);
            $pokemon->setNameEN($item->name_en);
            $pokemon->setType($item->type);

            if(!file_exists(__DIR__.'/../../../web/images/pokemon/'.$pokemon->getId().'.png')) {
                $image = file_get_contents($item->img);

                if(file_put_contents(__DIR__.'/../../../web/images/pokemon/'.$pokemon->getId().'.png', $image)) {
                    $pokemon->setImage('/images/pokemon/'.$pokemon->getId().'.png');
                }
            } else {
                $pokemon->setImage('/images/pokemon/'.$pokemon->getId().'.png');
            }

            $db->persist($pokemon);
        }

        $db->flush();
    }
}

<?php

namespace App\Command;

use App\Entity\Generation;
use App\Entity\Pokemon;
use App\Entity\Type;
use App\Repository\GenerationRepository;
use App\Repository\PokemonRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// Import ETL : PokéAPI -> base Pokémon.
// À coller dans src/Command/ImportPokemonCommand.php, puis :
//   symfony console app:import-pokemon          (151 Pokémon au hasard)
//   symfony console app:import-pokemon 50       (50 Pokémon au hasard)
#[AsCommand(name: 'app:import-pokemon',
    description: 'Importe des Pokémon aléatoires depuis PokéAPI')]
class ImportPokemonCommand
{
    private const URL_LISTE  = 'https://pokeapi.co/api/v2/pokemon?limit=100000';
    private const URL_DETAIL = 'https://pokeapi.co/api/v2/pokemon/%d/';

    // Types et générations déjà rencontrés pendant CET import (pas encore en base)
    private array $typesVus = [];
    private array $generationsVues = [];

    public function __construct(
        private HttpClientInterface $client,
        private EntityManagerInterface $em,
        private PokemonRepository $pokemons,
        private TypeRepository $types,
        private GenerationRepository $generations,
    ) {}

    public function __invoke(
        OutputInterface $output,
        #[Argument('Nombre de Pokémon à importer')] int $nombre = 151,
    ): int {
        // E1 - EXTRAIRE : la liste de tous les Pokémon, pour récupérer les ids
        $tousLesIds = $this->extraireIds();

        // E1 bis : on tire $nombre ids au hasard (sans doublon)
        shuffle($tousLesIds);
        $idsTires = array_slice($tousLesIds, 0, $nombre);

        $crees = 0;
        foreach ($idsTires as $id) {
            // E2 - EXTRAIRE : le détail d'un seul Pokémon
            $donnees = $this->client
                ->request('GET', sprintf(self::URL_DETAIL, $id))
                ->toArray();

            // T - règle 1 : sans nom ou sans type, le Pokémon est inutilisable
            if (empty($donnees['name']) || empty($donnees['types'])) {
                continue;
            }

            // T - règle 2 : clé naturelle (pokemonId) déjà en base = déjà importé
            if ($this->pokemons->findOneBy(['pokemonId' => $donnees['id']])) {
                continue;
            }

            // T - mapping : champ de la source -> propriété de l'entité
            $pokemon = (new Pokemon())
                ->setPokemonId($donnees['id'])
                // T5 - nom en minuscules, tronqué à 255 caractères
                ->setName(mb_substr(mb_strtolower(trim($donnees['name'])), 0, 255))
                ->setHeight($donnees['height'] ?? null)
                ->setWeight($donnees['weight'] ?? null)
                ->setOriginalForm((bool) ($donnees['is_default'] ?? false))
                // T4 - la génération se déduit de l'id (PokéAPI ne la donne pas ici)
                ->setGeneration($this->trouverOuCreerGeneration(
                    $this->numeroGeneration($donnees['id'])
                ));

            // T3 - chaque type devient une entité Type, sans doublon
            foreach ($donnees['types'] as $t) {
                $pokemon->addType($this->trouverOuCreerType($t['type']['name']));
            }

            // L - CHARGER : on prépare l'insertion...
            $this->em->persist($pokemon);
            $crees++;
        }
        // L - ...et on écrit tout d'un coup, à la fin
        $this->em->flush();
        $output->writeln("$crees Pokémon importés.");
        return Command::SUCCESS;
    }

    // E1 - tous les ids, sans les formes alternatives (ids 10001 et plus)
    private function extraireIds(): array
    {
        $resultats = $this->client
            ->request('GET', self::URL_LISTE)
            ->toArray()['results'];

        $ids = array_map(
            fn ($p) => (int) basename(rtrim($p['url'], '/')),
            $resultats
        );

        return array_values(array_filter($ids, fn ($id) => $id < 10000));
    }

    // T4 - numéro de génération en dur, selon la tranche d'id
    private function numeroGeneration(int $id): int
    {
        return match (true) {
            $id <= 151 => 1,
            $id <= 251 => 2,
            $id <= 386 => 3,
            $id <= 493 => 4,
            $id <= 649 => 5,
            $id <= 721 => 6,
            $id <= 809 => 7,
            $id <= 905 => 8,
            default    => 9,
        };
    }

    // T3 - le nom du type (texte) devient une entité Type, sans doublon
    private function trouverOuCreerType(string $nomBrut): Type
    {
        $nom = mb_substr(mb_strtolower(trim($nomBrut)), 0, 255);
        if (isset($this->typesVus[$nom])) {
            return $this->typesVus[$nom];          // déjà vu dans CET import
        }
        $type = $this->types->findOneBy(['name' => $nom]);
        if ($type === null) {
            $type = (new Type())->setName($nom);
            $this->em->persist($type);
        }
        return $this->typesVus[$nom] = $type;
    }

    // T4 - même principe pour les générations, retrouvées par leur numéro
    private function trouverOuCreerGeneration(int $numero): Generation
    {
        if (isset($this->generationsVues[$numero])) {
            return $this->generationsVues[$numero];
        }
        $generation = $this->generations->findOneBy(['number' => $numero]);
        if ($generation === null) {
            $generation = (new Generation())->setNumber($numero);
            $this->em->persist($generation);
        }
        return $this->generationsVues[$numero] = $generation;
    }
}
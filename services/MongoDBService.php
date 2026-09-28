<?php

class MongoDBService
{
    private MongoDB\Driver\Manager $manager;
    private string $databaseName = 'easyrdv_nosql';
    private string $collectionName = 'statistiques_rendez_vous';

    public function __construct()
    {
        $this->manager = new MongoDB\Driver\Manager(
            'mongodb://localhost:27017'
        );
    }

    public function testerConnexion(): bool
    {
        try {
            $command = new MongoDB\Driver\Command([
                'ping' => 1
            ]);

            $this->manager->executeCommand(
                $this->databaseName,
                $command
            );

            return true;

        } catch (Throwable $e) {
            return false;
        }
    }

    public function enregistrerStatistiquesPrestations(
        array $statistiques
    ): bool {
        try {
            $bulk = new MongoDB\Driver\BulkWrite();

            // On remplace les anciennes statistiques
            // afin d'éviter les doublons.
            $bulk->delete([]);

            foreach ($statistiques as $statistique) {

                $bulk->insert([
                    'prestation' =>
                        $statistique['prestation'],

                    'nombre_reservations' =>
                        (int) $statistique['nombre_reservations'],

                    'date_mise_a_jour' =>
                        new MongoDB\BSON\UTCDateTime()
                ]);
            }

            $this->manager->executeBulkWrite(
                $this->databaseName
                . '.'
                . $this->collectionName,
                $bulk
            );

            return true;

        } catch (Throwable $e) {
            return false;
        }
    }

    public function obtenirStatistiquesPrestations(): array
    {
        try {
            $query = new MongoDB\Driver\Query(
                [],
                [
                    'sort' => [
                        'nombre_reservations' => -1
                    ]
                ]
            );

            $cursor = $this->manager->executeQuery(
                $this->databaseName
                . '.'
                . $this->collectionName,
                $query
            );

            $statistiques = [];

            foreach ($cursor as $document) {
                $statistiques[] = [
                    'prestation' =>
                        $document->prestation,

                    'nombre_reservations' =>
                        (int) $document->nombre_reservations
                ];
            }

            return $statistiques;

        } catch (Throwable $e) {
            return [];
        }
    }
}
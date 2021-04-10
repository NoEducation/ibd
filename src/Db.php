<?php

namespace Ibd;

/**
 * Klasa obsługująca połączenie z bazą danych MySQL.
 *
 */
class Db
{
    /**
     * Dane dostępowe do bazy.
     */
    private string $dbLogin = 'root';
    private string $dbPassword = '';
    private string $dbHost = 'localhost';
    private string $dbName = 'ibd';

    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO("mysql:host={$this->dbHost};dbname={$this->dbName}", $this->dbLogin, $this->dbPassword);
        $this->pdo->query("SET NAMES utf8");
    }

    /**
     * Wykonuje podane zapytanie i zwraca wynik w postaci talicy.
     *
     * @param            $sql    string Zapytanie SQL
     * @param array|null $params Tablica z parametrami zapytania
     * @return array Tablica z danymi, false jeśl nie udało się wysłać zapytania
     */
    public function pobierzWszystko(string $sql, ?array $params = null): ?array
    {
        $stmt = $this->pdo->prepare($sql);

        if (!empty($params) && is_array($params)) {
            foreach ($params as $k => $v)
                $stmt->bindParam($k, $v);
        }

        return $stmt->execute() ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : null;
    }

    /**
     * Pobiera rekord o podanym ID z wybranej tabeli.
     *
     * @param string  $table
     * @param integer $id
     * @param string $idName
     * @return array
     */
    public function pobierz(string $sql, int $id, string $idName): ?array
    {
        $sql = $sql . " AND $idName = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]) ? $stmt->fetch(\PDO::FETCH_ASSOC) : null;
    }
}

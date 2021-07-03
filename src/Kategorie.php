<?php

namespace Ibd;

class Kategorie
{
    /**
     * Instancja klasy obsługującej połączenie do bazy.
     *
     * @var Db
     */
    private Db $db;

    public function __construct()
    {
        $this->db = new Db();
    }

    public function pobierzSelect(): string
    {
        return "SELECT * FROM kategorie k";
    }

    /**
     * Pobiera wszystkie kategorie.
     *
     * @return array
     */
    public function pobierzWszystkie(): array
    {
        $sql = "SELECT * FROM kategorie k";

        return $this->db->pobierzWszystko($sql);
    }

    public function pobierzZapytanie(array $params = []): array
    {
        $parametry = [];
        $sql = $this->pobierzSelect();

        // dodawanie warunków do zapytanie
        if (!empty($params['nazwa'])) {
            $sql .= " AND (k.nazwa LIKE :nazwa)";

            $parametry['nazwa'] = "%$params[nazwa]%";
        }

        // dodawanie sortowania
        if (!empty($params['sortowanie'])) {
            $kolumny = ['k.nazwa'];
            $kierunki = ['ASC', 'DESC'];
            [$kolumna, $kierunek] = explode(' ', $params['sortowanie']);

            if (in_array($kolumna, $kolumny) && in_array($kierunek, $kierunki)) {
                $sql .= " ORDER BY " . $params['sortowanie'];
            }
        }

        return ['sql' => $sql, 'parametry' => $parametry];
    }

    /**
     * Pobiera stronę z danymi książek.
     *
     * @param string $select
     * @param array  $params
     * @return array
     */
    public function pobierzStrone(string $select, array $params = []): array
    {
        return $this->db->pobierzWszystko($select, $params);
    }
    /**
     * Wykonuje podane w parametrze zapytanie SELECT.
     *
     * @param string $select
     * @return array
     */
    public function pobierzWszystko(string $select): array
    {
        return $this->db->pobierzWszystko($select);
    }

    /**
     * Pobiera dane autora o podanym id.
     *
     * @param int $id
     * @return array
     */
    public function pobierz(int $id): array
    {
        $result = $this->db->pobierzWszystko("select * from kategorie where id =". $id);

        return $result[0];
    }

    /**
     * Dodaje autora.
     *
     * @param array $dane
     * @return int
     */
    public function dodaj(array $dane): int
    {
        return $this->db->dodaj('kategorie', [
            'nazwa' => $dane['nazwa'],
        ]);
    }

    /**
     * Usuwa autora.
     *
     * @param int $id
     * @return bool
     */
    public function usun(int $id): bool
    {
        $sql = "SELECT COUNT(1) FROM ksiazki k where k.id_kategorii = " . $id;
        $result = $this->pobierzWszystko($sql);

        if($result[0]["COUNT(1)"] != '0')
            return false;

        return $this->db->usun('kategorie', $id);
    }

    /**
     *
     * @param array $dane
     * @param int   $id
     * @return bool
     */
    public function edytuj(array $dane, int $id): bool
    {
        $update = [
            'nazwa' => $dane['nazwa'],
        ];

        return $this->db->aktualizuj('kategorie', $update, $id);
    }

}

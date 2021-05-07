<?php

namespace Ibd;

class Ksiazki
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

    /**
     * Pobiera dane książki o podanym id.
     *
     * @param int $id
     * @return array
     */
    public function pobierz(int $id): ?array
    {
        $sql = "SELECT 
                k.id ,
                k.id_autora,
                k.id_kategorii,
                k.tytul,
                k.zdjecie,
                k.opis,
                k.cena,
                k.liczba_stron,
                k.isbn,
                a.imie,
                a.nazwisko,
                kk.nazwa
                FROM ksiazki k 
                INNER JOIN autorzy a on k.id_autora = a.id
                INNER JOIN kategorie kk on kk.id = k.id_kategorii
                WHERE 1=1";

		return $this->db->pobierz($sql, $id, "k.id");
	}

	/**
	 * Pobiera najlepiej sprzedające się książki.
	 * 
	 */
	public function pobierzBestsellery(): ?array
	{
		$sql = "SELECT 
                k.id ,
                k.tytul,
                k.zdjecie,
                a.imie,
                a.nazwisko
                FROM ksiazki k 
                INNER JOIN autorzy a on k.id_autora = a.id
                ORDER BY RAND() LIMIT 5";

        $result = $this->db->pobierzWszystko($sql);
        return $result;
	}
    /**
     * Pobiera zapytanie SELECT oraz jego parametry;
     *
     * @param array $params
     * @return array
     */
    public function pobierzZapytanie(array $params = []): array
    {
        $parametry = [];
        $sql = "SELECT 
                k.id ,
                k.id_autora,
                k.id_kategorii,
                k.tytul,
                k.zdjecie,
                k.opis,
                k.cena,
                k.liczba_stron,
                k.isbn,
                a.imie,
                a.nazwisko,
                kk.nazwa
                FROM ksiazki k 
                INNER JOIN autorzy a on k.id_autora = a.id
                INNER JOIN kategorie kk on kk.id = k.id_kategorii WHERE 1=1 ";

        // dodawanie warunków do zapytanie
        if (!empty($params['fraza'])) {
            $sql .= "AND (k.tytul LIKE :fraza 
                OR k.opis LIKE :fraza 
                OR (a.imie || ' ' || a.nazwisko) LIKE :fraza )";

            $parametry['fraza'] = "%$params[fraza]%";
        }
        if (!empty($params['id_kategorii'])) {
            $sql .= "AND k.id_kategorii = :id_kategorii ";
            $parametry['id_kategorii'] = $params['id_kategorii'];
        }

        // dodawanie sortowania
        if (!empty($params['sortowanie'])) {
            $kolumny = ['k.tytul', 'k.cena', 'a.nazwisko'];
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
}

<?php

namespace Ibd;

class Autorzy
{
	private Db $db;

	public function __construct()
	{
		$this->db = new Db();
	}

	/**
	 * Pobiera zapytanie SELECT z autorami.
	 *
	 * @return string
     */
	public function pobierzSelect(): string
    {
        return "SELECT *, (SELECT COUNT(1) FROM ksiazki kk where kk.id_autora = a.id) `ilosc` FROM autorzy a WHERE 1=1 ";
	}

    public function pobierzZapytanie(array $params = []): array
    {
        $parametry = [];
        $sql = $this->pobierzSelect();

        // dodawanie warunków do zapytanie
        if (!empty($params['nazwisko'])) {
            $sql .= " AND (a.nazwisko LIKE :nazwisko)";

            $parametry['nazwisko'] = "%$params[nazwisko]%";
        }
        if (!empty($params['imie'])) {
            $sql .= " AND a.imie LIKE :imie ";
            $parametry['imie'] = "%$params[imie]%";
        }

        // dodawanie sortowania
        if (!empty($params['sortowanie'])) {
            $kolumny = ['a.imie', 'a.nazwisko'];
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
        $result = $this->db->pobierzWszystko("select * from autorzy where id =". $id);

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
		return $this->db->dodaj('autorzy', [
			'imie' => $dane['imie'],
			'nazwisko' => $dane['nazwisko']
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
        $sql = "SELECT COUNT(1) FROM ksiazki k where k.id_autora = " . $id;
        $result = $this->pobierzWszystko($sql);

        if($result[0]["COUNT(1)"] != '0')
            return false;

		return $this->db->usun('autorzy', $id);
	}

	/**
	 * Zmienia dane autora.
	 * 
	 * @param array $dane
	 * @param int   $id
	 * @return bool
	 */
	public function edytuj(array $dane, int $id): bool
    {
		$update = [
			'imie' => $dane['imie'],
			'nazwisko' => $dane['nazwisko']
		];
		
		return $this->db->aktualizuj('autorzy', $update, $id);
	}

}

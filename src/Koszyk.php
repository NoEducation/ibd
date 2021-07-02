<?php

namespace Ibd;

class Koszyk
{
	/**
	 * Instancja klasy obsługującej połączenie do bazy.
	 *
	 */
	private Db $db;

	public function __construct()
	{
		$this->db = new Db();
	}

	/**
	 * Pobiera dane książek w koszyku.
	 *
	 * @return array
	 */
	public function pobierzWszystkie(): array
    {
        $sessionId = session_id();
		$sql = "
			SELECT ks.*, ko.liczba_sztuk, ko.id AS id_koszyka
			FROM ksiazki ks JOIN koszyk ko ON ks.id = ko.id_ksiazki
			WHERE ko.id_sesji = '" . session_id() . "'
			ORDER BY ko.data_dodania DESC";

		return $this->db->pobierzWszystko($sql);
	}

	public function policzIloscElemetow(): int {
	    $sessionId = session_id();
	    $sql = "SELECT COUNT(1) FROM `koszyk` AS ko WHERE ko.id_sesji = '" . session_id() . "';";
        $result =  $this->db->pobierzWszystko($sql);
        return $result[0]["COUNT(1)"];
    }

	/**
	 * Dodaje książkę do koszyka.
	 *
	 * @param int    $idKsiazki
	 * @param string $idSesji
	 * @return int
	 */
	public function dodaj(int $idKsiazki, string $idSesji): int
    {
		$dane = [
			'id_ksiazki' => $idKsiazki,
			'id_sesji' => $idSesji
		];

		return $this->db->dodaj('koszyk', $dane);
	}

	/**
	 * Sprawdza, czy podana książka znajduje się w koszyku.
	 *
	 * @param int    $idKsiazki
	 * @param string $idSesji
	 * @return bool
	 */
	public function czyIstnieje(int $idKsiazki, string $idSesji): bool
    {
		$sql = "SELECT * FROM koszyk WHERE id_sesji = '$idSesji' AND id_ksiazki = :id_ksiazki";
		$ile = $this->db->policzRekordy($sql, [':id_ksiazki' => $idKsiazki]);
		
		return $ile > 0;
	}

	/**
	 * Zmienia (usuwa) ilości sztuk książek w koszyku.
	 *
	 * @param array $dane Tablica z danymi (klucz to id rekordu w koszyku, wartość to liczba sztuk)
	 */
	public function zmienLiczbeSztuk(array $dane): void
	{
		foreach ($dane as $idKoszyka => $ilosc) {
			if ($ilosc <= 0) {
                $this->db->usun('koszyk', $idKoszyka);
            } else {
                $this->db->aktualizuj('koszyk', ['liczba_sztuk' => $ilosc], $idKoszyka);
            }
		}
	}

    /**
     * Czyści koszyk.
     *
     * @param string $idSesji
     * @return bool
     */
    public function wyczysc(string $idSesji): bool
    {
        return $this->db->wykonaj("DELETE FROM koszyk WHERE id_sesji = :id_sesji", ['id_sesji' => $idSesji]);
    }
	public function pobierzKsiazkeWKoszyku(int $id, string $idSesji): ?array{
        $sql = "SELECT k.id, k.liczba_sztuk FROM koszyk AS K WHERE k.id_sesji = '$idSesji'";
        $result = $this->db->pobierz($sql, $id, "k.id_ksiazki");

        return  $result;
    }

    public function zaktualizuj(int $liczba_sztuk, int $id): int
    {
        $dane = [
            'liczba_sztuk' =>$liczba_sztuk + 1
        ];

        return $this->db->aktualizuj('koszyk', $dane, $id);
    }

}

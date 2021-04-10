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
	 * Pobiera wszystkie książki.
	 *
	 * @return array
	 */
	public function pobierzWszystkie(): ?array
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
                INNER JOIN kategorie kk on kk.id = k.id_kategorii";
        $result = $this->db->pobierzWszystko($sql);
		return $result;
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
}

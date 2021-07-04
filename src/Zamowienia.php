<?php

namespace Ibd;

class Zamowienia
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
     * Dodaje zamówienie.
     * 
     * @param int $idUzytkownika
     * @return int Id zamówienia
     */
    public function dodaj(int $idUzytkownika): int
    {
        return $this->db->dodaj('zamowienia', [
            'id_uzytkownika' => $idUzytkownika,
            'id_statusu' => 1
        ]);
    }

    public function getSelect(string $whereClause = " "): string{
        return "SELECT z.*, u.login, s.nazwa AS status,
			ROUND(SUM(sz.cena*sz.liczba_sztuk), 2) AS suma,
			COUNT(sz.id) AS liczba_produktow,
			SUM(sz.liczba_sztuk) AS liczba_sztuk
			FROM zamowienia z JOIN uzytkownicy u ON z.id_uzytkownika = u.id
			JOIN zamowienia_statusy s ON z.id_statusu = s.id
			JOIN zamowienia_szczegoly sz ON z.id = sz.id_zamowienia"
            . $whereClause .
            "GROUP BY z.id";
    }

    /**
     * Dodaje szczegóły zamówienia.
     * 
     * @param int   $idZamowienia
     * @param array $dane Książki do zamówienia
     */
    public function dodajSzczegoly(int $idZamowienia, array $dane): void
    {
        foreach ($dane as $ksiazka) {
            $this->db->dodaj('zamowienia_szczegoly', [
                'id_zamowienia' => $idZamowienia,
                'id_ksiazki' => $ksiazka['id'],
                'cena' => $ksiazka['cena'],
                'liczba_sztuk' => $ksiazka['liczba_sztuk']
            ]);
        }
    }

    /**
     * Pobiera wszystkie zamówienia.
     *
     * @return array
     */
    public function pobierzWszystkie(): array
    {
        $sql = $this->getSelect();

        return $this->db->pobierzWszystko($sql);
    }

    public function getById(int $id){
        $whereClause = " WHERE Z.id = " . $id . " ";
        $sql = $this->getSelect($whereClause);
        $result = $this->db->pobierzWszystko($sql);

        return $result[0];
    }

    public function getStatuses(){
        $sql = "SELECT * FROM `zamowienia_statusy`";
        return $this->db->pobierzWszystko($sql);
    }

    public function changeStatus(array $dane, int $id){
        $result = $this->db->wykonaj("UPDATE zamowienia SET id_statusu = :id_status WHERE id = :id;", ['id_status' => $dane['status'], 'id' => $id]);
        return $result;
    }
}

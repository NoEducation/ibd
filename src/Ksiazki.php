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
        $sql = "
			SELECT k.*, CONCAT(a.imie, ' ', a.nazwisko) AS autor, kat.nazwa AS kategoria
			FROM ksiazki k 
			JOIN autorzy a ON k.id_autora = a.id
			JOIN kategorie kat ON k.id_kategorii = kat.id
        ";

        return $this->db->pobierzWszystko($sql);
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

    /**
     * Dodaje książkę do bazy.
     *
     * @param array $dane
     * @param array $pliki Dane wgrywanego pliku z okładką
     * @return int
     */
    public function dodaj(array $dane, array $pliki): int
    {
        $id = $this->db->dodaj('ksiazki', [
            'id_autora' => $dane['id_autora'],
            'id_kategorii' => $dane['id_kategorii'],
            'tytul' => $dane['tytul'],
            'opis' => $dane['opis'],
            'cena' => $dane['cena'],
            'liczba_stron' => $dane['liczba_stron'],
            'isbn' => $dane['isbn']
        ]);

        $rozszerzenie = strtolower(pathinfo($pliki['zdjecie']['name'], PATHINFO_EXTENSION));

        if (!empty($pliki['zdjecie']['name']) && $rozszerzenie == 'jpg') {
            // zostal wybrany plik ze zdjeciem do uploadu
            if($this->wgrajPlik($pliki, $id)) {
                $this->db->aktualizuj('ksiazki', ['zdjecie' => "$id.jpg"], $id);
            }
        }

        return $id;
    }

    /**
     * Wgrywa plik ze zdjęciem na serwer.
     *
     * @param array $pliki
     * @param int   $idKsiazki
     * @return bool
     */
    public function wgrajPlik(array $pliki, int $idKsiazki): bool
    {
        $nazwa = $idKsiazki . "_org.jpg";

        if (move_uploaded_file($pliki['zdjecie']['tmp_name'], "zdjecia/$nazwa")) {
            $this->stworzMiniature($nazwa, $idKsiazki);
            return true;
        }

        return false;
    }

    /**
     * Tworzy miniaturę wgrywanego zdjęcia.
     *
     * @param string $nazwa
     * @param int    $idKsiazki
     * @param int    $szerokosc
     */
    public function stworzMiniature(string $nazwa, int $idKsiazki, int $szerokosc = 100)
    {
        $img = imagecreatefromjpeg("zdjecia/$nazwa");
        $width = imagesx($img);
        $height = imagesy($img);
        $newWidth = $szerokosc;
        $newHeight = floor($height * ( $szerokosc / $width ));

        $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($tmpImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($tmpImg, "zdjecia/$idKsiazki.jpg");
    }

    /**
     * Zmienia dane książki.
     *
     * @param array $dane
     * @param int   $id
     * @param array $pliki
     * @return bool
     */
    public function edytuj(array $dane, int $id, array $pliki): bool
    {
        $update = [
            'id_autora' => $dane['id_autora'],
            'id_kategorii' => $dane['id_kategorii'],
            'tytul' => $dane['tytul'],
            'opis' => $dane['opis'],
            'cena' => $dane['cena'],
            'liczba_stron' => $dane['liczba_stron'],
            'isbn' => $dane['isbn']
        ];

        $rozszerzenie = strtolower(pathinfo($pliki['zdjecie']['name'], PATHINFO_EXTENSION));

        if (!empty($pliki['zdjecie']['name']) && $rozszerzenie == 'jpg') {
            // zostal wybrany plik ze zdjeciem do uploadu
            if ($this->wgrajPlik($pliki, $id)) {
                $update['zdjecie'] = "$id.jpg";
            }
        }

        return $this->db->aktualizuj('ksiazki', $update, $id);
    }

    /**
     * Usuwa książkę.
     *
     * @param int $id
     * @return bool
     */
    public function usun(int $id): bool
    {
        if(file_exists("zdjecia/$id.jpg")) {
            unlink("zdjecia/$id.jpg");
        }
        if (file_exists("zdjecia/" . $id . "_org.jpg")) {
            unlink("zdjecia/" . $id . "_org.jpg");
        }

        return $this->db->usun('ksiazki', $id);
    }
}

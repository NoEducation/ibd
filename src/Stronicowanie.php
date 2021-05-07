<?php

namespace Ibd;

class Stronicowanie
{
    /**
     * Instancja klasy obsługującej połączenie do bazy.
     *
     * @var Db
     */
    private $db;

    /**
     * Liczba rekordów wyświetlanych na stronie.
     *
     * @var int
     */
    private $naStronie = 5;

    /**
     * Aktualnie wybrana strona.
     *
     * @var int
     */
    private $strona = 0;

    /**
     * Ilosc stron.
     *
     * @var int
     */
    private $iloscRekordow = 0;
    /**
     * Dodatkowe parametry przekazywane w pasku adresu (metodą GET).
     *
     * @var array
     */
    private $parametryGet = [];

    /**
     * Parametry przekazywane do zapytania SQL.
     *
     * @var array
     */
    private $parametryZapytania;


    public function __construct($parametryGet, $parametryZapytania)
    {
        $this->db = new Db();
        $this->parametryGet = $parametryGet;
        $this->parametryZapytania = $parametryZapytania;

        if (!empty($parametryGet['strona'])) {
            $this->strona = (int)$parametryGet['strona'];
        }
    }

    /**
     * Dodaje do zapytania SELECT klauzulę LIMIT.
     *
     * @param string $select
     * @return string
     */
    public function dodajLimit(string $select): string
    {
        return sprintf('%s LIMIT %d, %d', $select, $this->strona * $this->naStronie, $this->naStronie);
    }

    /**
     * Generuje linki do wszystkich podstron.
     *
     * @param string $select Zapytanie SELECT
     * @param string $plik Nazwa pliku, do którego będą kierować linki
     * @return string
     */
    public function pobierzLinki(string $select, string $plik): string
    {
        $rekordow = $this->db->policzRekordy($select, $this->parametryZapytania);
        $this->iloscRekordow =  $rekordow;
        $liczbaStron = ceil($rekordow / $this->naStronie);
        $parametry = $this->_przetworzParametry();

        $linki = "<nav><ul class='pagination'>";
        for ($i = 0; $i < $liczbaStron; $i++) {

            if($i == 0 && $this->strona != 0){
                $linki .= sprintf(
                    "<li class='page-item'><a href='%s?%s&strona=%d' class='page-link'>%s</a></li>",
                    $plik,
                    $parametry,
                    0,
                   'Początek');
            }

            if($i == 0 && $this->strona != 0){
                $linki .= sprintf(
                    "<li class='page-item'><a href='%s?%s&strona=%d' class='page-link'>%s</a></li>",
                    $plik,
                    $parametry,
                    $this->strona - 1,
                    'Poprzednia');
            }

            if ($i == $this->strona) {
                $linki .= sprintf("<li class='page-item active'><a class='page-link'>%d</a></li>", $i + 1);
            } else {
                $linki .= sprintf(
                    "<li class='page-item'><a href='%s?%s&strona=%d' class='page-link'>%d</a></li>",
                    $plik,
                    $parametry,
                    $i,
                    $i + 1
                );
            }

            if($i == $liczbaStron - 1 && $this->strona != $liczbaStron - 1){
                $linki .= sprintf(
                    "<li class='page-item'><a href='%s?%s&strona=%d' class='page-link'>%s</a></li>",
                    $plik,
                    $parametry,
                    $this->strona + 1,
                    'Następna');
            }

            if($i == $liczbaStron - 1 && $this->strona != $liczbaStron - 1){
                $linki .= sprintf(
                    "<li class='page-item'><a href='%s?%s&strona=%d' class='page-link'>%s</a></li>",
                    $plik,
                    $parametry,
                    $liczbaStron - 1,
                    'Koniec');
            }
        }
        $linki .= "</ul></nav>";

        return $linki;
    }

    public function getStrona(){

        return $this -> strona;
    }

    public function getnaStronie(){
        return $this -> naStronie;
    }

    public function getIloscRekordow(){
        return $this->iloscRekordow;
    }

    /**
     * Przetwarza parametry wyszukiwania.
     * Wyrzuca zbędne elementy i tworzy gotowy do wstawienia w linku zestaw parametrów.
     *
     * @return string
     */
    private function _przetworzParametry(): string
    {
        $temp = [];
        $usun = ['szukaj', 'strona'];
        foreach ($this->parametryGet as $kl => $wart) {
            if (!in_array($kl, $usun))
                $temp[] = "$kl=$wart";
        }

        return implode('&', $temp);
    }
}

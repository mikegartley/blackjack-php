<?php

class BlackJack extends Deck
{
    public $player = array();
    public $dealer = array();

    public function BlackJack()
    {
        $this->buildDeck();

        for($c = 0; $c <= 2; $c++)
        {
            shuffle($this->DECKOFCARDS);
        }
    }
    public function dealDeck()
    {
        return array_pop($this->DECKOFCARDS);
    }

    public function whoWon($pVal, $dVal, $stand)
    {
        if($pVal > 21)
        {
            return 4;
        }
        else if($dVal > 21)
        {
            return 1;
        }
        else if($pVal == $dVal)
        {
            return 6;
        }
        else if($stand == 1){
            
            if($pVal > $dVal)
            {
                if ($pVal == 21)
                {
                    return 2;
                }
                else
                {
                    return 3;
                }
                
            }
            else
            {
                return 1;
            }
        }
    }
}
class Deck 
{
    public $DECKOFCARDS = array();

    public $cardValue = array("2", "3","4","5","6","7","8","9","10","J","Q","K","A");
    public $cardSuit = array("D","H","S","C");

    public function buildDeck()
    {
        for($int = 0; $int < 13; $int++)
        {
            for($d = 0; $d < 4; $d++)
            {
                array_push($this->DECKOFCARDS, $this->cardValue[$int].$this->cardSuit[$d]);
            }
        }
    }

    public function getValue($cardVal)
    {
        $val = 0;
       if(is_array($cardVal))
       {
            foreach($cardVal as &$vals)
            {     
                $val += $this->getCardVal($vals);
            }
        }
        return $val;
    }

    public function getCardVal($card)
    {
        $faceVal = substr($card, 0, -1);
        $suits = substr($card, -1, 1);
        $pat = '/[0-9]/';
        $fac_pat = '/[JQK]/';

        if(preg_match($pat, $faceVal))
        {
            return $faceVal;
        }
        else if(preg_match($fac_pat, $faceVal))
        {
            return 10;
        }
        else
        {
            return 11;
        }
    }

}
?>
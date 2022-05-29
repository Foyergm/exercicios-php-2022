<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay;


use Galoa\ExerciciosPhp2022\War\GamePlay\Country\CountryInterface;

/**
 *
 */
class Battlefield implements BattlefieldInterface
{
    public function rollDice(CountryInterface $country, bool $isAtacking): array
    {
        $dice = [];
        //Take all the troops in the country to attack but one, so that the country is not defeated.
        //If true, the country attacks, if not, it defends.
        if ($isAtacking){
            $diceTroop = $country->getNumberOfTroops() - 1;
        }
        else{
            $diceTroop = $country->getNumberOfTroops();
        }
        //Take the number of troops from each country in the confrontation, and roll the dice to attack or defend.
        for ($i = 1; $i <= $diceTroop; $i++){
            $dice[] = rand(1, 6);
        }
        //Number of dice for each troop in descending order.
        arsort($dice);
        return $dice;
    }

    public function computeBattle(CountryInterface $attackingCountry, array $attackingDice, CountryInterface $defendingCountry, array $defendingDice): void
    {
        //Counts the number of troops that lost the battle and calculates how many troops the attacker and defender lost.
        if (count($attackingDice) <= count($defendingDice)) {
            $max = count($attackingDice);
        } else {
            $max = count($defendingDice);
        }
        for ($i = 0; $i < $max; $i++) {
            if ($attackingDice[$i] >= $defendingDice[$i]) {
                $defendingCountry->killTroops(1);
            } else {
                $attackingCountry->killTroops(1);
            }
        }
    }
}
<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay\Country;

use Galoa\ExerciciosPhp2022\War\GamePlay\Battlefield;

/**
 * Defines a country, that is also a player.
 */
class BaseCountry implements CountryInterface {

  /**
   * The name of the country.
   *
   * @var string
   */
  protected $name;

    /**
     * Initial troop value.
     *
     * @var int
     */
  protected $troops = 3;

    /**
     * neighbors of a country.
     *
     * @var array
     */
  protected $neighbors = [];

    /**
     * Conquered countries.
     *
     * @var
     */
  protected $conqueredCountries;


    /**
   * Builder.
   *
   * @param string $name
   *   The name of the country.
   */
  public function __construct(string $name) {
    $this->name = $name;

  }

    /**
     * Pulls the name of all countries, playing like this in a fixed order.
     *
     * @return string
     */
    public function getName(): string{
        return $this->name;

    }

    /**
     * Defines the neighbors for each country.
     *
     * Runs only once.
     *
     * @param array $neighbors
     * @return void
     */
    public function setNeighbors(array $neighbors): void{
        foreach ($neighbors as $value){
            array_push($this->neighbors,$value);
        }

    }

    /**
     * Catch the neighbors of the countries.
     *
     * @return array|CountryInterface[]
     */
    public function getNeighbors(): array{
        return $this->neighbors;

    }

    /**
     * Get the number of troops.
     *
     * @return int
     */
    public function getNumberOfTroops(): int{
        return $this->troops;

    }

    /**
     * Call when you need to analyze whether the country has been conquered or not.
     *
     * @return bool
     */
    public function isConquered(): bool{
        //If the country has less than or equal to zero, it means that it was conquered.
        if($this->troops <= 0)
            return true;
        //If not, the country has not been conquered.
        else
            return false;

    }

    /**
     * Call when, a country returns with 0 troops.
     *
     * Here, it registers the neighbor who conquered the country.
     * @param CountryInterface $conqueredCountry
     * @return void
     */
    public function conquer(CountryInterface $conqueredCountry): void{
        //Neighbor who conquered a country.
        $conqueredCountryNeighbors = $conqueredCountry->getNeighbors();
        //Take the name of the conquered country.

        foreach($conqueredCountryNeighbors as $conqueredCountryNeighbor){
            $conqueredCountryNeighborName = $conqueredCountryNeighbor->getName();

            //Take the name of the conquered country and take that territory.
            if(strcasecmp($conqueredCountryNeighborName, $this->name) != 0){
                if(!in_array($conqueredCountryNeighbor, $this->neighbors)){
                    array_push($this->neighbors, $conqueredCountryNeighbor);
                }
                $conqueredCountryNeighbor->upNeighbors($conqueredCountry, $this);
            }
        }
        //Search for conquered country.
        $key = array_search($conqueredCountry, $this->neighbors);
        unset($this->neighbors[$key]);

        //Add to conquered countries.
        $this->conqueredCountries++;

    }
    /**
     * Updates neighbors when a country is conquered.
     *
     * @param CountryInterface $conqueredCountry
     * @param CountryInterface $conquerorCountry
     * @return void
     */
    protected function upNeighbors(CountryInterface $conqueredCountry, CountryInterface $conquerorCountry): void{
        $key = array_search($conqueredCountry, $this->neighbors);
        unset($this->neighbors[$key]);

        if(!in_array($conquerorCountry, $this->neighbors)){
            array_push($this->neighbors, $conquerorCountry);
        }
    }

    /**
     * Adds 3 standard troops for each round, plus 1 troops for each conquered country.
     *
     * @param int $troopsToBeAdded
     * @return void
     */
    public function addTroopsPerRound(int $troopsToBeAdded): void{
        $this->troops = $this->troops + 3 + $this->conqueredCountries;

    }

    /**
     * Kill troops that lost on the roll dice.
     *
     * @param int $killedTroops
     * @return void
     */
    public function killTroops(int $killedTroops): void{
    $this->troops = $this->troops - $killedTroops;

    }
}
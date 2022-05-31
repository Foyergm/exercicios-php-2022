<?php

namespace Galoa\ExerciciosPhp2022\WebScrapping;

use Box\Spout\Common\Entity\Style\CellAlignment;
use DOMXPath;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

    /**
     * Loads paper information from the HTML and creates a XLSX file.
     */
    public function scrap(\DOMDocument $dom): void {

        //A request for information for the HTML file.
        $finder = new DOMXPath($dom);
        $class="col-sm-12 col-md-8 col-lg-8 col-md-pull-4 col-lg-pull-4";
        $nodes = $finder->query("//*[contains(@class, '$class')]");

        //Takes an element from the character 'a'.
        $list = $nodes->item(0)->getElementsByTagName('a');
        $array = [];

        //Search in order the information for each of the objects.
        for ($i = 0; $i < $list->length; $i++){
            $volumeInfo = $finder->query("//div[contains(@class, 'volume-info')]", $list->item($i));
            $array[$i]['ID'] = $volumeInfo->item($i)->nodeValue;
            $array[$i]['Title'] = $list->item($i)->getElementsByTagName('h4')->item(0)->nodeValue;
            $array[$i]['Type'] = $list->item($i)->getElementsByTagName('div')->item(1)->firstChild->nodeValue;
            $authors = $list->item($i)->getElementsByTagName('div')->item(0)->getElementsByTagName('span');

            //Seeks the author's object and the author's institution and leaves them side by side.
            $j = 0;
            foreach ($authors as $author){
                $array[$i]['Author ' . ($j+1)] = trim($author->nodeValue, ';');
                $array[$i]['Author ' . ($j+1) . ' Institution'] = $author->getAttribute('title');
                $j++;
            }

        }

        //Create a style for the header.
        $style = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(10)
            ->build();

        //Compare one variable with the other to avoid repetitions.
        $compareArray = $array;
        usort($compareArray, function($a, $b)
        {
            $a = sizeof($a);
            $b = sizeof($b);
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? 1 : -1;
        });

        //Create an XLSX file and add the header and objects fetched to the HTML file.
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile('webscrapping/origin.xlsx');
        $rowFromValues = WriterEntityFactory::createRowFromArray(array_keys($compareArray[0]), $style);
        $writer->addRow($rowFromValues);
        foreach ($array as $row){
            $rowFromValues = WriterEntityFactory::createRowFromArray($row);
            $writer->addRow($rowFromValues);

        }
        //A função armazena os dados da sessão e termina a sessão.
        $writer->close();
    }


}
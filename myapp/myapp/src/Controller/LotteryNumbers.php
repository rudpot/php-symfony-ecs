<?php
// src/Controller/LotteryNumbers.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LotteryNumbers
{
     /**
      * @Route("/lucky/lottery")
      */
    public function number(): Response
    {
        $numbers = array();
        for ($ii=0; $ii<6; $ii++) {
            $numbers[] = random_int(0, 49);
        }

        return new Response(
            '<html><body>Lucky number: '.implode(" ",$numbers).'</body></html>'
        );
    }
}

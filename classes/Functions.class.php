<?php 

    class Functions 
    {
        function isFutureDate ($dateStr) {
            if (strlen($dateStr) === 7) {
                $dateStr .= "-01";
            } else if (strlen($dateStr) === 4) {
                $dateStr .= "-01-01";
            }
            $startDate = strtotime(date('Y-m-d', strtotime($dateStr) ) );
            $currentDate = strtotime(date('Y-m-d'));
          
            if($startDate > $currentDate) {
                return true;
            }
    
            return false;
        }
    }
    

?>
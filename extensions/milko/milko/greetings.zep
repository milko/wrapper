 namespace Milko;

 class Greetings
 {
     public static function say()
     {
         echo "Ciao bella";
     }

     public function check( var theVariable = null )
     {
         echo "\n[" . theVariable . "]\n";

         //
         // Check NULL.
         //
         if( theVariable === null )
         {
            echo "NULL\n";
         }

         //
         // Check FALSE.
         //
         elseif( theVariable === false )
         {
            echo "FALSE\n";
         }

         //
         // Other.
         //
         else
         {
            echo "Other\n";
         }
     }
 }

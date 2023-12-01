<?php
namespace Cyrex\Commands;
use App\Config\DefaultConfig;
use Meso\Meso;
use Meso\Base;
class DatabaseCommand
{

    public function handle($args)
    {
        if (!file_exists("./Configs/DefaultConfig.php")) {
            echo "Default Config does not exist use doctor command or create one";
            return;
        }
        if (!file_exists("./Resources/Meso/Meso.php") || !file_exists("./Resources/Meso/Base.php")) {
            echo "Meso does not exist use doctor command or create one";
            return;
        }
        require_once("./Configs/DefaultConfig.php");
        require_once("./Resources/Meso/Meso.php");
        require_once("./Resources/Meso/Base.php");
        $Conf = DefaultConfig\RetunConf();
        $Meso = new Meso($Conf->Database->Address, $Conf->Database->User, $Conf->Database->Password, $Conf->Database->Database);
        if (!$Meso->connect()) {
            print_r("Error: Service cannot initialize \n reason: Database Connection Error");
            exit;
        }

        $directory = getcwd().'/Src/Models/Entities';

    // Create the directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

    // Retrieve table names
        $tables = [];
        $result = $Meso->query("SHOW TABLES");

        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }

    // Loop through tables
        foreach ($tables as $table) {
            $className = ucfirst($table) . 'Entity';
            $entityContent = "<?php\nnamespace App\Entities; \n";
            $entityContent .= "class $className {\n";

            // Retrieve column information
            $columns = $Meso->query("DESCRIBE $table");

            while ($column = $columns->fetch_assoc()) {
                $propertyName = $column['Field'];
                $type = $column['Type'];
                $nullable = $column['Null'];
                $default = $column['Default'];

                // Generate comments for properties
                $comment = $column['Comment'] ?? '';
                $entityContent .= "    /**\n";
                $entityContent .= "     * $comment\n";
                $entityContent .= "     * @var $type\n";
                $entityContent .= "     */\n";

                // Add support for default values
                if ($default !== null) {
                    $entityContent .= "    public \$$propertyName = '$default';\n";
                } else {
                    $entityContent .= "    public \$$propertyName;\n";
                }

                // Generate verification functions based on column constraints
                $entityContent .= "    public function set_$propertyName(\$$propertyName) {\n";
                $entityContent .= "        if (\$$propertyName === null) {\n";
                $entityContent .= "            throw new \Exception('Value cannot be null');\n";
                $entityContent .= "        }\n";

                if (strpos($type, 'varchar') !== false) {
                    preg_match('/\((\d+)\)/', $type, $matches);
                    $maxLength = $matches[1];
                    $entityContent .= "        if (strlen(\$$propertyName) > $maxLength) {\n";
                    $entityContent .= "            throw new \Exception('Value exceeds maximum length');\n";
                    $entityContent .= "        }\n";
                }

                // Add more validation based on column constraints here

                $entityContent .= "        // Add more validation based on column constraints here\n";
                $entityContent .= "        \$this->$propertyName = \$$propertyName;\n";
                $entityContent .= "    }\n";
            }

            $entityContent .= "}\n";

            // Save entity class to a file
            $filename = getcwd()."/Src/Models/Entities/$className.php";
            file_put_contents($filename, $entityContent);

            echo "Entity class $className generated and saved to $filename.\n";
        }
    }


}

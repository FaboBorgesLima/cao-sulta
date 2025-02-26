<?php

require __DIR__ . "/../vendor/autoload.php";

use Lib\StringUtils;

class CLI
{
    public const HELP_MESSAGE = "-------------- commands --------------
    create controller <NameController>
    create model <Name>
";
    /**
     * @param array<string,string> $args
     */
    static public function create($args)
    {
        if (!array_key_exists(0, $args) || !array_key_exists(1, $args)) {
            echo CLI::HELP_MESSAGE;
            return;
        }

        $object = $args[0];
        $name = $args[1];

        if (!StringUtils::isUpperCamelCase($name)) {
            throw new Error("Names must be UpperCamelCase");
        }


        switch ($object) {
            case "controller":
                if (!str_ends_with($name, "Controller")) {
                    echo CLI::HELP_MESSAGE;
                    return;
                }
                $path = __DIR__ . "/../app/Controller/$name.php";
                if (file_exists($path)) {
                    echo "app/Controller/$name.php already exits\n";
                    return;
                }
                $file = fopen($path, "w");
                fwrite($file, "<?php

namespace App\Controller;

use Core\Http\Controllers\Controller;
use Core\Http\Response;

class $name extends Controller
{
    public function show(): void
    {
        echo \"$name\";
    }
}
");
                echo "created $name Controller at app/Controller/$name.php\n";
                break;
            case "model":

                $path = __DIR__ . "/../app/Models/$name.php";

                if (file_exists($path)) {
                    echo "app/Models/$name.php already exits\n";
                    return;
                }

                $file = fopen($path, "w");
                $table_name = StringUtils::camelToSnakeCase($name) . "s";
                fwrite($file, "<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;

class $name extends Model
{
    static protected string \$table = \"$table_name\";
    protected static array \$columns = [];

    public function validates(): void
    {
        \$this->addError(\"implementation\", \"must implement\");
    }
}
");
                echo "created $name Model at app/Models/$name.php\n";
                break;
            default:
                echo CLI::HELP_MESSAGE;
        }
    }

    /**
     * @param string[] $argv
     */
    static public function init(array $argv): void
    {
        if (array_key_exists(1, $argv) && method_exists(static::class, $argv[1])) {
            $args = array_splice($argv, 2);

            call_user_func(static::class . "::" . $argv[1], $args);

            return;
        }
        echo CLI::HELP_MESSAGE;
    }
}

CLI::init($argv);

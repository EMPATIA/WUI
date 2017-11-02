<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use File;
use Illuminate\Console\Command;
use ONE;
use Session;

class UsersImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import 
            {filename : Name of the file to import, shoud be in storage/user-import/}
            {entity : Entity Key}
            {--B|birthdayKey= : Key for birthday parameter}
            {--A|auth= : Auth component url with port}
            {--O|orchestrator= : Orchestrator componente url with port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import user csv file';

    protected $auth;
    protected $orchestrator;
    protected $birthdayKey;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() {
        $filename = $this->argument("filename");
        if (File::exists(storage_path() . "/user-import/" . $filename)) {
            // The given file exists
            $entityKey = $this->argument("entity");
            if ($entityKey!=null) {
                // The given entity key is not null (but I don't know if it's valid.)

                Session::set('X-ENTITY-KEY', $entityKey);
                if (is_null($this->option("auth")) && $this->confirm("You don't want to use a custom Auth URL?"))
                    $this->auth = $this->ask("Auth URL (including port):");
                else
                    $this->auth = $this->option("auth") ?? "http://empatia-dev.onesource.pt:5002";

                if (is_null($this->option("orchestrator")) && $this->confirm("You don't want to use a custom Orchestrator URL?"))
                    $this->orchestrator = $this->ask("Orchestrator URL (including port):");
                else
                    $this->orchestrator = $this->option("orchestrator") ?? "http://empatia-dev.onesource.pt:5009";

                $this->birthdayKey = (is_null($this->option("birthdayKey"))) ? $this->ask("Birthday Key:") : $this->option("birthdayKey");

                $keys = [];
                $keysOrig = [];
                if (($handle = fopen(storage_path() . "/user-import/" . $filename, "r")) !== false) {

                    /* Get Header of CSV File */
                    $data = fgetcsv($handle, 1000, ";");
                    if ($data != false) {
                        $num = count($data);
                        for ($c = 0; $c < $num; $c++) {
                            $keys[$c] = mb_strtolower($data[$c]);
                            $keysOrig[$c] = $data[$c];
                        }

                        $errors = 0;
                        $succeded = 0;
                        $progressBar = $this->output->createProgressBar(count(file(storage_path() . "/user-import/" . $filename))-1);
                        /* Get CSV content */
                        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                            $newUser = array();
                            $num = count($data);
                            for ($c = 0; $c < $num; $c++) {
                                switch ($keys[$c]) {
                                    case "name":
                                        $newUser["name"] = $data[$c];
                                        $newUser["email"] = $this->emailSanitize($data[$c]) . "@empaville.org";
                                        break;

                                    case "alfanumeric":
                                        $newUser["password"] = $data[$c];
                                        $newUser["alphanumeric_code"] = $data[$c];
                                        break;

                                    case "rfid":
                                        $newUser["rfid"] = $data[$c];
                                        break;

                                    case "age":
                                        $newUser["parameters"][$this->birthdayKey] = Carbon::now()->subYears($data[$c])->toDateString();
                                        break;

                                    case "birthday":
                                        $newUser["parameters"][$this->birthdayKey] = $data[$c];
                                        break;

                                    default:
                                        $newUser["parameters"][$keysOrig[$c]] = $data[$c];
                                }
                            }
                            $newUser["confirmed"] = 1;
                            if ($this->registerUser($newUser))
                                $succeded++;
                            else
                                $errors++;

                            $progressBar->advance();
                        }
                        $progressBar->finish();

                        $this->error($errors . " users failed to import.");
                        $this->info($succeded . " users successfully imported.");
                        $this->info("Finished import.");
                    } else
                        $this->error("Failed to access file header");
                    fclose($handle);
                } else
                    $this->error("Failed to access file");
            } else
                $this->error("Missing entity");
        } else
            $this->error("File not found");
    }

    private function emailSanitize($email) {
        $email = strtolower(str_replace(" ", "_", trim($email)));

        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
            '/(?![.=$\'€%-])\p{P}/u' => '',
            "#[[:punct:]]#" => '',
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $email);
    }

    private function registerUser($newUser) {
        try {
            $response = ONE::post([
                'url'       => $this->auth,
                'component' => 'auth',
                'api'       => 'auth',
                'params'    => $newUser
            ]);

            if($response->statusCode() == 201) {
                $userKey = $response->json()->user->user_key;

                $response = ONE::post([
                    'url'       => $this->orchestrator,
                    'component' => 'orchestrator',
                    'api'       => 'user',
                    'params' => [
                        'user_key' => $userKey,
                        'entity_key' => ONE::getEntityKey(),
                    ]
                ]);
                return ($response->statusCode() == 201);
            } else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
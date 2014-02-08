<?php

/**
 * Description of admin
 *
 * @author Bill
 */
class admin {

    function index() {

        /*
         * TODO: checar se o usuário está logado (SESSION = on)



          if (isset($_POST['login']) && isset($_POST['passwd'])) {
          if ((new Model\User\User())->login($_POST['login'], $_POST['passwd']))
          go('admin/page');
          else
          return (new View('admin/login'))->assign('info', 'Digite um Login e Senha válidos!')->render(false);
          }
          return (new View('admin/login'))->render(false);
         */


        $apacheSites = '/etc/apache2/sites-available/default';
        $apacheRestart = 'service apache2 restart';

        $username = 'loren';
        $userdir = $username;
        $userAddCmd = 'useradd -G webDev -d /var/www/' . $userdir . ' ' . $username;
        $userPassCmd = 'passwd ' . $username;

//chown -R loren:webDev /var/www/.$userdir
//chmod -R 0777 /var/www/.$userdir



        $lines = file(ROOT . 'log/access.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $temp = $this->parseLogLine($line);
            echo '<pre>' . print_r($this->LogTimeToMySQL($temp[3]), true) . '</pre>';
            $a[] = $temp;
        }

        exit('<h3>Dados</h3><pre>' . print_r($a, true) . '</pre>');
    }

    function parseLogLine($line) {
        // returns array with pieces
        $retarr = null;
        $retidx = 0;

        $startedwith = "";
        $tmppart = "";

        $parts = explode(' ', str_replace("\n", "", str_replace("\r", "", $line))); // explode the line into spaces
        for ($i = 0; $i < sizeof($parts); $i++) {
            $startchar = substr($parts[$i], 0, 1);
            $stopchar = substr($parts[$i], -1, 1);

            if ((strlen($tmppart) == 0) && ((strcmp($startchar, "\"") == 0) || (strcmp($startchar, "[") == 0))) {
                // if we're not looking for an end of quote and we found a quote or bracket
                if (strcmp($startchar, "\"") == 0)
                    $startedwith = "\"";
                else
                    $startedwith = "[";

                if (((strcmp($startedwith, "[") == 0) && (strcmp($stopchar, "]") == 0)) || ((strcmp($startedwith, "\"") == 0) && (strcmp($stopchar, "\"") == 0))) {
                    // found end in same block
                    $retarr[sizeof($retarr)] = $parts[$i];
                } else {
                    $tmppart = $parts[$i]; // save this part
                }
            } else if (strlen($tmppart) != 0) {
                // we're looking for an end of quote, but even if this has one, we need to add it
                $tmppart = $tmppart . " " . $parts[$i];

                if (((strcmp($startedwith, "[") == 0) && (strcmp($stopchar, "]") == 0)) || ((strcmp($startedwith, "\"") == 0) && (strcmp($stopchar, "\"") == 0))) {
                    // found our end
                    $retarr[sizeof($retarr)] = $tmppart; // add the full string
                    $tmppart = "";    // clear out the buffer
                    $startedwith = "";
                }
            } else {
                // should just add this part on
                $retarr[sizeof($retarr)] = $parts[$i];  // add this chunk
            }
        }
        return $retarr;
    }

    function convertLogTime($logTime) {
        //       1  4   8    13 16 19 22
        // turn [dd/Mon/yyyy:hr:mm:ss zone] into "Mon dd yyyy hr:min:ss zone" for parsing
        $timeStr = substr($logTime, 4, 3) . " " . substr($logTime, 1, 2) . " " . substr($logTime, 8, 4) . " " . substr($logTime, 13, 14);
        return strtotime($timeStr);
    }

    function convertDateToMySQL($date) {
        return date("Y-m-d G:i:s", $date);
    }

    function LogTimeToMySQL($logTime) {
        return $this->convertDateToMySQL($this->convertLogTime($logTime));
    }

}

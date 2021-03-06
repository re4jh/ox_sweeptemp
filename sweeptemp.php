<?
/*	OXID SweepTemp
	Author: jonas.hess@revier.de
	Licence: GPLv3
*/

$tmp_folder  = 'tmp';
$tmp_garbage = Array();
$tmp_smarty  = Array();

//alowed_ips
$allowed_ips = array(
    '111.222.333.444',
    '555.666.777.888',
    '999.111.222.333'
);

//Function to determine ip
Class ServerInfo
{
    public $servervars;

    public function __construct($servervars)
    {
        $this->servervars = $servervars;
    }

    function getClientIP()
    {
        $ipaddress = 'UNKNOWN'; // Set the ipaddress to unknown
        if ($this->servervars['HTTP_CLIENT_IP']) // Start capturing his ip
        {
            $ipaddress = $this->servervars['HTTP_CLIENT_IP'];
        } elseif ($this->servervars['HTTP_X_FORWARDED_FOR']) {
            $ipaddress = $this->servervars['HTTP_X_FORWARDED_FOR'];
        } elseif ($this->servervars['HTTP_X_FORWARDED']) {
            $ipaddress = $this->servervars['HTTP_X_FORWARDED'];
        } elseif ($this->servervars['HTTP_FORWARDED_FOR']) {
            $ipaddress = $this->servervars['HTTP_FORWARDED_FOR'];
        } elseif ($this->servervars['HTTP_FORWARDED']) {
            $ipaddress = $this->servervars['HTTP_FORWARDED'];
        } elseif ($this->servervars['REMOTE_ADDR']) {
            $ipaddress = $this->servervars['REMOTE_ADDR'];
        }
        return $ipaddress;
    }
}

$myServerInfo = new ServerInfo($_SERVER);


if (!in_array($myServerInfo->getClientIP(), $allowed_ips)) {
    exit('Your IP is not allowed to sweep!');
}

//Define the Files to sweep by Regex
$tmp_garbage[] = '^[0-9a-z\^\%A-Z_]*oxforgotpw[0-9a-z\^\%A-Z_]*\.php$';
$tmp_garbage[] = '^[0-9a-z\^%A-Z_]*oxcontent[0-9]*oxbaseshop\.php$';
$tmp_garbage[] = '^[0-9a-z\^\%A-Z_]*\.tpl\.php$';
$tmp_garbage[] = '^[0-9a-z\^\%A-Z_]*sort[0-9a-z\^\%A-Z_]*\.tpl\.php$';
$tmp_garbage[] = '^[0-9a-z\^\%A-Z_]*basket[0-9a-z\^\%A-Z_]*\.tpl\.php$';
$tmp_garbage[] = '^[0-9a-z\^%A-Z_]*\_i18n\.txt$';
$tmp_garbage[] = '^[0-9a-z\^%A-Z_]*\_allfields\_1\.txt$';
$tmp_garbage[] = '^oxpec\_[0-9a-z\^%A-Z_]*seo\.txt$';
$tmp_garbage[] = '^oxpec\_[0-9a-z\^%A-Z_]*allviews\.txt$';
$tmp_garbage[] = '^oxpec\_[0-9a-z\^%A-Z_]*\_allfields\_\.txt$';
$tmp_garbage[] = '^oxpec\_langcache\_[0-9a-z\^%A-Z_]*\_default\.txt$';
$tmp_garbage[] = '^oxpec\_[0-9a-z\^%A-Z_]*Cache\.txt$';
$tmp_garbage[] = '^oxpec\_menu\_[0-9a-z\^%A-Z_]*\_xml\.txt$';
$tmp_garbage[] = '^oxpec\_oxuser\_[0-9a-z\^%A-Z_]*\.txt$';
$tmp_garbage[] = '^oxpec\_oxshops\_[0-9a-z\^%A-Z_]*\.txt$';

//Scan folder and delete

$files = scandir($tmp_folder . '/');
$count = 0;
foreach ($tmp_garbage as $regex) {
    foreach ($files as $file) {
        if (preg_match('/' . $regex . '/', $file) && file_exists($tmp_folder . '/' . $file)) {
            unlink($tmp_folder . '/' . $file);
            $count++;
        }
    }
}

// Sweep Smarty-TMP

//Define the Files to sweep by Regex
$smartyfiles = scandir($tmp_folder . '/smarty/');
$smartycount = 0;


foreach ($smartyfiles as $file) {
    if ($file != '.' && $file != '..') {
        unlink($tmp_folder . '/smarty/' . $file);
        $smartycount++;
    }
}

echo $count . ' Temp-File(s) deleted!';
echo ' // ' . $smartycount . ' Smarty-Tempfile(s) deleted!';

?>

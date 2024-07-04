<?php


namespace App\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;

class FileService
{
    public function checkFile($file)
    {
        $codeStatut="ERROR";
        $requiredHeaders=array();
        $fileError = $file['error'];
        $fileTmpLoc = $file['tmp_name'];
        $extensions_valides = array('csv');
        $fileName = $file['name'];
        $extension_upload = strtolower(substr(strrchr($fileName, '.'), 1));
        $search = array("à","é","è","\""," ");
        $replace = array("a","e","e","","_");
        $n = $fileName . date_format(new \DateTime("now"), "Y-m-dH:i:s");
        $nom = sha1($n);
        if ($fileError > 0)
        {
            $codeStatut= "ERROR_TRANSFERT_FILE";
        }
        else
        {
            $codeStatut="OK";
            if (in_array($extension_upload, $extensions_valides))
            {
                if (($handle = fopen($fileTmpLoc, "r+")) !== FALSE)
                {
                    while (($data = fgetcsv($handle, 1000000, ";")) !== FALSE)
                    {
                        break;
                    }
                    $data=array_map("trim",$data);
                    $data=array_map("utf8_encode",$data);
                    $data=str_replace($search, $replace, $data);
                    $pattern = '/[^a-zA-Z0-9\s_]/';
                    $data[0] = preg_replace($pattern, '', $data[0]);
                    rewind($handle);
                    fputcsv($handle, $data,";");

                    if(count(array_intersect($requiredHeaders, $data))==count($requiredHeaders))
                    {
                        $codeStatut="OK";
                    }
                    else
                    {
                        $codeStatut = "ERROR_EXTENSION";
                    }
                    fclose($handle);
                }
            }
            else
            {
                $codeStatut = "ERROR_EXTENSION";
            }
        }
        return ["codeStatut"=>$codeStatut , "fileTmpLoc"=>$fileTmpLoc , "extension_upload"=>$extension_upload , "nom"=>$nom];
    }
    public function convert($filename, $delimiter = ';')
    {
        if(!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }
        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                if(!$header) {
                    $header = $row;
                    $header = array_map('trim',$header);
                    $header = array_map("utf8_encode", str_replace(" ","_",$header));
                } else {
                    $row = array_map("utf8_encode", $row);
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        return $data;
    }
    public function convert2($filename, $delimiter = ';')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }
        
        $header = NULL;
        $data = array();
        $count = 0;
        
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                $count++;
                
                if ($count == 1) {
                    $header = $row;
                    $header = array_map('trim', $header);
                    $header = array_map("utf8_encode", str_replace(" ", "_", $header));
                    continue;
                }

                // Ensure both header and row have the same number of elements
                if (count($header) == count($row)) {
                    $row = array_map("utf8_encode", $row);
                    $data[] = array_combine($header, $row);
                } else {
                    // Handle rows with a different number of columns (log, skip, or pad)
                    // Here we skip such rows
                    // You can log or handle these rows differently if needed
                    continue;
                }
            }
            fclose($handle);
        }
        
        return $data;
    }

}
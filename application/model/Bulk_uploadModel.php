<?php

/**
 * Class RegistrationModel
 *
 * Everything registration-related happens here.
 */
class Bulk_uploadModel
{
    public static function csvFileUpload()
    {
        try {
            $return = true;
            $path = $_FILES['fileToUpload']['name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            // check if file uploaded
            if (!$path) {
                Session::add('feedback_negative', Text::get('FILE_NOT_LOADED'));
                $return = false;
            }


            // check if file have csv extension
            if ($ext != "csv") {
                Session::add('feedback_negative', Text::get('NOT_CSV_FILE'));
                $return = false;
            }

            if (!$return) return false;

            $csv = array_map('str_getcsv', file($_FILES["fileToUpload"]["tmp_name"]));

            if (count($csv) && count($csv) > 1)
            {
                if (! (isset($csv[0][0]) && $csv[0][0] == "name"))
                {
                    Session::add('feedback_negative', Text::get('WRONG_CSV_FILE_STRUCTURE'));
                    return false;
                }
                if (! (isset($csv[0][1]) && $csv[0][1] == "value"))
                {
                    Session::add('feedback_negative', Text::get('WRONG_CSV_FILE_STRUCTURE'));
                    return false;
                }
                foreach ($csv as $index=>$row)
                {
                    if ($index == 0) continue;

                    if (count($row) != 2 ) {
                        Session::add('feedback_negative', Text::get('WRONG_CSV_FILE_STRUCTURE'));
                        return false;
                    }
                }
            }
            else
            {
                Session::add('feedback_negative', Text::get('WRONG_CSV_FILE_STRUCTURE'));
                return false;
            }

            array_shift ($csv);

            if (! self::writeNewCsvToDatabase($csv) )
            {
                Session::add('feedback_negative', Text::get('SOMETHING_BAD'));
                return false;
            }

        } catch (Exception $e) {
            Session::add('feedback_negative', Text::get('SOMETHING_BAD'));
            return false;
        }

        return true;

    }

    public static function placeholders($text, $count=0, $separator=","){
        $result = array();

        if($count > 0){

            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

    public static function writeNewCsvToDatabase($rows)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $database->beginTransaction();


        try {

            $userId = Session::get('user_id');

            $question_marks = [];
            $insert_values = [];

            foreach($rows as $d){

                array_unshift($d, $userId);
                $question_marks[] = '('  . self::placeholders('?', sizeof($d)) . ')';
                $insert_values = array_merge($insert_values, array_values($d));
            }

            $sql = "INSERT INTO csv_data (user_id , name, value) VALUES " .
                implode(',', $question_marks);

            $query = $database->prepare($sql);
            $query->execute($insert_values);
        } catch (PDOException $e){
            Session::add('feedback_negative', Text::get('SOMETHING_BAD'));
            return false;
        }
        $database->commit();

        return true;

    }

    public static function getUploads() {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT id , user_id , name, value FROM csv_data";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));

        $uploads = $query->fetchAll();

        if ($uploads == 0) {
            Session::add('feedback_negative', Text::get('NO_UPLOADS'));
        }

        return $uploads;
    }
}

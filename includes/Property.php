<?php

class Property extends DB
{
    public function select()
    {
        $sql = "SELECT * FROM properties ORDER BY id DESC;";

        $result = $this->connect()->query($sql);

        if ($result->rowCount()) {

            while ($row = $result->fetch()) {

                $data[] = $row;

            }

            return $data;
        }
    }
}
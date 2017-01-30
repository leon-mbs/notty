<?php

namespace App\Entity;

use \ZCL\DB\Entity;

/**
 * Entity service
 * 
 * @table=services
 * @keyfield=service_id
 */
class VendorService extends Entity
{

    const SERVICE_VENUE = 1;
    const SERVICE_MUSIC = 2;
    const SERVICE_TRAN = 3;
    const SERVICE_STAFF = 4;
    const SERVICE_FOOD = 5;
    const SERVICE_SPEAKER = 6;
    const SERVICE_PHOTO = 7;
    const SERVICE_DECOR = 8;

    protected function init() {
        $this->servicetype = "";
    }

    public static function getTypeList() {


        return array(self::SERVICE_VENUE => "Venue",
            self::SERVICE_MUSIC => "Music",
            self::SERVICE_TRAN => "Transport",
            self::SERVICE_STAFF => "Event Staff",
            self::SERVICE_FOOD => "Food & Beverages ",
            self::SERVICE_SPEAKER => "Speakers, animators",
            self::SERVICE_PHOTO => "Photo, video",
            self::SERVICE_DECOR => "Decoration, special effects"
        );
    }

    public static function getVenueTypes() {
        return array(
            1 => "House", 2 => "Apartment ", 3 => "Pub", 4 => "Open area"
        );
    }

    public static function getMusicTypes() {
        return array(
            1 => "DJ", 2 => "Karaoke", 3 => "Performer"
        );
    }

    public static function getGenres() {
        return array(
            1 => "Pop", 2 => "House", 3 => "Disco"
        );
    }

    public static function getTransportTypes() {
        return array(
            1 => "Limousine", 2 => "Party Bus"
        );
    }

    public static function getStaffTypes() {
        return array(
            1 => "Waiter", 2 => "Cook", 3 => "Guard"
        );
    }

    protected function beforeSave() {
        parent::beforeSave();



        $this->details = "<detail>";

        $this->details .= "<subtype>{$this->subtype}</subtype>";
        $this->details .= "<typename>{$this->typename}</typename>";
        $this->details .= "<subtypename>{$this->subtypename}</subtypename>";
        $this->details .= "<desc><![CDATA[{$this->desc}]]></desc>";
        $this->details .= "<capacity>{$this->capacity}</capacity>";
        $this->details .= "<catering>{$this->catering}</catering>";
        $this->details .= "<cost>{$this->cost}</cost>";
        $this->details .= "<costmin>{$this->costmin}</costmin>";
        $this->details .= "<costdel>{$this->costdel}</costdel>";
        $this->details .= "<image>{$this->image}</image>";
        $this->details .= "<latitude>{$this->latitude}</latitude>";
        $this->details .= "<longitude>{$this->longitude}</longitude>";
        $this->details .= "<address>{$this->address}</address>";
        $this->details .= "<genres>";
        foreach ($this->genre as $id) {
            $this->details .= "<genre>{$id}</genre>";
        }
        $this->details .= "</genres>";

        $this->details .= "</detail>";

        return true;
    }

    protected function afterLoad() {



        if (strlen($this->details) > 0) {

            $xml = simplexml_load_string($this->details);

            $this->subtype = (int) ($xml->subtype[0]);
            $this->desc = (string) ($xml->desc[0]);
            $this->typename = (string) ($xml->typename[0]);
            $this->subtypename = (string) ($xml->subtypename[0]);
            $this->capacity = (int) ($xml->capacity[0]);
            $this->catering = (int) ($xml->catering[0]);
            $this->cost = (int) ($xml->cost[0]);
            $this->costmin = (int) ($xml->costmin[0]);
            $this->costdel = (int) ($xml->costdel[0]);
            $this->image = (int) ($xml->image[0]);
            $this->address = (string) ($xml->address[0]);
            $this->longitude = (string) ($xml->longitude[0]);
            $this->latitude = (string) ($xml->latitude[0]);
            $arr = array();
            foreach ($xml->genres->children() as $child) {
                $id = (int) ($child[0]);
                $arr[] = $id;
            }
            $this->genre = $arr;
        }

        parent::afterLoad();
    }

}

<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 29/10/2016
 * Time: 18:45
 */
class APICall
{
    private $_d;
    private $_db = null;
    private $opt = array();

    //Constructs the Check Class. Used to check that a form can be submitted. Is correct.
    public function __construct() {
        $this->_db = DB::getInstance();
        $this->_d = new DateTime();
        $this->_d->setTimezone( new DateTimeZone("Europe/London"));
        array_push($this->opt, 2002, 2006, 6000, 1621, 1402, 1800, 2201, 1412, 2306, 2510);
    }

    public function printOption($option){
       echo 'channel name: ' . $option->getChannelTitle() . '<br/>';
       echo 'program name: ' . $option->getShowTitle() . '<br/>';
       echo 'program desc: ' . $option->getDesc() . '<br/>';
       echo 'channel number: ' . $option->getChannelNumber() . '<br/>';
    }
    
    public function addToDB($option)
    {
        $newShowTitle = $this->_db->getPDO()->quote($option->getShowTitle());
        $newShortDest = $this->_db->getPDO()->quote($option->getDesc());
        //echo "INSERT INTO programs (name, short_desc, channel_id) VALUES (" . $newShowTitle . "," .$newShortDest. ",'" .$option->getChannel()."')";
        $this->_db->insertSQL("INSERT INTO programs (name, short_desc, channel_id) VALUES (" . $newShowTitle . "," . $newShortDest . ",'" . $option->getChannel() . "')");
    }

    public function addProgramToDB($channelid){
        ini_set("allow_url_fopen", 1);
        $this->_db->query("SELECT code FROM channels WHERE id=".$channelid);
        $channelCode = $this->_db->getFirst()->code;
        echo "SELECT code FROM channels WHERE id=".$channelid;
        //var_dump($channelCode);
            //echo "http://epgservices.sky.com/tvlistings-proxy/TVListingsProxy/tvlistings.json?channels=".$channelCode."&time=". $this->_d->format('Ymd')."0000&dur=10&detail=2&siteId=1";
        $json = file_get_contents("http://epgservices.sky.com/tvlistings-proxy/TVListingsProxy/tvlistings.json?channels=".$channelCode."&time=". $this->_d->format('Ymd')."0000&dur=10&detail=2&siteId=1");
        $obj = json_decode($json);
        //echo '<pre>';
        //var_dump($obj->channels);
        //echo '</pre>';
        //$this->_db->query("SELECT id FROM channels WHERE code=".$channelCode);
        //$res = $this->_db->getFirst()->id;
        if(is_object($obj->channels->program))
        {
            $option = new Program($obj->channels->program->title, null, $obj->channels->program->shortDesc, $channelid );

        } else {
            $option = new Program($obj->channels->program[0]->title, null, $obj->channels->program[0]->shortDesc, $channelid);
        }
        $this->addToDB($option);
        return $option;
    }


    public function initialiseArray(){
        
    }
}

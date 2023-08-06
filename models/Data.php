<?php
require_once('models/Db.php');

/**
 * Model per la gestione degli utenti e le operazioni CRUD
 */

class Data extends Db
{
    public $apikey;
    public $data;

    public function __construct()
    {
        $ini = parse_ini_file('./config.ini');
        $this->apikey = $ini['apy_key'];
        parent::__construct();
    }

    //if in the db there is not the id, or the created_at is older than 15 min, get the data from youtube and save it in the db
    public function get($id)
    {
        $data = $this->getFromDb($id);

        //if there is no data, get it from youtube, else if the data is older than 15 min, update it
        if (!$data) {
            $data = $this->getVideoList($id);
            $this->save($id, $data);
        } else {
            $created_at = $data['created_at'];
            $now = date('Y-m-d H:i:s');
            $diff = strtotime($now) - strtotime($created_at);
            if ($diff > 900) {
                $data = $this->getVideoList($id);
                $this->update($id, $data);
            } else {
                $data = json_decode($data['data'], true);
            }
        }
        return $data;
    }

    private function getFromDb($id)
    {
        $sql = "SELECT * FROM data WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    private function update($id,$data){ 
        $data_json = json_encode($data);
        $now =  date('Y-m-d H:i:s');
        $sql = "UPDATE data SET data = :data, created_at = :created_at WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->bindParam(':data', $data_json, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $now, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function delete($id){
        $sql = "DELETE FROM data WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function save($id,$data){
        $data_json = json_encode($data);
        $now =  date('Y-m-d H:i:s');
        $sql = "INSERT INTO data (id, data, created_at) VALUES (:id, :data, :created_at)";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->bindParam(':data', $data_json, PDO::PARAM_STR);
        $stmt->bindParam(':created_at', $now, PDO::PARAM_STR);
        $stmt->execute();
    }

    //Recupero la playlist e tolgo i video privati e non listati
    private function getPlaylistFromYoutube($id,$nextPageToken = null){
        if($nextPageToken){
            $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet%2C%20status&maxResults=50&playlistId=".$id."&pageToken=".$nextPageToken."&key=".$this->apikey;
        }else{
            $url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet%2C%20status&maxResults=50&playlistId=".$id."&key=".$this->apikey;
        }
        //remove all the item with the status private
        $data = json_decode(file_get_contents($url),true);
        $items = $data['items'];
        foreach($items as $key => $item){
            if($item['status']['privacyStatus'] == 'private' || $item['status']['privacyStatus'] == 'unlisted'){
                unset($items[$key]);
            }
        }
        $data['items'] = $items;
        return $data;
    }

    /**
     * get the playlist from youtube, if there is a nextPageToken, get the next page
     */
    private function getVideoList($id){
        $data = $this->getPlaylistFromYoutube($id);
        $nextPageToken = isset($data['nextPageToken']) ? $data['nextPageToken'] : null;
        while($nextPageToken){
            $data2 = $this->getPlaylistFromYoutube($id,$nextPageToken);
            $nextPageToken = isset($data2['nextPageToken']) ? $data2['nextPageToken'] : null;
            $data['items'] = array_merge($data['items'],$data2['items']);
        }
        return $data;
    }

}
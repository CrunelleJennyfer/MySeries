<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serie extends CI_Model {

  function __construct()
  {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('my_queries');
      $this->load->model('user');
  }

  public function get_all($limit,$last){
    $query = $this->my_queries->query('get_all_series', ['limit' => $limit, 'lastVisit' => $last]);
    return $query->result();
  }


  public function watched($vu, $idEpisode){
    $query = $this->my_queries->query($vu ? 'watched' : 'unwatched',
     ['idUser'=>$_SESSION['userId'],  'idEpisode' => $idEpisode]);
  }


  public function get_followed(){
    $query = $this->my_queries->query('get_followed_series', $_SESSION);
    $series = $query->result_array();
    $result = [];
    for($i=0;$i<count($series);$i++){
      $result[$series[$i]['id']] = $series[$i];
    }

    $query = $this->my_queries->query('get_next_episode_user', $_SESSION);
    $episodes = $query->result_array();
    for($i=0;$i<count($episodes);$i++){
      $result[$episodes[$i]['idSerie']]['episode'][] = $episodes[$i];
    }
    return $result;
  }


  public function get($id){
    $query = $this->my_queries->query('get_serie', ['id' => $id]);
    $result = $query->first_row();
    if ($this->user->is_logged()){
      $query = $this->my_queries->query('isFollowing',
        ['idUser'=>$this->session->userId ,'idSerie' => $id]);
      $result->follow = $query->num_rows()>0;
    }
    return $result;
  }

  public function get_cast($id){
    $query = $this->my_queries->query('get_cast', ['id' => $id]);
    return $query->result();
  }

  public function get_season_list($id){
    $query = $this->my_queries->query('get_season_list', ['id' => $id]);
    return $query->result();
  }

  public function get_episode_list($id,$saison){
    $data = ['id' => $id, 'saison' => $saison];
    if ($this->user->is_logged()){
      $data['userId'] = $_SESSION['userId'];
      $name ='get_episode_list_vu';
    } else {
      $name ='get_episode_list';
    }
    $query = $this->my_queries->query($name, $data);
    return $query->result();
  }

  public function get_next_episode($id){
    $query = $this->my_queries->query('get_next_episode', ['id' => $id]);
    return $query->first_row();
  }

  public function get_crew_list($id){
    $query = $this->my_queries->query('get_crew_list', ['id' => $id]);
    return $query->result();
  }

}

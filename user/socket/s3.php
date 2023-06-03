#!/usr/bin/php
<?php
// 채팅 서버로 잘 동작하네요. 몇 개 문제를 제외하면...
// 실행: /usr/bin/php ./server3.php
//-----------------------
class Select{
      var $fhs;
       
       //-----------------------
       function Select(&$fh){
               $this->fhs = array($fh);
       }

       //-----------------------
       function add(&$fh){
               array_push($this->fhs, $fh);
       }

       //-----------------------
       function remove(&$fh){
               for($i = 0; $i < count($this->fhs); $i++){
                       if($this->fhs[$i] == $fh){
                               array_splice($this->fhs, $i, 1);
                               break;
                       }
               }
       }

       function can_read($limit = 5){
        $read_fhs = $this->fhs;
        $write_fhs = array();
        $exception_fhs = array();
        socket_select($read_fhs, $write_fhs, $exception_fhs, $limit);
        return $read_fhs;
}

       //-----------------------
       function can_write($limit = 0){
               $write_fhs = $this->fhs;
               socket_select($read_fhs = null, $write_fhs, $exception_fhs = null, $limit);
               return $write_fhs;
       }
}

//-----------------------
class Guest{
       var $fh;
       var $name;

       function Guest($fh, $name){
               $this->fh = $fh;
               $this->name = $name;
       }
}

//-----------------------
class Lobby{
       var $fh;
       var $sel;
       var $guests;

       //-----------------------
       function Lobby(){
               $this->guests = array();
       }
       
       //-----------------------
       function open($addr, $port){
               $this->fh = socket_create(AF_INET, SOCK_STREAM, 0)
                       or die("create error!!n");
               socket_setopt($this->fh, SOL_SOCKET, SO_REUSEADDR, 1)
                       or die("setopt error!!n");
               socket_bind($this->fh, $addr, $port) or die("bind error!!n");
               socket_listen($this->fh, 5) or die("listen error!!n");

               $this->sel = new Select($this->fh);

               print "Wating...\n";
       }

       //-----------------------
       function close($g){
               socket_close($this->fh);
       }

       //-----------------------
       function work(){
               while(true){
                       foreach($this->sel->can_read() as $fh){
                               if($fh == $this->fh){
                                       print "Got connection.\n";
                                       $new = socket_accept($this->fh)
                                               or die("accept error!!n");
                                       $this->sel->add($new);
                                       socket_write($new, "What is your nick name? Enter the name:");
                               }
                               else{
                                       $buf = socket_read($fh, 1024);
                                       if($buf){
                                               printf("Received: %s\n", $buf);
                                               $g = $this->get_guest($fh);
                                               if(! $g){
                                                       $this->add_guest(new Guest($fh, $buf));
                                                       continue;
                                               }

                                               $this->talk2all(sprintf("%s: %s\n"
                                                       , $g->name, $buf));
                                       }
                                       else{
                                               if($this->is_guest($fh))
                                                       {$this->del_guest($fh);}
                                               $this->sel->remove($fh);
                                               socket_close($fh);
                                               print "A client has been removed.\n";
                                       }
                               }
                       }
               }
       }

       //-----------------------
       function talk2all($buf){
               foreach($this->guests as $g){
                       socket_write($g->fh, $buf);
               }
       }

       //-----------------------
       function get_guest(&$fh){
               for($i = 0; $i < count($this->guests); $i++){
                       if($fh == $this->guests[$i]->fh){return $this->guests[$i];}
               }
               return null;
       }

       //-----------------------
       function is_guest(&$fh){
               for($i = 0; $i < count($this->guests); $i++){
                       if($fh == $this->guests[$i]->fh){return true;}
               }
               return false;
       }

       //-----------------------
       function add_guest(&$g){
               array_push($this->guests, $g);
               $this->talk2all("## " . $g->name . " has entered. ##\n");
               printf("count($this->guests) = %d\n", count($this->guests));
       }

       //-----------------------
       function del_guest(&$fh){
               for($i = 0; $i < count($this->guests); $i++){
                       if($fh == $this->guests[$i]->fh){array_splice($this->guests, $i);}
               }
               printf("count($this->guests) = %d\n", count($this->guests));
       }
}

//-----------------------
//main
set_time_limit(0);

$lob = new Lobby();
$lob->open("192.168.0.2", 20480);
$lob->work();
?>

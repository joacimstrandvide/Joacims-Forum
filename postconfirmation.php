
 <?php 
 header('Content-type: text/html');
 
 $html = file_get_contents("index.html");
 $html_pieces = explode("<!-- ==xxx== -->", $html);
 echo $html_pieces[0]; 
 
 if (count($_POST) > 0) {
     
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "users";
     
     $conn = new mysqli($servername, $username, $password, $dbname);
    
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     }
     else {
        $email = $_POST["email"];
        $userpass = $_POST["userpass"];
        $presentation = $_POST["presentation"];
        $topicid = $_POST["topicid"];
        $text = htmlspecialchars($_POST["content"], ENT_QUOTES);
        $text = str_replace('<', '&lt;', $text);
        $text = str_replace('>', '&gt;', $text);
        $text = str_replace('å', '&aring;', $text);
        $text = str_replace('ä', '&auml;', $text);
        $text = str_replace('ö', '&ouml;', $text);
        $text = str_replace('Å', '&Aring;', $text);
        $text = str_replace('Ä', '&Auml;', $text);
        $text = str_replace('Ö', '&Ouml;', $text);
        $updates = $_POST["updates"];
        $nbrposts = $_POST["nbrposts"];
        
        echo "Inloggad som <a class='welcome' href=\"" . $email . "\" title=\"" . $presentation . "\" >" . $email . "</a><br>";
        echo "<form action='index.php' method='post'>";
        echo "<input type='hidden' name='email' value='$email'>";
        echo "<input type='hidden' name='pass' value='$userpass'>";
        echo "<br />";
        echo "<br />";
        echo "<input type='submit' class='result_button' name='submit' value='Tillbaka till startsidan'>";
        echo "</form>";
        
        $stmt = $conn->prepare("INSERT INTO posts (id, topicid, time, user, text) VALUES (?, ?, now(), ?, ?)");
        $stmt->bind_param("iiss", $nbrposts, $topicid, $email, $text);
        $stmt->execute();
        $stmt->close();
        echo "Inlägget har sparats.<br>";
        
        if ($updates == 1) {
            $originator = $_POST["originator"];
            $header = $_POST["header"];
            
            //sätt tidszon
            date_default_timezone_set('Europe/Stockholm');
            require 'PHPMailerAutoload.php';
            //Skapa ny PHPMailer-instans
            $mail = new PHPMailer;
            //använd SMTP
            $mail->isSMTP();
            //aktivera SMTP-felsökning för klient- och servermeddelanden
            $mail->SMTPDebug = 2;
            //HTML-vänliga debugmeddelanden
            $mail->Debugoutput = 'html';
            //använd gmail som host
            $mail->Host = 'smtp.gmail.com';
            //SMTP-portnummer = 587 för TLS
            $mail->Port = 587;
            //krypteringssystem=tls
            $mail->SMTPSecure = 'tls';
            //Använd SMTP-autentisering
            $mail->SMTPAuth = true;
            //användarnamn
            $mail->Username = "xxxxxxxxxxx"; // OBS: ersätt med rätt gmailkonto
            $mail->Password = $mailpass;
            $mail->setFrom('xxxxxxxxxxx', $email);
            $mail->addReplyTo('xxxxxxxxxxx', $email);
            $mail->addAddress($originator);
            $mail->Subject = "Nytt inlägg i tråden '" . $header . "'";
            $msg = $text;
            $mail->Body = $msg;
            //skicka meddelandet
            if (!$mail->send()) {
                echo "Felmeddelande: " . $mail->ErrorInfo;
            } else {
                echo "Meddelande skickat!";
            }
        }
        
        $closed = mysqli_close($conn);
        if ($closed) {
            //echo "Databasuppkopplingen stängd";
        }
        else {
            echo "Lyckades inte stänga databasuppkopplingen";
        }
     }


 }

 ?> 
 
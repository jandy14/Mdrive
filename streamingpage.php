<!DOCTYPE html>
<html lang="kr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>M Drive</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/main_page.css" rel="stylesheet">
    <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css'>
    <style type="text/css">
    <?php
        require_once("./class/DBManager.php");
        if(!isset($_COOKIE['userEmail']))
            header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/index.html');
        if(!isset($_GET['num']))
            header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/mainpage.php');
        $db_manager = new DB_Manager();
        $stmt = $db_manager->pdo->prepare("SELECT user_num FROM User WHERE email = ?");
        $stmt->execute(array($_COOKIE['userEmail']));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $db_manager->pdo->prepare("SELECT name,caption_num FROM Video WHERE owner_num = ? and video_num = ?");
        $stmt->execute(array($user["user_num"],$_GET["num"]));
        if($stmt->rowCount() != 1)
            header('Location: http://ec2-54-202-179-17.us-west-2.compute.amazonaws.com/MDrive/mainpage.php');
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>

    body {
        background-color: #000000;
        margin: 0px;
        /*hide scroll*/
    }
    
    video::-webkit-media-controls-fullscreen-button {
        display: none;
    }
    </style>
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <div class="user-head">>
                    <div class="user-name">
                        <h5><a href="#">재생중인 영상 제목</a></h5>
                        <span><a href="#">이 동영상을 위한 자막</a></span>
                    </div>
                </div>
                <div class="inbox-body">
                    <a href="upload_subtitle.html" data-target="#uploadForm" data-toggle="modal" title="Compose" class="btn btn-compose">
                              UPLOAD
                    </a>
                </div>
                <ul class="inbox-nav inbox-divider">
                    <li class="active">
                        <!-- 영상에 링킹되있는 자막들 출력 -->
                        <a href="#"><i class="fa fa-file-text"></i> 자막 제목</a>
                    </li>
                </ul>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->
        <div id="page-content-wrapper">
            <div class="row">
                <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
                <video id="vt" width="400" height="100" controls>
                    <?php
                        echo "<source src='./file/video/".$_COOKIE['userEmail']."/".$video['name']."' type='video/mp4'>";
                        $stmt = $db_manager->pdo->prepare("SELECT caption_num,name FROM Caption WHERE video_num = ? and owner_num = ?");
                        $stmt->execute(array($_GET['num'], $user['user_num']));
                        $count = $stmt->rowCount();
                        for($i = 0; $i < $count; ++$i)
                        {
                            $caption = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo "<track kind='subtitles' label='".$caption['name']."' src='./file/caption/".$_COOKIE['userEmail']."/".$caption['name']."' ";
                            if($caption['caption_num'] == $video['caption_num'])
                                echo "default";
                            echo ">";
                        }

                    ?>
                    what the?
                </video>
                <!-- Projects Row -->
            </div>
        </div>
        <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        </script>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#vt").attr({
                "width": $(window).width(),
                "height": $(window).height()
            });
        });
        $(window).resize(function() {
            $("#vt").attr({
                "width": $(window).width(),
                "height": $(window).height()
            });
        });
        </script>
        <div class="modal fade" id="uploadForm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </div>
</body>

</html>